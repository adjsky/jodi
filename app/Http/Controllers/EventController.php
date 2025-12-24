<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Event\CreateRequest;
use App\Http\Requests\Event\DestroyRequest;
use App\Http\Requests\Event\UpdateRequest;
use App\Models\Event;
use Carbon\Carbon;

class EventController extends Controller
{
    public function create(CreateRequest $request)
    {
        $data = $request->validatedInSnakeCase();
        // TODO: should subHours(x) be a preference or configuration?
        $data['notify_at'] = Carbon::parse($data['starts_at'])->subHours(3);
        $data['notify_status'] = 'waiting';

        $this->user()->events()->create($data);

        return back();
    }

    public function update(UpdateRequest $request, Event $event)
    {
        $event->update($request->validatedInSnakeCase());

        return back();
    }

    public function destroy(DestroyRequest $request, Event $event)
    {
        $event->delete();

        return back();
    }
}
