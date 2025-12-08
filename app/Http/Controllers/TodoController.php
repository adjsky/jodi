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
        $this->user()->todos()->create($request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'todo_date' => 'date_format:Y-m-d',
        ]));

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

        DB::beginTransaction();

        Todo::setNewOrder([$todo->id], $data['position']);

        $todo->category = $data['category'];
        $todo->save();

        DB::commit();

        return back();
    }
}
