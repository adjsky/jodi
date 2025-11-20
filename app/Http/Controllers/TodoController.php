<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TodoController extends Controller
{
    public function add(Request $request)
    {
        $this->user()->todos()->create($request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'todo_date' => 'date_format:Y-m-d',
        ]));

        return back()->with('success', __('Todo successfully created.'));
    }

    public function update(Request $request, Todo $todo) {}

    public function delete(Request $request, Todo $todo)
    {
        Gate::authorize('delete', $todo);

        $todo->delete();

        return back()->with('success', __('Todo successfully deleted.'));
    }

    public function complete(Request $request, string $id)
    {
        //
    }
}
