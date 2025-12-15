<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TodoController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $this->user()->todos()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null,
            'todo_date' => $data['date'],
        ]);

        return back();
    }

    public function update(Request $request, Todo $todo)
    {
        Gate::authorize('update', $todo);

        $todo->update($request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|nullable|string',
            'color' => 'sometimes|nullable|hex_color',
        ]));

        return back();
    }

    public function destroy(Request $request, Todo $todo)
    {
        Gate::authorize('destroy', $todo);

        $todo->delete();

        return back();
    }

    public function complete(Request $request, Todo $todo)
    {
        Gate::authorize('complete', $todo);

        $todo->completed_at = $todo->completed_at ? null : now();
        $todo->save();

        return back();
    }

    public function reorder(Request $request, Todo $todo)
    {
        Gate::authorize('reorder', $todo);

        $data = $request->validate([
            'position' => 'integer',
            'category' => 'nullable|string',
        ]);

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
