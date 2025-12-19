<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Todo\CompleteRequest;
use App\Http\Requests\Todo\CreateRequest;
use App\Http\Requests\Todo\DestroyRequest;
use App\Http\Requests\Todo\ReorderRequest;
use App\Http\Requests\Todo\UpdateRequest;
use App\Models\Todo;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    public function create(CreateRequest $request)
    {
        $data = $request->validatedInSnakeCase();
        $category = isset($data['category'])
            ? $this->user()->categories()->firstOrCreate(['name' => $data['category']])
            : null;

        $this->user()->todos()->create([...$data, 'category_id' => $category?->id]);

        return back();
    }

    public function update(UpdateRequest $request, Todo $todo)
    {
        $data = $request->validatedInSnakeCase();

        DB::transaction(function () use ($data, $todo) {
            $todo->fill($data);

            if ($todo->isDirty('position')) {
                $todo->position = $todo->getHighestOrderNumber();
            }

            if (isset($data['category'])) {
                $todo->category_id = $this->user()
                    ->categories()
                    ->firstOrCreate(['name' => $data['category']])->id;
            }

            $todo->save();
        });

        return back();
    }

    public function destroy(DestroyRequest $request, Todo $todo)
    {
        $todo->delete();

        return back();
    }

    public function complete(CompleteRequest $request, Todo $todo)
    {
        $todo->completed_at = $todo->completed_at ? null : now();
        $todo->save();

        return back();
    }

    public function reorder(ReorderRequest $request, Todo $todo)
    {
        $data = $request->validated();

        // TODO: vibe coded, check later
        DB::transaction(function () use ($todo, $data) {
            $oldCategory = $todo->category_id;
            $oldPosition = $todo->position;

            $newCategory = $data['category']
                ? $this->user()->categories()->where('name', $data['category'])->firstOrFail()->id
                : null;
            $newPosition = $data['position'];

            if ($oldCategory === $newCategory && $oldPosition === $newPosition) {
                return;
            }

            if ($oldCategory !== $newCategory) {
                Todo::where('category_id', $oldCategory)
                    ->where('position', '>', $oldPosition)
                    ->decrement('position');

                Todo::where('category_id', $newCategory)
                    ->where('position', '>=', $newPosition)
                    ->increment('position');

                $todo->category_id = $newCategory;
                $todo->position = $newPosition;
            } else {
                if ($newPosition > $oldPosition) {
                    Todo::where('category_id', $oldCategory)
                        ->where('position', '>', $oldPosition)
                        ->where('position', '<=', $newPosition)
                        ->decrement('position');
                } else {
                    Todo::where('category_id', $oldCategory)
                        ->where('position', '>=', $newPosition)
                        ->where('position', '<', $oldPosition)
                        ->increment('position');
                }

                $todo->position = $newPosition;
            }

            $todo->save();
        });

        return back();
    }
}
