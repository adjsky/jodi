<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Event\CreateRequest;
use App\Http\Requests\Event\DestroyRequest;
use App\Http\Requests\Event\UpdateRequest;
use App\Models\Event;

class EventController extends Controller
{
    public function create(CreateRequest $request)
    {
        $data = $request->validated();

        $this->user()->events()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'is_all_day' => $data['isAllDay'],
            'starts_at' => $data['startsAt'],
            'ends_at' => $data['endsAt'] ?? null,
        ]);

        return back();
    }

    public function update(UpdateRequest $request, Event $event)
    {
        $event->update($request->validated());

        return back();
    }

    public function destroy(DestroyRequest $request, Event $event)
    {
        $event->delete();

        return back();
    }
}
