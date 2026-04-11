<?php

declare(strict_types=1);

namespace App\Domain\Todo\Data\Output;

use App\Domain\Todo\Models\Category;
use App\Domain\Todo\Models\Todo;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TodoData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public ?CategoryData $category,
        public ?string $color,
        public ?string $rrule,
        public ?string $recurringSince,
        public ?string $occursAt,
        public Carbon $scheduledAt,
        public bool $hasTime,
        public ?Carbon $notifyAt,
        public ?Carbon $completedAt,
        public ?Carbon $createdAt,
    ) {}

    public static function fromModel(Todo $todo): self
    {
        return new self(
            $todo->id,
            $todo->title,
            $todo->description,
            $todo->category ? CategoryData::from($todo->category) : null,
            $todo->color,
            $todo->rrule,
            $todo->recurring_since,
            $todo->occurs_at,
            $todo->scheduled_at,
            $todo->has_time,
            $todo->notify_at,
            $todo->completed_at,
            $todo->created_at
        );
    }
}

#[TypeScript]
class CategoryData extends Data
{
    public function __construct(public int $id, public string $name) {}

    public static function fromModel(Category $category): self
    {
        return new self($category->id, $category->name);
    }
}
