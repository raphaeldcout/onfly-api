<?php

namespace App\DTOs\Expenses;

class ExpenseDTO
{
    public string $description;
    public string $date_registration;
    public float $value;

    public function __construct(string $description, string $date_registration, float $value)
    {
        $this->description = $description;
        $this->date_registration = $date_registration;
        $this->value = $value;
    }
}
