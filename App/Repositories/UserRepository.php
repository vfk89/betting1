<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Database\Connection;
use App\DTO\UserDTO;
use App\Models\User;
use PDO;

final class UserRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getConnection();
    }

    public function getById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) return null;

        $dto = new UserDTO(
            $data['login'],
            $data['password'], // хэш
            $data['name'],
            $data['gender'],
            $data['birth_date'],
            $data['status']
        );

        return new User($dto);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM users');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn(array $row) => new UserDTO(
                (int)$row['id'],
                $row['login'],
                $row['password'],
                $row['name'],
                $row['gender'],
                $row['birth_date'],
                $row['status']
            ),
            $rows
        );
    }


    public function create(UserDTO $dto): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (login, password, name, gender, birth_date, status) 
             VALUES (:login, :password, :name, :gender, :birth_date, :status)'
        );
        $stmt->execute([
            'login' => $dto->login,
            'password' => $dto->getHashedPassword(),
            'name' => $dto->name,
            'gender' => $dto->gender,
            'birth_date' => $dto->birthDate,
            'status' => $dto->status
        ]);
        return (int)$this->pdo->lastInsertId();
    }
}
