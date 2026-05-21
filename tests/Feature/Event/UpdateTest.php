<?php

declare(strict_types=1);

use App\Domain\Event\Actions\UpdateEvent;
use App\Domain\Event\Models\Event;
use App\Domain\Identity\Models\User;
use App\Domain\Recurrence\Models\RecurrenceException;
use Carbon\Carbon;
use Tests\Factory\Data\UpdateEventDataFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('regular update', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create();

    $data = UpdateEventDataFactory::make();

    UpdateEvent::make()->handle($event, $data);

    assertDatabaseHas('events', [
        'id' => $event->id,
        'user_id' => $user->id,
        'title' => $data->title,
        'description' => $data->description,
        'location' => null,
        'color' => $data->color,
        'rrule' => $data->rrule,
        'starts_at' => Carbon::parse($data->startsAt)->toDateTimeString(),
        'ends_at' => Carbon::parse($data->endsAt)->toDateTimeString(),
        'notify_at' => Carbon::parse($data->notifyAt)->toDateTimeString(),
        'notify_status' => 'waiting',
    ]);
});

test('update preserves notify_status if notify_at is the same', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create([
        'notify_status' => 'sent',
    ]);

    $data = UpdateEventDataFactory::make(['notify_at' => $event->notify_at->toISOString()]);

    UpdateEvent::make()->handle($event, $data);

    assertDatabaseHas('events', [
        'id' => $event->id,
        'notify_status' => $event->notify_status,
    ]);
});

test('update resets notify_status if notify_at is different', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create([
        'notify_status' => 'sent',
    ]);

    $data = UpdateEventDataFactory::make(['notify_at' => '2030-01-01T12:34:56Z']);

    UpdateEvent::make()->handle($event, $data);

    assertDatabaseHas('events', [
        'id' => $event->id,
        'notify_status' => 'waiting',
    ]);
});

test('recurring local update applies an exception instead of modifying the original event', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=WEEKLY';
    $event = Event::factory()->for($user)->create([
        'rrule' => $rrule,
    ]);
    $occursAt = $event->starts_at->clone()->addWeek()->format('Y-m-d');

    $data = UpdateEventDataFactory::make([
        'rrule' => $rrule,
        'occursAt' => $occursAt,
    ]);

    UpdateEvent::make()->handle($event, $data);

    assertDatabaseHas('events', [
        'id' => $event->id,
        'user_id' => $user->id,
        'title' => $event->title,
        'description' => $event->description,
        'location' => $event->location,
        'color' => $event->color,
        'rrule' => $event->rrule,
        'starts_at' => $event->starts_at->toDateTimeString(),
        'ends_at' => $event->ends_at->toDateTimeString(),
        'notify_at' => $event->notify_at->toDateTimeString(),
        'notify_status' => 'waiting',
    ]);

    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'event',
        'recurrenceable_id' => $event->id,
        'occurs_at' => $occursAt,
        'is_cancelled' => false,
        'overrides' => json_encode([
            'title' => $data->title,
            'description' => $data->description,
            'starts_at' => $data->startsAt,
            'ends_at' => $data->endsAt,
            'notify_at' => $data->notifyAt,
        ]),
    ]);
});

test('recurring global update modifies the original event', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=WEEKLY';
    $event = Event::factory()->for($user)->create([
        'rrule' => $rrule,
    ]);
    $occursAt = $event->starts_at->clone()->addWeek();

    $data = UpdateEventDataFactory::make([
        'rrule' => $rrule,
        'startsAt' => $occursAt->clone()->addDay()->format('Y-m-d'),
        'occursAt' => $occursAt->format('Y-m-d'),
        'scope' => 'all',
    ]);

    UpdateEvent::make()->handle($event, $data);

    assertDatabaseHas('events', [
        'id' => $event->id,
        'user_id' => $user->id,
        'title' => $data->title,
        'description' => $data->description,
        'location' => null,
        'color' => $data->color,
        'rrule' => $data->rrule,
        'starts_at' => Carbon::parse($data->startsAt)->toDateTimeString(),
        'ends_at' => Carbon::parse($data->endsAt)->toDateTimeString(),
        'notify_at' => Carbon::parse($data->notifyAt)->toDateTimeString(),
        'notify_status' => 'waiting',
    ]);

    assertDatabaseMissing('recurrence_exceptions', [
        'recurrenceable_type' => 'event',
        'recurrenceable_id' => $event->id,
    ]);
});

