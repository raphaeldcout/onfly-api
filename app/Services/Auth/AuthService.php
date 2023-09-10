<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Auth\UserRepository;
use App\DTOs\Auth\UserDTO;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Execute register and expect User entity created.
     *
     * @return User
     */
    public function register(UserDTO $userDTO): User
    {
        return $this->userRepository->createUser($userDTO);
    }

    /**
     * Execute login and expect token created.
     *
     * @return string | null
     */
    public function login(string $email, string $password, string $device_name): string | null
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return null;
        }

        if ($device_name && Hash::check($password, $user->password)) {
            $token = $user->createToken($device_name)->plainTextToken;

            return $token;
        }

        return null;
    }
}
