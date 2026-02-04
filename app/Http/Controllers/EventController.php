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
        $data = $request->validatedInSnakeCase();
        $data['notify_status'] = 'waiting';

        $this->user()->events()->create($data);

        return back();
    }

    public function update(UpdateRequest $request, Event $event)
    {
        $data = $request->validatedInSnakeCase();

        if ($event->notify_at->ne($data['notify_at'])) {
            $data['notify_status'] = 'waiting';
        }

        $event->update($data);

        return back();
    }

    public function destroy(DestroyRequest $request, Event $event)
    {
        $event->delete();

        return back();
    }
}
