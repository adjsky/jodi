<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Todo extends Model implements Sortable
{
    /** @use HasFactory<\Database\Factories\TodoFactory> */
    use HasFactory, SortableTrait;

    protected $fillable = [
        'title',
        'description',
        'category_id',
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

    /** @return BelongsTo<Category,$this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return Builder<static> */
    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('category', $this->category)
            ->whereDate('todo_date', $this->todo_date);
    }
}
