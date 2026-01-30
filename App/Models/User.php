<?php
declare(strict_types=1);

namespace App\Models;

use App\DTO\UserDTO;

final readonly class User
{
    public function __construct(
        private UserDTO $dto
    ) {}

    public function id(): ?int
    {
        return $this->dto->id;
    }

    public function isActive(): bool
    {
        return $this->dto->status === 'active';
    }

    public function dto(): UserDTO
    {
        return $this->dto;
    }


}
