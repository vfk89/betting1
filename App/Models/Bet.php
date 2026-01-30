<?php
declare(strict_types=1);

namespace App\Models;

use App\DTO\BetDTO;

final class Bet
{
    public function __construct(
        private BetDTO $dto
    ) {}

    // ====== getters ======

    public function id(): ?int
    {
        return $this->dto->id;
    }

    public function userId(): int
    {
        return $this->dto->userId;
    }

    public function eventId(): int
    {
        return $this->dto->eventId;
    }

    public function outcome(): string
    {
        return $this->dto->outcome;
    }

    public function coefficient(): string
    {
        return $this->dto->coefficient;
    }

    public function amount(): string
    {
        return $this->dto->amount;
    }

    public function currency(): string
    {
        return $this->dto->currency;
    }

    public function status(): string
    {
        return $this->dto->status;
    }

    // ====== domain logic ======

    public function isPending(): bool
    {
        return $this->dto->status === BetDTO::STATUS_PENDING;
    }

    public function win(): void
    {
        if (!$this->isPending()) {
            throw new \DomainException('Bet already settled');
        }

        $this->dto = new BetDTO(
            $this->dto->id,
            $this->dto->userId,
            $this->dto->eventId,
            $this->dto->outcome,
            $this->dto->coefficient,
            $this->dto->amount,
            $this->dto->currency,
            BetDTO::STATUS_WON
        );
    }


//    public function win(): void
//    {
//        $this->dto = new BetDTO(
//            $this->dto->id,
//            $this->dto->userId,
//            $this->dto->eventId,
//            $this->dto->outcome,
//            $this->dto->coefficient,
//            $this->dto->amount,
//            $this->dto->currency,
//            BetDTO::STATUS_WON
//        );
//    }


    public function lose(): void
    {
        if (!$this->isPending()) {
            throw new \DomainException('Bet already settled');
        }

        $this->dto = new BetDTO(
            $this->dto->id,
            $this->dto->userId,
            $this->dto->eventId,
            $this->dto->outcome,
            $this->dto->coefficient,
            $this->dto->amount,
            $this->dto->currency,
            BetDTO::STATUS_LOST
        );
    }


//    public function lose(): void
//    {
//        $this->dto = new BetDTO(
//            $this->dto->id,
//            $this->dto->userId,
//            $this->dto->eventId,
//            $this->dto->outcome,
//            $this->dto->coefficient,
//            $this->dto->amount,
//            $this->dto->currency,
//            BetDTO::STATUS_LOST
//        );
//    }

    public function dto(): BetDTO
    {
        return $this->dto;
    }
}
