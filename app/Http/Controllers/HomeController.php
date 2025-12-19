<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\EventDto;
use App\Data\TodoDto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function show(Request $request)
    {
        $search = $request->validate(['d' => 'nullable|date_format:Y-m-d']);
        $date = $search['d'] ?? now($request->cookies->getString('jodi-timezone'))->toDateString();

        return inertia('Home', [
            'todos' => Inertia::defer(
                fn () => TodoDto::collect(
                    $this->user()->todos()
                        ->with('category')
                        ->whereDate('todos.todo_date', $date)
                        ->orderBy('todos.position', 'asc')
                        ->get()
                        ->sortBy(fn ($todo) => $todo->category?->name)
                        ->values()
                )
            ),
            'events' => Inertia::defer(
                fn () => EventDto::collect(
                    $this->user()->events()
                        ->whereDate('starts_at', $date)
                        ->orderBy('starts_at', 'asc')
                        ->get()
                )
            ),
            'categories' => Inertia::defer(
                fn () => $this->user()->categories->pluck('name'),
                'other'
            ),
        ]);
    }
}
