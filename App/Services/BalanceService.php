<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\BalanceDTO;
use App\Models\Balance;
use App\Repositories\BalanceRepository;
use Throwable;

final readonly class BalanceService
{
    public function __construct(
        private BalanceRepository $balances
    ) {}

    public function get(int $userId, string $currency): Balance
    {
        $dto = $this->balances->getForUser($userId, $currency);
        return new Balance($dto);
    }

    public function getBalancesByUser(int $userId): array
    {
        return array_map(
            fn (BalanceDTO $dto) => new Balance($dto),
            $this->balances->findByUserId($userId)
        );
    }

    public function updateBalance(int $userId, string $currency, float $amount): void
    {
        if ($amount >= 0) {
            $this->increase($userId, $currency, (string)$amount);
        } else {
            $this->decrease($userId, $currency, (string)abs($amount));
        }
    }

    public function increase(int $userId, string $currency, string $amount): void
    {
        $balance = $this->get($userId, $currency);
        $balance->increase($amount);
        $this->balances->save($balance->dto());
    }

    public function decrease(int $userId, string $currency, string $amount): void
    {
        $balance = $this->get($userId, $currency);

        if (bccomp($balance->amount(), $amount, 2) < 0) {
            throw new \RuntimeException('Недостаточно средств');
        }

        $balance->decrease($amount);
        $this->balances->save($balance->dto());
    }
}
