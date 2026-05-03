<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Todo\Data\Input\ReorderTodosData;
use App\Domain\Todo\Models\Todo;
use App\Domain\Todo\Models\TodoPosition;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ReorderTodos extends JodiAction
{
    public function handle(User $user, ReorderTodosData $data): void
    {
        DB::transaction(function () use ($data, $user) {
            TodoPosition::upsert(
                $data->todos->map(fn ($t) => [
                    'todo_id' => $t->id,
                    'date' => $t->date,
                    'position' => $t->position,
                ])->toArray(),
                uniqueBy: ['todo_id', 'date'],
                update: ['position']
            );

            $data->todos
                ->groupBy('categoryId')
                ->each(
                    fn ($todos, $categoryId) => $user->todos()
                        ->whereIn('id', $todos->pluck('id'))
                        ->update(['category_id' => $categoryId ?: null])
                );
        });
    }

    public function authorize(JodiRequest $request): bool
    {
        return $this->user()->can('reorder', [Todo::class, $request->todos]);
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle($this->user(), ReorderTodosData::from($request));

        return back();
    }
}
