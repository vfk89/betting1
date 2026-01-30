<?php
declare(strict_types=1);

namespace App\DTO;

final class BalanceDTO
{
    public int $userId;
    public string $currency;
    public string $balance;

    public function __construct(int $userId, string $currency, string $balance)
    {
        $this->userId = $userId;
        $this->currency = $currency;
        $this->balance = $balance;
    }
}
