<?php

namespace App\Policies;

use App\Models\Expenses;
use App\Models\User;

class ExpensesPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expenses $expenses): bool
    {
        return $user->id === $expenses->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expenses $expenses): bool
    {
        return $user->id === $expenses->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expenses $expenses): bool
    {
        return $user->id === $expenses->user_id;
    }
}
