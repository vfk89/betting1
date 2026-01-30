<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTO\ContactDTO;
use App\Database\Connection;
use PDO;

final class ContactRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getConnection();
    }

    public function create(ContactDTO $dto): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO user_contacts (user_id, type, value)
             VALUES (:user_id, :type, :value)'
        );

        $stmt->execute([
            'user_id' => $dto->userId,
            'type' => $dto->type,
            'value' => $dto->value,
        ]);
    }

    /**
     * @return ContactDTO[]
     */
    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM user_contacts WHERE user_id = :id'
        );

        $stmt->execute(['id' => $userId]);

        return array_map(
            fn($row) => new ContactDTO(
                (int)$row['user_id'],
                $row['type'],
                $row['value']
            ),
            $stmt->fetchAll()
        );
    }
}
