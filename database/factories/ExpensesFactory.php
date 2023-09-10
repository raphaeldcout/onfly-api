<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expenses>
 */
class ExpensesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->sentence(),
            'date_registration' => fake()->date(),
            'value' => fake()->randomFloat(2, 1, 1000),
            'user_id' => function () {
                // Aqui você pode criar um usuário usando a UserFactory
                return User::factory()->create()->id;
            },
        ];
    }
}
