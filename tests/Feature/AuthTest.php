<?php

namespace Tests\Feature;

use App\DTOs\Auth\UserDTO;
use App\Http\Requests\{RegisterRequest, LoginRequest};
use App\Http\Resources\{UserResource, AuthResource};
use App\Models\User;
use App\Repositories\Auth\UserRepository;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Illuminate\Http\{Response, JsonResponse};
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A test to register a new usar with error validation form response.
     */
    public function test_registers_a_new_user_without_data(): void
    {
        $payload = [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => ''
        ];

        $response = $this->post('/api/register', $payload);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_FOUND);
    }

    /**
     * A test to register a new user with success.
     */
    public function test_registers_a_new_user_with_success(): void
    {
        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '123456',
            'password_confirmation' => '123456'
        ];

        $request = new RegisterRequest($payload);

        $authService = new AuthService(new UserRepository());

        $userDTO = new UserDTO($request->input('name'), $request->input('email'), $request->input('password'));
        $user = $authService->register($userDTO);

        $userResource = (new UserResource($user))->response()->setStatusCode(Response::HTTP_CREATED);

        $response = new TestResponse($userResource);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_CREATED)->assertJsonStructure([
            'data' => [
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);
        // Assert the response content `name` and `email` passed.
        $this->assertDatabaseHas('users', [
            'name' => $payload['name'],
            'email' => $payload['email']
        ]);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to login a user not exist.
     */
    public function test_login_a_user_not_exist(): void
    {
        $payload = [
            'email' => 'usuario_que_nao_existe@teste.com.br',
            'password' => '1232131',
            'device_name' => Str::random(10),
        ];

        $response = $this->post('/api/login', $payload);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)->assertExactJson([
            'message' => __('messages.login_failed')
        ]);
    }

    /**
     * A test to login a user with success.
     */
    public function test_login_a_user_with_success(): void
    {
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => Str::random(10),
        ];

        $request = new LoginRequest($payload);

        $authService = new AuthService(new UserRepository());

        $token = $authService->login($request->input('email'), $request->input('password'), $request->input('device_name'));

        $userResource = (new AuthResource(['access_token' => $token]))->response()->setStatusCode(Response::HTTP_OK);

        $response = new TestResponse($userResource);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure([
            'data' => [
                'access_token'
            ],
        ]);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to get me without token.
     */
    public function test_get_me_without_token(): void
    {
        $response = $this->getJson('/api/me', [
            'Authorization' => "",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * A test to get me with success.
     */
    public function test_get_me_with_success(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken(Str::random(10))->plainTextToken;

        $response = $this->getJson('/api/me', [
            'Authorization' => "Bearer {$token}",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure([
            'data' => [
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to do logout.
     */
    public function test_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken(Str::random(10))->plainTextToken;

        $response = $this->postJson('/api/logout', [], [
            'Authorization' => "Bearer {$token}",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
