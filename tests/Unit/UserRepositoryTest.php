<?php

namespace Tests\Unit;

use App\DTOs\Auth\UserDTO;
use App\Models\User;
use App\Repositories\Auth\UserRepository;
use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Mockery;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A unit test to create user from `repository`.
     */
    public function test_create_user_from_repository(): void
    {
        // Cria uma instância mock do UserRepository.
        $userRepository = Mockery::mock(UserRepository::class);

        // Define o molde do UserRepository mock.
        $userRepository->shouldReceive('createUser')
            ->once()
            ->withArgs([UserDTO::class])
            ->andReturn(new User());

        $userDTO = new UserDTO('Raphael Melo', 'raphael.melo@example.com', '123456');

        $user = $userRepository->createUser($userDTO);

        // Assert the instace of class `User` resource traitment.
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * A unit test to find user by email in the `repository`.
     */
    public function test_find_by_email_with_existing_user(): void
    {
        // Cria uma instância do UserRepository.
        $userRepository = new UserRepository();

        $user = User::factory()->create();

        $foundUser = $userRepository->findByEmail($user->email);

        // Assert the instace of class `User`.
        $this->assertInstanceOf(User::class, $foundUser);
        // Assert the emails.
        $this->assertEquals($user->email, $foundUser->email);
    }


    /**
     * A unit test to find user by email that not exist in the `repository`.
     */
    public function test_find_by_email_with_not_existing_user()
    {
        // Cria uma instância do UserRepository.
        $userRepository = new UserRepository();

        $foundUser = $userRepository->findByEmail('email-nenhum@example.com');

        // Assert the result is NULL.
        $this->assertNull($foundUser);
    }

    /**
     * A unit test to find user by id in the `repository`.
     */
    public function test_find_by_id_with_existing_user()
    {
        // Cria uma instância do UserRepository.
        $userRepository = new UserRepository();

        $user = User::factory()->create();

        $foundUser = $userRepository->findById($user->id);

        // Assert the instace of class `User`.
        $this->assertInstanceOf(User::class, $foundUser);
        // Assert the user returned is the same user factorory.
        $this->assertEquals($user->id, $foundUser->id);
    }
}
