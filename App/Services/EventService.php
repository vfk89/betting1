<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\EventDTO;
use App\Models\Event;
use App\Repositories\EventRepository;

final readonly class EventService
{
    public function __construct(
        private EventRepository $events
    ) {}

    public function create(string $title): Event
    {
        $dto = new EventDTO(
            id: null,
            title: $title
        );

        $saved = $this->events->create($dto);

        return new Event($saved);
    }



    /**
     * @return Event[]
     */
    public function getAllEvents(): array
    {
        $dtos = $this->events->all();

        return array_map(
            fn (EventDTO $dto) => new Event($dto),
            $dtos
        );
    }

    public function getById(int $id): ?Event
    {
        $dto = $this->events->findById($id);

        return $dto ? new Event($dto) : null;
    }
}
