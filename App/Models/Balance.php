<?php
declare(strict_types=1);

namespace App\Models;

use App\DTO\BalanceDTO;

final class Balance
{
    public function __construct(
        private BalanceDTO $dto
    ) {}

    public function currency(): string
    {
        return $this->dto->currency;
    }

    public function amount(): string
    {
        return $this->dto->balance;
    }

    public function increase(string $value): void
    {
        $this->dto = new BalanceDTO(
            $this->dto->userId,
            $this->dto->currency,
            bcadd($this->dto->balance, $value, 2)
        );
    }

    public function decrease(string $value): void
    {
        // Проверяем, хватает ли средств
        if (bccomp($this->dto->balance, $value, 2) < 0) {
            throw new \DomainException('Insufficient balance');
        }

        $this->dto = new BalanceDTO(
            $this->dto->userId,
            $this->dto->currency,
            bcsub($this->dto->balance, $value, 2)
        );
    }


//    public function decrease(string $value): void
//    {
//        $this->dto = new BalanceDTO(
//            $this->dto->userId,
//            $this->dto->currency,
//            bcsub($this->dto->balance, $value, 2)
//        );
//    }

    public function dto(): BalanceDTO
    {
        return $this->dto;
    }
}
