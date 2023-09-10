<?php

namespace Tests\Feature;

use App\DTOs\Expenses\ExpenseDTO;
use App\Http\Requests\{ExpensesStoreRequest, ExpensesUpdateRequest};
use App\Http\Resources\ExpenseResource;
use App\Models\{User, Expenses};
use App\Repositories\Auth\UserRepository;
use App\Repositories\Expenses\ExpensesRepository;
use App\Services\Expenses\ExpensesService;
use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Illuminate\Http\{Response, JsonResponse};
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExpensesTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A test to get all expenses user owner.
     */
    public function test_get_all_expenses_user_owner(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken(Str::random(10))->plainTextToken;

        Expenses::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/expenses', [
            'Authorization' => "Bearer {$token}",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'description',
                    'date_registration',
                    'value',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to create expense with error validation form response.
     */
    public function test_create_a_new_expense_without_data(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken(Str::random(10))->plainTextToken;

        $payload = [
            'description' => '',
            'date_registration' => '',
            'value' => ''
        ];

        $response = $this->post('/api/expenses', $payload, [
            'Authorization' => "Bearer {$token}",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_FOUND);
    }

    /**
     * A test to create expense with success.
     */
    public function test_create_a_new_expense_with_success(): void
    {
        $user = User::factory()->create();

        $payload = [
            'description' => $this->faker->sentence,
            'date_registration' => $this->faker->date,
            'value' => $this->faker->randomFloat(2, 1, 1000)
        ];

        $request = new ExpensesStoreRequest($payload);

        $expensesService = new ExpensesService(new ExpensesRepository(), new UserRepository());

        $expenseDTO = new ExpenseDTO(
            $request->input('description'),
            $request->input('date_registration'),
            $request->input('value')
        );
        $expense = $expensesService->create($expenseDTO, $user->id);

        $expenseResource = (new ExpenseResource($expense))->response()->setStatusCode(Response::HTTP_CREATED);

        $response = new TestResponse($expenseResource);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_CREATED)->assertJsonStructure([
            'data' => [
                'id',
                'description',
                'date_registration',
                'value',
                'created_at',
                'updated_at',
            ],
        ]);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to get expense by other user UNAUTHORIZED.
     */
    public function test_get_expense_by_other_user_unauthorized(): void
    {
        $user_owner = User::factory()->create();
        $user_owner->createToken(Str::random(10))->plainTextToken;

        $expense = Expenses::factory()->create(['user_id' => $user_owner->id]);

        $user_faker = User::factory()->create();
        $token = $user_faker->createToken(Str::random(10))->plainTextToken;

        $response = $this->getJson('/api/expenses/' . $expense->getRawOriginal('id'), [
            'Authorization' => "Bearer {$token}",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to get expense that not exist.
     */
    public function test_get_expense_not_found(): void
    {
        $user_owner = User::factory()->create();
        $token = $user_owner->createToken(Str::random(10))->plainTextToken;

        $response = $this->getJson('/api/expenses/' . Str::random(10), [
            'Authorization' => "Bearer {$token}",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to get expense with success.
     */
    public function test_get_expense_with_success(): void
    {
        $user_owner = User::factory()->create();
        $token = $user_owner->createToken(Str::random(10))->plainTextToken;

        $expense = Expenses::factory()->create(['user_id' => $user_owner->id]);

        $response = $this->getJson('/api/expenses/' . $expense->getRawOriginal('id'), [
            'Authorization' => "Bearer {$token}",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_OK);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to update expense with error validation form response.
     */
    public function test_update_a_expense_without_data(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken(Str::random(10))->plainTextToken;

        $payload = [
            'id' => '',
            'description' => '',
            'date_registration' => '',
            'value' => ''
        ];

        $response = $this->patchJson('/api/expenses/1', $payload, [
            'Authorization' => "Bearer {$token}",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * A test to update expense that not exist.
     */
    public function test_update_expense_not_found(): void
    {
        $user_owner = User::factory()->create();
        $token = $user_owner->createToken(Str::random(10))->plainTextToken;

        $expense = Expenses::factory()->create(['user_id' => $user_owner->id]);

        $payload = [
            'id' => $expense->getRawOriginal('id'),
            'description' => $expense->getRawOriginal('description'),
            'date_registration' => $expense->getRawOriginal('date_registration'),
            'value' => $expense->getRawOriginal('value')
        ];

        $response = $this->patchJson('/api/expenses/' . Str::random(10), $payload, [
            'Authorization' => "Bearer {$token}",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to update expense by other user UNAUTHORIZED.
     */
    public function test_update_expense_by_other_user_unauthorized(): void
    {
        $user_owner = User::factory()->create();
        $user_owner->createToken(Str::random(10))->plainTextToken;

        $expense = Expenses::factory()->create(['user_id' => $user_owner->id]);
        $expense_id = $expense->getRawOriginal('id');

        $user_faker = User::factory()->create();
        $token = $user_faker->createToken(Str::random(10))->plainTextToken;

        $response = $this->patchJson('/api/expenses/' . $expense_id, [
            'Authorization' => "Bearer {$token}",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to update expense with success.
     */
    public function test_update_a_expense_with_success(): void
    {
        $user = User::factory()->create();

        $expense = Expenses::factory()->create(['user_id' => $user->id]);
        $expense_created = [
            'id' => $expense->getRawOriginal('id'),
            'description' => $expense->getRawOriginal('description'),
            'date_registration' => $expense->getRawOriginal('date_registration'),
            'value' => $expense->getRawOriginal('value')
        ];

        $payload = [
            'description' => $this->faker->sentence,
            'date_registration' => $this->faker->date,
            'value' => $this->faker->randomFloat(2, 1, 1000)
        ];

        $request = new ExpensesUpdateRequest($payload);

        $expensesService = new ExpensesService(new ExpensesRepository(), new UserRepository());

        $expenseDTO = new ExpenseDTO(
            $request->input('description'),
            $request->input('date_registration'),
            $request->input('value')
        );
        $expense = $expensesService->update($expenseDTO, $expense_created['id'], $user->id);

        $expenseResource = (new ExpenseResource($expense))->response()->setStatusCode(Response::HTTP_OK);

        $response = new TestResponse($expenseResource);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure([
            'data' => [
                'id',
                'description',
                'date_registration',
                'value',
                'created_at',
                'updated_at',
            ],
        ]);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to delete expense that not exist.
     */
    public function test_delete_expense_not_found(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken(Str::random(10))->plainTextToken;

        $expense = Expenses::factory()->create(['user_id' => $user->id]);

        $payload = [
            'id' => $expense->getRawOriginal('id'),
            'description' => $expense->getRawOriginal('description'),
            'date_registration' => $expense->getRawOriginal('date_registration'),
            'value' => $expense->getRawOriginal('value')
        ];

        $response = $this->json('delete', '/api/expenses/' . Str::random(10), $payload, [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to delete expense by other user UNAUTHORIZED.
     */
    public function test_delete_expense_by_other_user_unauthorized(): void
    {
        $user_owner = User::factory()->create();
        $user_owner->createToken(Str::random(10))->plainTextToken;

        $expense = Expenses::factory()->create(['user_id' => $user_owner->id]);

        $user_faker = User::factory()->create();
        $token = $user_faker->createToken(Str::random(10))->plainTextToken;

        $response = $this->json('delete', '/api/expenses/' . $expense->getRawOriginal('id'), [
            'Authorization' => "Bearer {$token}",
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }

    /**
     * A test to delete expense with success.
     */
    public function test_delete_a_expense_with_success(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken(Str::random(10))->plainTextToken;

        $expense = Expenses::factory()->create(['user_id' => $user->id]);

        $payload = [
            'id' => $expense->getRawOriginal('id'),
            'description' => $expense->getRawOriginal('description'),
            'date_registration' => $expense->getRawOriginal('date_registration'),
            'value' => $expense->getRawOriginal('value')
        ];

        $response = $this->json('delete', '/api/expenses/' . $payload['id'], $payload, [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);

        // Assert the response status code.
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        // Assert the instace of class `JsonResponse` resource traitment.
        $this->assertInstanceOf(JsonResponse::class, $response->baseResponse);
    }
}
