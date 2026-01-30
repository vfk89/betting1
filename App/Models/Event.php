<?php
declare(strict_types=1);

namespace App\Models;

use App\DTO\EventDTO;

final readonly class Event
{
    public function __construct(
        private EventDTO $dto
    ) {}

    public function id(): ?int
    {
        return $this->dto->id;
    }

    public function title(): string
    {
        return $this->dto->title;
    }

    public function dto(): EventDTO
    {
        return $this->dto;
    }
}
