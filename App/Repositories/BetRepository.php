<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTO\BetDTO;
use App\Database\Connection;
use PDO;

final class BetRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getConnection();
    }

    public function create(BetDTO $dto): BetDTO
    {
        $stmt = $this->db->prepare(
            'INSERT INTO bets (user_id, event_id, outcome, coefficient, amount, currency, status)
             VALUES (:user_id, :event_id, :outcome, :coefficient, :amount, :currency, :status)'
        );

        $stmt->execute([
            'user_id'     => $dto->userId,
            'event_id'    => $dto->eventId,
            'outcome'     => $dto->outcome,
            'coefficient' => $dto->coefficient,
            'amount'      => $dto->amount,
            'currency'    => $dto->currency,
            'status'      => $dto->status,
        ]);

        return new BetDTO(
            id: (int)$this->db->lastInsertId(),
            userId: $dto->userId,
            eventId: $dto->eventId,
            outcome: $dto->outcome,
            coefficient: $dto->coefficient,
            amount: $dto->amount,
            currency: $dto->currency,
            status: $dto->status
        );
    }

    public function save(BetDTO $dto): void
    {
        $stmt = $this->db->prepare(
            'UPDATE bets
             SET status = :status
             WHERE id = :id'
        );

        $stmt->execute([
            'id'     => $dto->id,
            'status' => $dto->status,
        ]);
    }

    public function updateStatus(int $betId, string $status): void
    {
        $stmt = $this->db->prepare(
            'UPDATE bets SET status = :status WHERE id = :id'
        );

        $stmt->execute([
            'id'     => $betId,
            'status' => $status,
        ]);
    }

    public function findById(int $id): ?BetDTO
    {
        $stmt = $this->db->prepare('SELECT * FROM bets WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row ? $this->map($row) : null;
    }

    /**
     * @return BetDTO[]
     */
    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM bets WHERE user_id = :user_id ORDER BY id DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        return array_map([$this, 'map'], $stmt->fetchAll());
    }

    private function map(array $row): BetDTO
    {
        return new BetDTO(
            id: (int)$row['id'],
            userId: (int)$row['user_id'],
            eventId: (int)$row['event_id'],
            outcome: $row['outcome'],
            coefficient: $row['coefficient'],
            amount: $row['amount'],
            currency: $row['currency'],
            status: $row['status']
        );
    }

    public function getAll(): array
    {
        return array_map([$this, 'map'],
            $this->db->query('SELECT * FROM bets')->fetchAll()
        );
    }

    public function getByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM bets WHERE event_id = :id');
        $stmt->execute(['id' => $eventId]);
        return array_map([$this, 'map'], $stmt->fetchAll());
    }

}
