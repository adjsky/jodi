<?php

declare(strict_types=1);

namespace App\Domain\Todo\Data\Input;

use App\Support\Data\JodiData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class ReorderTodosData extends JodiData
{
    /** @var Collection<int, ReorderTodoData> */
    #[DataCollectionOf(ReorderTodoData::class)]
    public Collection $todos;
}
