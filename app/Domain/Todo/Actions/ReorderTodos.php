<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Todo\Data\Input\ReorderTodosData;
use App\Domain\Todo\Models\Todo;
use App\Domain\Todo\Models\TodoPosition;
use App\Support\Actions\Action;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ReorderTodos extends Action
{
    public function handle(ReorderTodosData $data): void
    {
        DB::transaction(function () use ($data) {
            TodoPosition::upsert(
                $data->todos->map(fn ($t) => [
                    'todo_id' => $t->id,
                    'date' => $t->date,
                    'position' => $t->position,
                ])->toArray(),
                uniqueBy: ['todo_id', 'date'],
                update: ['position']
            );

            foreach ($data->todos as $t) {
                $this->user()->todos()
                    ->where('id', $t->id)
                    ->update(['category_id' => $t->categoryId]);
            }
        });
    }

    public function authorize(JodiRequest $request): bool
    {
        return $this->user()->can('reorder', [Todo::class, $request->todos]);
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle(ReorderTodosData::from($request));

        return back();
    }
}
