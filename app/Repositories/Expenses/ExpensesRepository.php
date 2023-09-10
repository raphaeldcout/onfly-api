<?php

namespace App\Repositories\Expenses;

use App\Models\{User, Expenses};
use App\DTOs\Expenses\ExpenseDTO;
use Illuminate\Database\Eloquent\Collection;

class ExpensesRepository
{
    public function getAll(string $userId): Collection
    {
        $user = User::find($userId);
        $expenses = $user->expenses;

        return $expenses;
    }

    public function createExpense(ExpenseDTO $expenseDTO, string $userId): Expenses
    {
        return Expenses::create([
            'description' => $expenseDTO->description,
            'date_registration' => $expenseDTO->date_registration,
            'user_id' => $userId,
            'value' => $expenseDTO->value,
        ]);
    }

    public function findById(string $id): ?Expenses
    {
        $expense = Expenses::find($id);

        return $expense;
    }

    public function updateExpense(ExpenseDTO $expenseDTO, string $id, string $user_id): ?Expenses
    {
        $expense = Expenses::where([
            ['id', $id],
            ['user_id', $user_id],
        ])->first();

        if ($expense) {
            $expense->update([
                'description' => $expenseDTO->description,
                'date_registration' => $expenseDTO->date_registration,
                'value' => $expenseDTO->value
            ]);
        }

        return $expense;
    }

    public function deleteExpense(string $id, string $user_id): void
    {
        $expense = Expenses::where([
            ['id', $id],
            ['user_id', $user_id],
        ])->first();

        if ($expense) {
            $expense->delete();
        }
    }
}
