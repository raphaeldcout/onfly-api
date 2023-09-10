<?php

namespace App\Services\Expenses;

use App\Repositories\Expenses\ExpensesRepository;
use App\Repositories\Auth\UserRepository;
use App\DTOs\Expenses\ExpenseDTO;
use App\Jobs\ExpenseCreatedJob;
use Illuminate\Database\Eloquent\Collection;
use App\Models\{Expenses};

class ExpensesService
{
    private ExpensesRepository $expensesRepository;
    private UserRepository $userRepository;

    public function __construct(ExpensesRepository $expensesRepository, UserRepository $userRepository)
    {
        $this->expensesRepository = $expensesRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Return all expenses created by user owner.
     *
     * @return Collection<Expenses>
     */
    public function getAll(string $userId): Collection
    {
        return $this->expensesRepository->getAll($userId);
    }

    /**
     * Execute create of expenses.
     *
     * @return Expenses
     */
    public function create(ExpenseDTO $expense, string $userId): Expenses
    {
        $user = $this->userRepository->findById($userId);
        ExpenseCreatedJob::dispatch($user);

        return $this->expensesRepository->createExpense($expense, $userId);
    }

    /**
     * Get expense by ID.
     *
     * @return Expenses
     */
    public function findById(string $id): Expenses | null
    {
        $expense = $this->expensesRepository->findById($id);

        if (!$expense) {
            return null;
        }

        return $expense;
    }

    /**
     * Update a expense by ID.
     *
     * @return Expenses
     */
    public function update(ExpenseDTO $expense, string $id, string $userId): ?Expenses
    {
        $expense = $this->expensesRepository->updateExpense($expense, $id, $userId);

        return $expense;
    }

    /**
     * Delte a expense by ID.
     *
     * @return void
     */
    public function delete(string $id, string $userId): void
    {
        $this->expensesRepository->deleteExpense($id, $userId);
    }
}