test('recurring global update preserves original dates', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=WEEKLY';
    $event = Event::factory()->for($user)->create([
        'rrule' => $rrule,
    ]);
    $occursAt = $event->starts_at->clone()->addWeek();

    $data = UpdateEventDataFactory::make([
        'rrule' => $rrule,
        'startsAt' => $occursAt->toISOString(),
        'occursAt' => $occursAt->format('Y-m-d'),
        'scope' => 'all',
    ]);

    UpdateEvent::make()->handle($event, $data);

    assertDatabaseHas('events', [
        'id' => $event->id,
        'starts_at' => $event->starts_at->toDateTimeString(),
        'ends_at' => $event->ends_at->toDateTimeString(),
        'notify_at' => $event->notify_at->toDateTimeString(),
    ]);
});

test('recurring global update resets existing exceptions', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=WEEKLY';
    $event = Event::factory()->for($user)->create(['rrule' => $rrule]);
    $occursAt = $event->starts_at->clone()->addWeek();

    RecurrenceException::factory()->for($event, 'recurrenceable')->create([
        'occurs_at' => $occursAt->format('Y-m-d'),
        'overrides' => ['notify_status' => 'sent'],
    ]);
    RecurrenceException::factory()->for($event, 'recurrenceable')->create([
        'occurs_at' => $occursAt->clone()->addWeek()->format('Y-m-d'),
    ]);

    $data = UpdateEventDataFactory::make([
        'rrule' => $rrule,
        'occursAt' => $occursAt->format('Y-m-d'),
        'scope' => 'all',
    ]);

    UpdateEvent::make()->handle($event, $data);

    assertDatabaseMissing('recurrence_exceptions', [
        'recurrenceable_type' => 'event',
        'recurrenceable_id' => $event->id,
        'occurs_at' => $occursAt->clone()->addWeek()->format('Y-m-d'),
    ]);

    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'event',
        'recurrenceable_id' => $event->id,
        'occurs_at' => $occursAt->format('Y-m-d'),
        'overrides' => json_encode(['notify_status' => 'sent']),
    ]);
});

test('event splits when changing rrule', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $event = Event::factory()->for($user)->create(['rrule' => $rrule]);

    $data = UpdateEventDataFactory::make([
        'rrule' => 'FREQ=WEEKLY',
        'occursAt' => $event->starts_at->clone()->addDays(4),
        'scope' => 'all',
    ]);

    UpdateEvent::make()->handle($event, $data);

    $startsAt = Carbon::parse($data->startsAt);
    $until = $startsAt->clone()->subDay()->endOfDay()->format('Ymd\THisT');

    assertDatabaseHas('events', [
        'id' => $event->id,
        'rrule' => "FREQ=DAILY;UNTIL={$until}",
    ]);

    assertDatabaseHas('events', [
        'id' => $event->id + 1,
        'starts_at' => $startsAt->toDateTimeString(),
        'rrule' => 'FREQ=WEEKLY',
    ]);
});

test('event splits when resetting rrule', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create(['rrule' => 'FREQ=DAILY']);

    $data = UpdateEventDataFactory::make([
        'rrule' => null,
        'occursAt' => $event->starts_at->clone()->addDays(4),
        'scope' => 'all',
    ]);

    UpdateEvent::make()->handle($event, $data);

    assertDatabaseCount('events', 2);
});

test('event does not split when rrule is the same', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $event = Event::factory()->for($user)->create(['rrule' => $rrule]);

    $data = UpdateEventDataFactory::make([
        'rrule' => $rrule,
        'occursAt' => $event->starts_at->clone()->addDays(4),
        'scope' => 'all',
    ]);

    UpdateEvent::make()->handle($event, $data);

    assertDatabaseCount('events', 1);
});

test('exceptions are transferred and reset when changing rrule', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $event = Event::factory()->for($user)->create(['rrule' => $rrule]);
    $occursAt = $event->starts_at->clone()->addDays(4);

    RecurrenceException::factory()->for($event, 'recurrenceable')->create([
        'occurs_at' => $occursAt->format('Y-m-d'),
        'overrides' => ['notify_status' => 'sent'],
    ]);
    RecurrenceException::factory()->for($event, 'recurrenceable')->create([
        'occurs_at' => $occursAt->clone()->subDays(2)->format('Y-m-d'),
    ]);

    $data = UpdateEventDataFactory::make([
        'startsAt' => $occursAt->toISOString(),
        'endsAt' => $occursAt->clone()->setTime(15, 1, 0)->toISOString(),
        'notifyAt' => $occursAt->clone()->subHour()->toISOString(),
        'rrule' => 'FREQ=WEEKLY',
        'occursAt' => $occursAt->format('Y-m-d'),
        'scope' => 'all',
    ]);

    UpdateEvent::make()->handle($event, $data);

    assertDatabaseMissing('recurrence_exceptions', [
        'recurrenceable_type' => 'event',
        'recurrenceable_id' => $event->id,
    ]);

    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'event',
        'recurrenceable_id' => $event->id + 1,
    ]);
});
