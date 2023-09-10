<?php

namespace App\Repositories\Auth;

use App\Models\User;
use App\DTOs\Auth\UserDTO;

class UserRepository
{
    public function createUser(UserDTO $userDTO): User
    {
        return User::create([
            'name' => $userDTO->name,
            'email' => $userDTO->email,
            'password' => bcrypt($userDTO->password),
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findById(string $id): ?User
    {
        return User::find($id);
    }
}
