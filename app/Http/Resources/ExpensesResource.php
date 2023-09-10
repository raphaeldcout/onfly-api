<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpensesResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $expenses = [];

        foreach ($this->collection as $product) {

            array_push($expenses, [
                'id' => $product->id,
                'description' => $product->description,
                'date_registration' => $product->date_registration,
                'value' => $product->value,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ]);
        }

        return $expenses;
    }
}
