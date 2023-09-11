<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    description: 'Expenses model',
    properties: [
        new OA\Property(
            property: 'id',
            description: 'ID',
            type: 'integer'
        ),
        new OA\Property(
            property: 'description',
            description: 'Description',
            type: 'string'
        ),
        new OA\Property(
            property: 'date_registration',
            description: 'Date registration',
            type: 'string'
        ),
        new OA\Property(
            property: 'user_id',
            description: 'ID User',
            type: 'string'
        ),
        new OA\Property(
            property: 'value',
            description: 'Value',
            type: 'number'
        ),
        new OA\Property(
            property: 'created_at',
            description: 'Created timestamp',
            type: 'string'
        ),
        new OA\Property(
            property: 'updated_at',
            description: 'Updated timestamp',
            type: 'string'
        ),
    ]
)]

class Expenses extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'date_registration',
        'user_id',
        'value',
    ];

    /**
     * The function that show user owner expense.
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
