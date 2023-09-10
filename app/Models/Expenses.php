<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
