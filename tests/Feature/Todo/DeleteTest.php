<?php

declare(strict_types=1);

use App\Domain\Identity\Models\User;
use App\Domain\Recurrence\Models\RecurrenceException;
use App\Domain\Todo\Actions\DestroyTodo;
use App\Domain\Todo\Models\Todo;
use App\Domain\Todo\Models\TodoPosition;
use Tests\Factory\Data\DestroyTodoDataFactory;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('delete non-recurring todo', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create();

    $data = DestroyTodoDataFactory::make([
        'scope' => 'this',
    ]);

    DestroyTodo::make()->handle($todo, $data);

    assertDatabaseMissing('todos', ['id' => $todo->id]);
});

test('delete single recurring todo', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create(['rrule' => 'FREQ=WEEKLY']);
    $occursAt = $todo->scheduled_at->clone()->addWeek()->toDateString();

    $data = DestroyTodoDataFactory::make([
        'occursAt' => $occursAt,
        'date' => $occursAt,
        'scope' => 'this',
    ]);

    DestroyTodo::make()->handle($todo, $data);

    assertDatabaseHas('todos', ['id' => $todo->id]);
    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'todo',
        'recurrenceable_id' => $todo->id,
        'occurs_at' => $occursAt,
        'is_cancelled' => true,
    ]);
});

test('delete all recurring todos', function () {
    $user = User::factory()->create();

    $todo = Todo::factory()->for($user)->create(['rrule' => 'FREQ=WEEKLY']);

    RecurrenceException::factory()->for($todo, 'recurrenceable')->create([
        'occurs_at' => $todo->scheduled_at->clone()->addWeek()->toDateString(),
        'overrides' => [],
    ]);

    $data = DestroyTodoDataFactory::make(['scope' => 'all']);

    DestroyTodo::make()->handle($todo, $data);

    assertDatabaseMissing('todos', ['id' => $todo->id]);
    assertDatabaseMissing('recurrence_exceptions', [
        'recurrenceable_type' => 'todo',
        'recurrenceable_id' => $todo->id,
    ]);
});

test('delete current and future todos', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create(['rrule' => 'FREQ=DAILY']);

    $occursAt = $todo->scheduled_at->clone()->addWeek();

    $exception = RecurrenceException::factory()->for($todo, 'recurrenceable')->create([
        'occurs_at' => $occursAt->clone()->addDays(2)->toDateString(),
        'overrides' => [],
    ]);

    TodoPosition::factory()->for($todo)->create([
        'date' => $occursAt->clone()->addDays(2)->toDateString(),
        'position' => 1,
    ]);
    TodoPosition::factory()->for($todo)->create([
        'date' => $occursAt->clone()->addDays(3)->toDateString(),
        'position' => 1,
    ]);

    $data = DestroyTodoDataFactory::make([
        'date' => $occursAt->clone()->addDay()->toDateString(),
        'occursAt' => $occursAt->toDateString(),
        'scope' => 'following',
    ]);

    DestroyTodo::make()->handle($todo, $data);

    $until = $occursAt->clone()->endOfDay()->format('Ymd\THis\Z');

    assertDatabaseHas('todos', [
        'id' => $todo->id,
        'rrule' => "FREQ=DAILY;UNTIL={$until}",
    ]);

    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'todo',
        'recurrenceable_id' => $todo->id,
        'occurs_at' => $occursAt->toDateString(),
        'is_cancelled' => true,
    ]);

    assertDatabaseMissing('recurrence_exceptions', [
        'recurrenceable_type' => 'event',
        'recurrenceable_id' => $todo->id,
        'occurs_at' => $exception->occurs_at,
    ]);

    assertDatabaseMissing('todo_positions', ['todo_id' => $todo->id]);
});
