<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\UserDTO;
use App\Models\User;
use App\Repositories\UserRepository;

final readonly class UserService
{
    public function __construct(
        private UserRepository $users
    ) {}



    public function getById(int $id): ?User
    {
        $dto = $this->users->getById($id);

        return $dto ? new User($dto) : null;
    }

//    public function getByLogin(string $login): ?User
//    {
//        $dto = $this->users->findByLogin($login);
//
//        return $dto ? new User($dto) : null;
//    }


    public function getAllUsers(): array
    {
        return array_map(
            fn (UserDTO $dto) => new User($dto),
            $this->users->getAll()
        );
    }


//    public function block(User $user): void
//    {
//        $this->users->updateStatus($user->id(), 'blocked');
//    }
}
