<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TodoPositionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoPosition extends Model
{
    /** @use HasFactory<TodoPositionFactory> */
    use HasFactory;

    protected $fillable = [
        'occurs_at',
        'position',
    ];

    protected $hidden = [];

    /**
     * @return array{occurs_at: "date:Y-m-d"}
     */
    protected function casts(): array
    {
        return [
            'occurs_at' => 'date:Y-m-d',
        ];
    }
}
