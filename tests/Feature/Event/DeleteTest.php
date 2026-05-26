<?php

declare(strict_types=1);

use App\Domain\Event\Actions\DestroyEvent;
use App\Domain\Event\Models\Event;
use App\Domain\Identity\Models\User;
use App\Domain\Recurrence\Models\RecurrenceException;
use Tests\Factory\Data\DestroyEventDataFactory;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('delete non-recurring event', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create();

    $data = DestroyEventDataFactory::make([
        'scope' => 'this',
    ]);

    DestroyEvent::make()->handle($event, $data);

    assertDatabaseMissing('events', ['id' => $event->id]);
});

test('delete single recurring event', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create(['rrule' => 'FREQ=WEEKLY']);
    $occursAt = $event->starts_at->addWeek()->toDateString();

    $data = DestroyEventDataFactory::make([
        'occursAt' => $occursAt,
        'scope' => 'this',
    ]);

    DestroyEvent::make()->handle($event, $data);

    assertDatabaseHas('events', ['id' => $event->id]);
    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'event',
        'recurrenceable_id' => $event->id,
        'occurs_at' => $occursAt,
        'is_cancelled' => true,
    ]);
});

test('delete all recurring events', function () {
    $user = User::factory()->create();

    $event = Event::factory()->for($user)->create(['rrule' => 'FREQ=WEEKLY']);

    $exception = RecurrenceException::factory()->for($event, 'recurrenceable')->create([
        'occurs_at' => $event->starts_at->addWeek()->toDateString(),
        'overrides' => [],
    ]);

    $data = DestroyEventDataFactory::make(['scope' => 'all']);

    DestroyEvent::make()->handle($event, $data);

    assertDatabaseMissing('events', ['id' => $event->id]);
    assertDatabaseMissing('recurrence_exceptions', ['id' => $exception->id]);
});

test('delete current and future events', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create(['rrule' => 'FREQ=DAILY']);

    $occursAt = $event->starts_at->addWeek();

    $exception = RecurrenceException::factory()->for($event, 'recurrenceable')->create([
        'occurs_at' => $occursAt->addDays(2)->toDateString(),
        'overrides' => [],
    ]);

    $data = DestroyEventDataFactory::make([
        'date' => $occursAt->addDay()->toDateString(),
        'occursAt' => $occursAt->toDateString(),
        'scope' => 'following',
    ]);

    DestroyEvent::make()->handle($event, $data);

    $until = $occursAt->endOfDay()->format('Ymd\THis\Z');

    assertDatabaseHas('events', [
        'id' => $event->id,
        'rrule' => "FREQ=DAILY;UNTIL={$until}",
    ]);

    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'event',
        'recurrenceable_id' => $event->id,
        'occurs_at' => $occursAt->toDateString(),
        'is_cancelled' => true,
    ]);

    assertDatabaseMissing('recurrence_exceptions', [
        'recurrenceable_type' => 'event',
        'recurrenceable_id' => $event->id,
        'occurs_at' => $exception->occurs_at,
    ]);
});
