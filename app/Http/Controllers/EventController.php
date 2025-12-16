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
        $this->user()->events()->create($request->validatedInSnakeCase());

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
