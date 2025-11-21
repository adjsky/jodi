<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\TodoDto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function show(Request $request)
    {
        $user = $this->user();

        $search = $request->validate(['d' => 'nullable|date_format:Y-m-d']);
        $date = $search['d'] ?? now()->toDateString();

        return inertia('Home', [
            'todos' => Inertia::defer(
                fn () => TodoDto::collect(
                    $user->todos()
                        ->where('todo_date', '=', $date)
                        ->orderBy('created_at', 'asc')
                        ->orderBy('category', 'asc')
                        ->get()
                )
            ),
        ]);
    }
}
