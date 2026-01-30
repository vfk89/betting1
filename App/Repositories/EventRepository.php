<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTO\EventDTO;
use App\Database\Connection;
use PDO;

final class EventRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getConnection();
    }

    public function create(EventDTO $dto): EventDTO
    {
        $stmt = $this->db->prepare(
            'INSERT INTO events (title) VALUES (:title)'
        );
        $stmt->execute(['title' => $dto->title]);

        return new EventDTO(
            id: (int)$this->db->lastInsertId(),
            title: $dto->title
        );
    }

    /**
     * @return EventDTO[]
     */
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM events');

        return array_map([$this, 'map'], $stmt->fetchAll());
    }

    public function findById(int $id): ?EventDTO
    {
        $stmt = $this->db->prepare('SELECT * FROM events WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row ? $this->map($row) : null;
    }

    private function map(array $row): EventDTO
    {
        return new EventDTO(
            id: (int)$row['id'],
            title: $row['title']
        );
    }
}
