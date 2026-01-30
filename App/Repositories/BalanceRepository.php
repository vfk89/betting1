<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DTO\BalanceDTO;
use App\Database\Connection;
use PDO;

final class BalanceRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getConnection();
    }

    public function getForUser(int $userId, string $currency): BalanceDTO
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM user_balances
             WHERE user_id = :user_id AND currency = :currency'
        );

        $stmt->execute([
            'user_id' => $userId,
            'currency' => $currency,
        ]);

        $row = $stmt->fetch();

        if (!$row) {
            $this->db->prepare(
                'INSERT INTO user_balances (user_id, currency, balance)
                 VALUES (:user_id, :currency, 0)'
            )->execute([
                'user_id' => $userId,
                'currency' => $currency,
            ]);

            return new BalanceDTO($userId, $currency, '0.00');
        }

        return new BalanceDTO(
            (int)$row['user_id'],
            $row['currency'],
            $row['balance']
        );
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM user_balances WHERE user_id = :id');
        $stmt->execute(['id' => $userId]);

        return array_map([$this, 'map'], $stmt->fetchAll());
    }


    public function save(BalanceDTO $dto): void
    {
        $stmt = $this->db->prepare(
            'UPDATE user_balances
             SET balance = :balance
             WHERE user_id = :user_id AND currency = :currency'
        );

        $stmt->execute([
            'balance' => $dto->balance,
            'user_id' => $dto->userId,
            'currency' => $dto->currency,
        ]);
    }

    private function map(array $row): BalanceDTO
    {
        return new BalanceDTO(
            (int)$row['user_id'],
            $row['currency'],
            $row['balance']
        );
    }
}
