<?php
declare(strict_types=1);

namespace App\Services;

use App\Services\UserService;
use App\Services\BalanceService;
use App\Services\BetService;
use App\Services\EventService;
use RuntimeException;

final readonly class ApiService
{
    public function __construct(
        private UserService $users,
        private BalanceService $balances,
        private BetService $bets,
        private EventService $events
    ) {}

    /* ===== CLIENT ===== */

    public function listUsers(): array
    {
        return [
            'success' => true,
            'data' => array_map(fn($u) => [
                'id' => $u->id(),
                'name' => $u->dto()->name,
                'login' => $u->dto()->login,
            ], $this->users->getAllUsers())
        ];
    }

    public function dashboard(int $userId): array
    {
        return [
            'success' => true,
            'balances' => array_map(fn($b) => [
                'currency' => $b->currency(),
                'amount' => $b->amount(),
            ], $this->balances->getBalancesByUser($userId)),
            'events' => array_map(fn($e) => [
                'id' => $e->id(),
                'title' => $e->title(),
            ], $this->events->getAllEvents()),
            'bets' => array_map(fn($b) => [
                'id' => $b->id(),
                'eventId' => $b->eventId(),
                'outcome' => $b->outcome(),
                'amount' => $b->amount(),
                'currency' => $b->currency(),
                'status' => $b->status(),
            ], $this->bets->getBetsByUser($userId)),
        ];
    }

    public function placeBet(array $input): array
    {
        if (!isset($input['userId'], $input['eventId'], $input['outcome'], $input['coefficient'], $input['amount'], $input['currency'])) {
            throw new RuntimeException('Missing parameters for placing bet');
        }

        $bet = $this->bets->place(
            (int)$input['userId'],
            (int)$input['eventId'],
            $input['outcome'],
            (string)$input['coefficient'],
            (string)$input['amount'],
            $input['currency']
        );

        return ['success' => true, 'betId' => $bet->id()];
    }

    /* ===== ADMIN ===== */

    public function adminUpdateBalance(array $input): array
    {
        $userId = (int) ($input['userId'] ?? 0);
        $currency = (string) ($input['currency'] ?? '');
        $amount = (string) ($input['amount'] ?? '');

        if ($amount === '') {
            throw new RuntimeException('Amount required');
        }

        $current = $this->balances->get($userId, $currency);
        $diff = bcsub($amount, $current->amount(), 2);
        $this->balances->updateBalance($userId, $currency, (float)$diff);

        return ['success' => true, 'message' => 'Баланс пользователя обновлён'];
    }

    public function adminGetAllBets(): array
    {
        return ['success' => true, 'data' => $this->bets->getAll()];
    }

    public function adminSettleBet(array $input): array
    {
        if (!isset($input['betId'], $input['result'])) {
            throw new RuntimeException('Missing parameters for settling bet');
        }

        $this->bets->settleSingle((int)$input['betId'], $input['result']);
        return ['success' => true];
    }

    public function adminSettleEvent(array $input): array
    {
        if (!isset($input['eventId'], $input['outcome'])) {
            throw new RuntimeException('Missing parameters for settling event');
        }

        $this->bets->settleEvent((int)$input['eventId'], $input['outcome']);
        return ['success' => true];
    }
}
