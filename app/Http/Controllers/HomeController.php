<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Event\Actions\ListEvents;
use App\Domain\Todo\Actions\ListTodos;
use App\Domain\Todo\Data\Output\CategoryData;
use App\Support\Http\JodiRequest;
use Carbon\Carbon;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke(JodiRequest $request)
    {
        $search = $request->validate(['d' => 'nullable|date_format:Y-m-d']);
        $timezone = $request->timezone();

        $date = $search['d'] ?? now($timezone)->toDateString();

        $startUtc = Carbon::parse($date, $timezone)->startOfDay()->utc();
        $endUtc = Carbon::parse($date, $timezone)->endOfDay()->utc();

        return inertia('Home', [
            'todos' => ListTodos::make()->handle($this->user(), $date, $startUtc, $endUtc),
            'events' => ListEvents::make()->handle($this->user(), $startUtc, $endUtc),
            'me' => [
                'nInvitations' => $this->user()->invitations->count(),
                'nFriends' => $this->user()->friends->count(),
            ],
            'categories' => Inertia::defer(
                fn () => CategoryData::collect($this->user()->categories()->get()),
            ),
        ]);
    }
}
