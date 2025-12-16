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
        $data = $request->validated();

        $this->user()->todos()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null,
            'todo_date' => $data['date'],
        ]);

        return back();
    }

    public function update(UpdateRequest $request, Todo $todo)
    {
        $todo->update($request->validated());

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
            $oldCategory = $todo->category;
            $oldPosition = $todo->position;
            $newCategory = $data['category'];
            $newPosition = $data['position'];

            if ($oldCategory !== $newCategory) {
                Todo::where('category', $oldCategory)
                    ->where('position', '>', $oldPosition)
                    ->decrement('position');

                Todo::where('category', $newCategory)
                    ->where('position', '>=', $newPosition)
                    ->increment('position');

                $todo->category = $newCategory;
                $todo->position = $newPosition;
                $todo->save();
            } else {
                if ($newPosition !== $oldPosition) {
                    if ($newPosition > $oldPosition) {
                        Todo::where('category', $oldCategory)
                            ->where('position', '>', $oldPosition)
                            ->where('position', '<=', $newPosition)
                            ->decrement('position');
                    } else {
                        Todo::where('category', $oldCategory)
                            ->where('position', '>=', $newPosition)
                            ->where('position', '<', $oldPosition)
                            ->increment('position');
                    }

                    $todo->position = $newPosition;
                    $todo->save();
                }
            }
        });

        return back();
    }
}
