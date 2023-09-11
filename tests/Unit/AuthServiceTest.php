<?php

namespace Tests\Unit;

use App\DTOs\Auth\UserDTO;
use App\Models\User;
use App\Repositories\Auth\UserRepository;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A unit test to register a new user from `service_layer` with success.
     */
    public function test_register_a_new_user_success(): void
    {
        // Cria uma instância mock do UserRepository.
        $userRepository = Mockery::mock(UserRepository::class);

        // Cria uma instância AuthService injetando o UserRepository mock.
        $authService = new AuthService($userRepository);

        // Cria um objeto UserDTO com dados fakes.
        $userDTO = new UserDTO('Raphael Melo', 'raphael.melo@example.com', '123456');

        // Cria um objeto.
        $expectedUser = new User();

        // Define o molde do UserRepository mock.
        $userRepository->shouldReceive('createUser')
            ->once()
            ->withArgs([$userDTO])
            ->andReturn($expectedUser);

        $response = $authService->register($userDTO);

        // Assert the response is a object USER.
        $this->assertInstanceOf(User::class, $response);
    }

    /**
     * A unit test to login a user from `service_layer` with invalid credentials.
     */
    public function test_a_login_with_invalid_credentials(): void
    {
        // Cria uma instância mock do UserRepository.
        $userRepository = Mockery::mock(UserRepository::class);

        // Cria uma instância AuthService injetando o UserRepository mock.
        $authService = new AuthService($userRepository);

        $email = 'raphael.melo@example.com';
        $password = '123456';
        $device_name = 'UnitTest';

        // Define o molde do UserRepository mock.
        $userRepository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn(null);

        $response = $authService->login($email, $password, $device_name);

        // Assert the response is NULL.
        $this->assertNull($response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
