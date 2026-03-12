<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Event\CreateRequest;
use App\Http\Requests\Event\DestroyRequest;
use App\Http\Requests\Event\UpdateRequest;
use App\Models\Event;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

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

        Log::info(Arr::only($data, ['title']));

        // if ($event->notify_at->ne($data['notify_at'])) {
        //     $data['notify_status'] = 'waiting';
        // }

        $event->applyException($data['starts_at'], Arr::only($data, ['title']));

        // $event->update($data);

        return back();
    }

    public function destroy(DestroyRequest $request, Event $event)
    {
        $data = $request->validatedInSnakeCase();

        if (is_null($event->rrule) || $data['scope'] == 'all') {
            $event->deleteExceptions();
            $event->delete();
        } else {
            $event->cancelOccurrence($data['occurs_at']);
        }

        return back();
    }
}
