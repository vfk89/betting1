<?php
declare(strict_types=1);

namespace App\DTO;

final readonly class EventDTO
{
    public ?int $id;
    public string $title;

    public function __construct(?int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }
}
