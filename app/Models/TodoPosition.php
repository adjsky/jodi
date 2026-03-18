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
        'date',
        'position',
    ];

    protected $hidden = [];

    /**
     * @return array{date: "date:Y-m-d"}
     */
    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
        ];
    }
}
