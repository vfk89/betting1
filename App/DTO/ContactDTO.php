<?php
declare(strict_types=1);

namespace App\DTO;

final class ContactDTO
{
    public int $userId;
    public string $type;
    public string $value;

    public function __construct(int $userId, string $type, string $value)
    {
        $this->userId = $userId;
        $this->type = $type;
        $this->value = $value;
    }
}
