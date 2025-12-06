<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Todo;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TodoDto extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public ?string $category,
        public ?string $color,
        public Carbon $date,
        public ?Carbon $completedAt,
        public ?Carbon $createdAt,
    ) {}

    public static function fromModel(Todo $todo): self
    {
        return new self(
            $todo->id,
            $todo->title,
            $todo->description,
            $todo->category,
            $todo->color,
            $todo->todo_date,
            $todo->completed_at,
            $todo->created_at
        );
    }
}
