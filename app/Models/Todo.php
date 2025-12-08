<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Todo extends Model implements Sortable
{
    /** @use HasFactory<\Database\Factories\TodoFactory> */
    use HasFactory, SortableTrait;

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

    /** @return Builder<static> */
    public function buildSortQuery(): Builder
    {
        return static::query()->where('category', $this->category);
    }
}
