<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Todo;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date_format:Y-m-d',
            'isAllDay' => 'required|boolean',
            'startsAt' => 'required|date_format:H:i',
            'endsAt' => 'nullable|date_format:H:i',
        ]);

        $date = CarbonImmutable::parse($data['date']);

        $this->user()->events()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'is_all_day' => $data['isAllDay'],
            'starts_at' => $date->setTimeFromTimeString($data['startsAt']),
            'ends_at' => $request->has('endsAt') ? $date->setTimeFromTimeString($data['endsAt']) : null,
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
}
