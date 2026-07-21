<?php

declare(strict_types=1);

use App\Domain\Event\Actions\ListEvents;
use App\Domain\Event\Models\Event;
use App\Domain\Identity\Models\User;
use Carbon\Carbon;

test('lists a non-recurring single-day event', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create([
        'starts_at' => '2030-01-01 18:00:00',
        'ends_at' => '2030-01-01 20:00:00',
    ]);

    $events = ListEvents::make()->handle(
        $user,
        Carbon::parse('2030-01-01 00:00:00'),
        Carbon::parse('2030-01-02 23:59:59')
    );

    expect($events)->toHaveCount(1);
    expect($events->first())->toMatchObject([
        'id' => $event->id,
        'startsAt' => '2030-01-01 18:00:00',
        'endsAt' => '2030-01-01 20:00:00',
    ]);
});

test('lists a non-recurring multi-day event', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create([
        'starts_at' => '2030-01-01 18:00:00',
        'ends_at' => '2030-01-03 09:00:00',
    ]);

    $events = ListEvents::make()->handle(
        $user,
        Carbon::parse('2030-01-01 00:00:00'),
        Carbon::parse('2030-01-02 23:59:59')
    );

    expect($events)->toHaveCount(1);
    expect($events->first())->toMatchObject([
        'id' => $event->id,
        'startsAt' => '2030-01-01 18:00:00',
        'endsAt' => '2030-01-03 09:00:00',
    ]);
});

test('does not list an event after it ends at the start of the day', function () {
    $user = User::factory()->create();
    Event::factory()->for($user)->create([
        'starts_at' => '2030-01-01 18:00:00',
        'ends_at' => '2030-01-02 00:00:00',
    ]);

    $events = ListEvents::make()->handle(
        $user,
        Carbon::parse('2030-01-02 00:00:00'),
        Carbon::parse('2030-01-02 23:59:59')
    );

    expect($events)->toBeEmpty();
});

test('lists a recurring single-day occurrence', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create([
        'starts_at' => '2030-01-01 18:00:00',
        'ends_at' => '2030-01-01 20:00:00',
        'rrule' => 'FREQ=WEEKLY',
    ]);

    $events = ListEvents::make()->handle(
        $user,
        Carbon::parse('2030-01-08 00:00:00'),
        Carbon::parse('2030-01-08 23:59:59')
    );

    expect($events)->toHaveCount(1);
    expect($events[0])->toMatchObject([
        'id' => $event->id,
        'startsAt' => '2030-01-08 18:00:00',
        'endsAt' => '2030-01-08 20:00:00',
    ]);
});

test('lists a recurring multi-day occurrence on a continuation day', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create([
        'starts_at' => '2030-01-01 18:00:00',
        'ends_at' => '2030-01-03 09:00:00',
        'rrule' => 'FREQ=WEEKLY',
    ]);

    $events = ListEvents::make()->handle(
        $user,
        Carbon::parse('2030-01-09 00:00:00'),
        Carbon::parse('2030-01-09 23:59:59')
    );

    expect($events)->toHaveCount(1);
    expect($events[0])->toMatchObject([
        'id' => $event->id,
        'startsAt' => '2030-01-08 18:00:00',
        'endsAt' => '2030-01-10 09:00:00',
    ]);
});

test('excludes a recurring occurrence ending at the range boundary', function () {
    $user = User::factory()->create();

    Event::factory()->for($user)->create([
        'starts_at' => '2030-01-01 00:00:00',
        'ends_at' => '2030-01-02 00:00:00',
        'rrule' => 'FREQ=DAILY',
    ]);

    $events = ListEvents::make()->handle(
        $user,
        Carbon::parse('2030-01-02 00:00:00'),
        Carbon::parse('2030-01-02 23:59:59'),
    );

    expect($events)->toHaveCount(1);
    expect($events[0])->toMatchObject([
        'startsAt' => '2030-01-02 00:00:00',
        'endsAt' => '2030-01-03 00:00:00',
    ]);
});
