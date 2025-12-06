<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    /** @use HasFactory<\Database\Factories\TodoFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'todo_date',
        'color',
    ];

    protected $hidden = [];

    /**
     * @return array{notify_at: "datetime", completed_at: "datetime", todo_date: "date:Y-m-d"}
     */
    protected function casts(): array
    {
        return [
            'notify_at' => 'datetime',
            'completed_at' => 'datetime',
            'todo_date' => 'date:Y-m-d',
        ];
    }
}
