<?php
declare(strict_types=1);

namespace App\Models;

use App\DTO\ContactDTO;

final readonly class Contact
{
    public function __construct(
        private ContactDTO $dto
    ) {}

    public function type(): string
    {
        return $this->dto->type;
    }

    public function value(): string
    {
        return $this->dto->value;
    }

    public function dto(): ContactDTO
    {
        return $this->dto;
    }
}
