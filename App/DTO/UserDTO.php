<?php
declare(strict_types=1);

namespace App\DTO;

final class UserDTO
{
    public ?int $id;
    public string $login;
    public string $password;
    public string $name;
    public string $gender;
    public string $birthDate;
    public string $status;

    public function __construct(
        ?int $id,
        string $login,
        string $password,
        string $name,
        string $gender,
        string $birthDate,
        string $status = 'active'
    ) {
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->name = $name;
        $this->gender = $gender;
        $this->birthDate = $birthDate;
        $this->status = $status;
    }

    public function getHashedPassword(): string
    {
        return password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'name' => $this->name,
            'gender' => $this->gender,
            'birthDate' => $this->birthDate,
            'status' => $this->status,
        ];
    }
}
