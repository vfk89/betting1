<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\BetDTO;
use App\Models\Bet;
use App\Repositories\BetRepository;
use App\Database\Connection;
use Throwable;

final readonly class BetService
{
    public function __construct(
        private BetRepository  $bets,
        private BalanceService $balances
    ) {}

    /**
     * Use-case: place bet
     * @throws Throwable
     */
    public function place(
        int $userId,
        int $eventId,
        string $outcome,
        string $coefficient,
        string $amount,
        string $currency
    ): Bet {
        $pdo = Connection::getConnection();

        if ($pdo->inTransaction()) {
            throw new \RuntimeException('Unexpected active transaction');
        }

        $pdo->beginTransaction();

        try {
            // 1. списываем баланс
            $this->balances->decrease($userId, $currency, $amount);

            // 2. создаём ставку
            $dto = new BetDTO(
                id: null,
                userId: $userId,
                eventId: $eventId,
                outcome: $outcome,
                coefficient: $coefficient,
                amount: $amount,
                currency: $currency,
                status: BetDTO::STATUS_PENDING
            );

            $saved = $this->bets->create($dto);

            $pdo->commit();

            return new Bet($saved);

        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function getBetsByUser(int $userId): array
    {
        return array_map(
            fn (BetDTO $dto) => new Bet($dto),
            $this->bets->getByUserId($userId)
        );
    }

    /**
     * @throws Throwable
     */
    public function settleWin(Bet $bet): void
    {
        // Расчет выигрыша
        $win = bcmul($bet->amount(), $bet->coefficient(), 2);

        // Увеличиваем баланс
        $this->balances->increase(
            $bet->userId(),
            $bet->currency(),
            $win
        );

        // Меняем статус ставки
        $this->bets->updateStatus($bet->id(), BetDTO::STATUS_WON);
    }

    /**
     * @throws Throwable
     */
    public function settleLose(Bet $bet): void
    {
        $this->bets->updateStatus($bet->id(), BetDTO::STATUS_LOST);
    }

    public function getAll(): array
    {
        return array_map(
            fn($dto) => [
                'id' => $dto->id,
                'userId' => $dto->userId,
                'eventId' => $dto->eventId,
                'outcome' => $dto->outcome,
                'amount' => $dto->amount,
                'currency' => $dto->currency,
                'status' => $dto->status,
            ],
            $this->bets->getAll()
        );
    }

    /**
     * Use-case: settle a single bet (including recalculation)
     * @throws Throwable
     */
    public function settleSingle(int $betId, string $result): void
    {
        $pdo = Connection::getConnection();
        $pdo->beginTransaction();

        try {
            $dto = $this->bets->findById($betId);
            if (!$dto) {
                throw new \RuntimeException('Bet not found');
            }

            $bet = new Bet($dto);
            $currentStatus = $bet->status();

            if ($currentStatus === $result) {
                // Ничего не делаем
                $pdo->commit();
                return;
            }

            // 🔁 WON → LOST (откат выигрыша)
            if ($currentStatus === BetDTO::STATUS_WON && $result === BetDTO::STATUS_LOST) {
                $this->rollbackWin($bet);
                $this->bets->updateStatus($betId, BetDTO::STATUS_LOST);
            }
            // 🔁 LOST → WON (начисление выигрыша)
            elseif ($currentStatus === BetDTO::STATUS_LOST && $result === BetDTO::STATUS_WON) {
                $this->settleWin($bet);
            }
            // 🟢 PENDING → *
            elseif ($currentStatus === BetDTO::STATUS_PENDING) {
                $result === BetDTO::STATUS_WON
                    ? $this->settleWin($bet)
                    : $this->settleLose($bet);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Use-case: settle all bets for an event (pending only)
     * @throws Throwable
     */
    public function settleEvent(int $eventId, string $outcome): void
    {
        $bets = $this->bets->getByEventId($eventId);

        foreach ($bets as $dto) {
            if ($dto->status !== BetDTO::STATUS_PENDING) continue;

            $bet = new Bet($dto);

            $bet->outcome() === $outcome
                ? $this->settleWin($bet)
                : $this->settleLose($bet);
        }
    }

    /**
     * Rollback previously credited win
     */
    private function rollbackWin(Bet $bet): void
    {
        $win = bcmul($bet->amount(), $bet->coefficient(), 2);

        $this->balances->decrease(
            $bet->userId(),
            $bet->currency(),
            $win
        );
    }
}
