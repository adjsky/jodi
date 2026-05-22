<?php

declare(strict_types=1);

use App\Domain\Identity\Models\User;
use App\Domain\Recurrence\Models\RecurrenceException;
use App\Domain\Todo\Actions\UpdateTodo;
use App\Domain\Todo\Models\Category;
use App\Domain\Todo\Models\Todo;
use App\Domain\Todo\Models\TodoPosition;
use Carbon\Carbon;
use Tests\Factory\Data\UpdateTodoDataFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('regular update', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create();

    $data = UpdateTodoDataFactory::make();

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseHas('todos', [
        'id' => $todo->id,
        'user_id' => $user->id,
        'title' => $data->title,
        'description' => $data->description,
        'color' => $data->color,
        'rrule' => $data->rrule,
        'scheduled_at' => Carbon::parse($data->scheduledAt)->toDateTimeString(),
        'has_time' => $data->hasTime,
        'notify_at' => null,
        'notify_status' => null,
    ]);
});

test('update preserves notify_status if notify_at is the same', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create([
        'notify_at' => now(),
        'notify_status' => 'sent',
    ]);

    $data = UpdateTodoDataFactory::make(['notify_at' => $todo->notify_at->toISOString()]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseHas('todos', [
        'id' => $todo->id,
        'notify_status' => $todo->notify_status,
    ]);
});

test('update resets notify_status if notify_at is different', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create([
        'notify_status' => 'sent',
    ]);

    $data = UpdateTodoDataFactory::make(['notifyAt' => '2030-01-01T12:34:56Z']);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseHas('todos', [
        'id' => $todo->id,
        'notify_status' => 'waiting',
    ]);
});

test('recurring local update applies an exception instead of modifying the original todo', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=WEEKLY';
    $todo = Todo::factory()->for($user)->create([
        'rrule' => $rrule,
    ]);
    $occursAt = $todo->scheduled_at->clone()->addWeek()->toDateString();

    $data = UpdateTodoDataFactory::make([
        'rrule' => $rrule,
        'scheduledAt' => $occursAt,
        'occursAt' => $occursAt,
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseHas('todos', [
        'id' => $todo->id,
        'user_id' => $user->id,
        'title' => $todo->title,
        'description' => $todo->description,
        'color' => $todo->color,
        'rrule' => $todo->rrule,
        'scheduled_at' => $todo->scheduled_at->toDateTimeString(),
        'notify_at' => null,
        'notify_status' => null,
    ]);

    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'todo',
        'recurrenceable_id' => $todo->id,
        'occurs_at' => $occursAt,
        'is_cancelled' => false,
        'overrides' => json_encode([
            'title' => $data->title,
            'description' => $data->description,
        ]),
    ]);
});

test('recurring global update modifies the original todo', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=WEEKLY';
    $todo = Todo::factory()->for($user)->create([
        'rrule' => $rrule,
    ]);
    $occursAt = $todo->scheduled_at->clone()->addWeek();

    $data = UpdateTodoDataFactory::make([
        'rrule' => $rrule,
        'scheduledAt' => $occursAt->clone()->addDay()->toDateString(),
        'occursAt' => $occursAt->toDateString(),
        'scope' => 'all',
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseHas('todos', [
        'id' => $todo->id,
        'user_id' => $user->id,
        'title' => $data->title,
        'description' => $data->description,
        'color' => $data->color,
        'rrule' => $data->rrule,
        'scheduled_at' => Carbon::parse($data->scheduledAt)->toDateTimeString(),
        'notify_at' => null,
        'notify_status' => null,
    ]);

    assertDatabaseMissing('recurrence_exceptions', [
        'recurrenceable_type' => 'todos',
        'recurrenceable_id' => $todo->id,
    ]);
});

test('recurring global update preserves original dates', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=WEEKLY';
    $todo = Todo::factory()->for($user)->create([
        'rrule' => $rrule,
    ]);
    $occursAt = $todo->scheduled_at->clone()->addWeek();

    $data = UpdateTodoDataFactory::make([
        'rrule' => $rrule,
        'scheduledAt' => $occursAt->toISOString(),
        'occursAt' => $occursAt->toDateString(),
        'scope' => 'all',
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseHas('todos', [
        'id' => $todo->id,
        'scheduled_at' => $todo->scheduled_at->toDateTimeString(),
        'notify_at' => $todo->notify_at?->toDateTimeString(),
    ]);
});

test('recurring global update resets existing exceptions', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=WEEKLY';
    $todo = Todo::factory()->for($user)->create(['rrule' => $rrule]);
    $occursAt = $todo->scheduled_at->clone()->addWeek();

    RecurrenceException::factory()->for($todo, 'recurrenceable')->create([
        'occurs_at' => $occursAt->toDateString(),
        'overrides' => ['notify_status' => 'sent'],
    ]);
    RecurrenceException::factory()->for($todo, 'recurrenceable')->create([
        'occurs_at' => $occursAt->clone()->addWeek()->toDateString(),
    ]);

    $data = UpdateTodoDataFactory::make([
        'rrule' => $rrule,
        'occursAt' => $occursAt->toDateString(),
        'scope' => 'all',
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseMissing('recurrence_exceptions', [
        'recurrenceable_type' => 'todo',
        'recurrenceable_id' => $todo->id,
        'occurs_at' => $occursAt->clone()->addWeek()->toDateString(),
    ]);

    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'todo',
        'recurrenceable_id' => $todo->id,
        'occurs_at' => $occursAt->toDateString(),
        'overrides' => json_encode(['notify_status' => 'sent']),
    ]);
});

test('todo splits when changing rrule', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $todo = Todo::factory()->for($user)->create(['rrule' => $rrule]);

    $data = UpdateTodoDataFactory::make([
        'rrule' => 'FREQ=WEEKLY',
        'occursAt' => $todo->scheduled_at->clone()->addDays(4),
        'scope' => 'all',
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    $scheduledAt = Carbon::parse($data->scheduledAt);
    $until = $scheduledAt->clone()->subDay()->endOfDay()->format('Ymd\THisT');

    assertDatabaseHas('todos', [
        'id' => $todo->id,
        'rrule' => "FREQ=DAILY;UNTIL={$until}",
    ]);

    assertDatabaseHas('todos', [
        'id' => $todo->id + 1,
        'scheduled_at' => $scheduledAt->toDateTimeString(),
        'rrule' => 'FREQ=WEEKLY',
    ]);
});

test('todo splits when resetting rrule', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create(['rrule' => 'FREQ=DAILY']);

    $data = UpdateTodoDataFactory::make([
        'rrule' => null,
        'occursAt' => $todo->scheduled_at->clone()->addDays(4),
        'scope' => 'all',
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseCount('todos', 2);
});

test('todo does not split when rrule is the same', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $todo = Todo::factory()->for($user)->create(['rrule' => $rrule]);

    $data = UpdateTodoDataFactory::make([
        'rrule' => $rrule,
        'occursAt' => $todo->scheduled_at->clone()->addDays(4),
        'scope' => 'all',
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseCount('todos', 1);
});

test('completion is preserved after splitting', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $todo = Todo::factory()->for($user)->create([
        'scheduled_at' => '2026-01-01T12:34:56Z',
        'rrule' => $rrule,
    ]);

    RecurrenceException::factory()->for($todo, 'recurrenceable')->create([
        'occurs_at' => '2026-01-02',
        'overrides' => ['completed_at' => '2026-01-02:11:22:33Z'],
    ]);
    RecurrenceException::factory()->for($todo, 'recurrenceable')->create([
        'occurs_at' => '2026-01-05',
        'overrides' => ['completed_at' => '2026-01-05:11:22:33Z'],
    ]);

    $data = UpdateTodoDataFactory::make([
        'rrule' => 'FREQ=WEEKLY',
        'scheduledAt' => '2026-01-05T12:34:56Z',
        'occursAt' => '2026-01-05',
        'scope' => 'all',
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'todo',
        'recurrenceable_id' => $todo->id,
        'occurs_at' => '2026-01-02',
        'overrides' => json_encode([
            'completed_at' => '2026-01-02:11:22:33Z',
        ]),
    ]);
    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'todo',
        'recurrenceable_id' => $todo->id + 1,
        'occurs_at' => '2026-01-05',
        'overrides' => json_encode([
            'completed_at' => '2026-01-05:11:22:33Z',
        ]),
    ]);
});

test('exceptions are transferred and reset when changing rrule', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $todo = Todo::factory()->for($user)->create(['rrule' => $rrule]);
    $occursAt = $todo->scheduled_at->clone()->addDays(4);

    RecurrenceException::factory()->for($todo, 'recurrenceable')->create([
        'occurs_at' => $occursAt->toDateString(),
        'overrides' => ['notify_status' => 'sent'],
    ]);
    RecurrenceException::factory()->for($todo, 'recurrenceable')->create([
        'occurs_at' => $occursAt->clone()->subDays(2)->toDateString(),
    ]);

    $data = UpdateTodoDataFactory::make([
        'scheduledAt' => $occursAt->toISOString(),
        'notifyAt' => $occursAt->clone()->subHour()->toISOString(),
        'rrule' => 'FREQ=WEEKLY',
        'occursAt' => $occursAt->toDateString(),
        'scope' => 'all',
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseMissing('recurrence_exceptions', [
        'recurrenceable_type' => 'todo',
        'recurrenceable_id' => $todo->id,
    ]);

    assertDatabaseHas('recurrence_exceptions', [
        'recurrenceable_type' => 'todo',
        'recurrenceable_id' => $todo->id + 1,
    ]);
});

test('positions are transferred when changing rrule', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $todo = Todo::factory()->for($user)->create([
        'rrule' => $rrule,
        'scheduled_at' => '2026-01-01T12:34:56Z',
    ]);

    TodoPosition::factory()->for($todo)->create([
        'date' => '2026-01-01',
        'position' => 1,
    ]);
    TodoPosition::factory()->for($todo)->create([
        'date' => '2026-01-02',
        'position' => 1,
    ]);

    $data = UpdateTodoDataFactory::make([
        'scheduledAt' => '2026-01-02T12:34:56Z',
        'rrule' => 'FREQ=WEEKLY',
        'occursAt' => '2026-01-02',
        'scope' => 'all',
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseMissing('todo_positions', [
        'todo_id' => $todo->id,
        'date' => '2026-01-02',
    ]);

    assertDatabaseHas('todo_positions', [
        'todo_id' => $todo->id,
        'date' => '2026-01-01',
    ]);
    assertDatabaseHas('todo_positions', [
        'todo_id' => $todo->id + 1,
        'date' => '2026-01-02',
    ]);
});

test('positions are reset when changing rrule and date', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $todo = Todo::factory()->for($user)->create([
        'rrule' => $rrule,
        'scheduled_at' => '2026-01-01T12:34:56Z',
    ]);

    TodoPosition::factory()->for($todo)->create([
        'date' => '2026-01-02',
        'position' => 1,
    ]);

    $data = UpdateTodoDataFactory::make([
        'scheduledAt' => '2026-01-03T12:34:56Z',
        'rrule' => 'FREQ=WEEKLY',
        'occursAt' => '2026-01-02',
        'scope' => 'all',
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseMissing('todo_positions', ['todo_id' => $todo->id + 1]);
});

test('positions are reset when changing rrule and category', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $todo = Todo::factory()->for($user)->create([
        'rrule' => $rrule,
        'scheduled_at' => '2026-01-01T12:34:56Z',
    ]);

    $category = Category::factory()->for($user)->create();

    TodoPosition::factory()->for($todo)->create([
        'date' => '2026-01-02',
        'position' => 1,
    ]);

    $data = UpdateTodoDataFactory::make([
        'scheduledAt' => '2026-01-02T12:34:56Z',
        'rrule' => 'FREQ=WEEKLY',
        'occursAt' => '2026-01-02',
        'scope' => 'all',
        'categoryId' => $category->id,
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseMissing('todo_positions', ['todo_id' => $todo->id]);
});

test('positions are reset when moving an occurrence to another date', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $todo = Todo::factory()->for($user)->create([
        'rrule' => $rrule,
        'scheduled_at' => '2026-01-01T12:34:56Z',
    ]);

    TodoPosition::factory()->for($todo)->create([
        'date' => '2026-01-02',
        'position' => 1,
    ]);

    $data = UpdateTodoDataFactory::make([
        'scheduledAt' => '2026-01-03T12:34:56Z',
        'rrule' => 'FREQ=WEEKLY',
        'occursAt' => '2026-01-02',
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseMissing('todo_positions', ['todo_id' => $todo->id]);
});

test('positions are reset when changing an occurrence category', function () {
    $user = User::factory()->create();

    $rrule = 'FREQ=DAILY';
    $todo = Todo::factory()->for($user)->create([
        'rrule' => $rrule,
        'scheduled_at' => '2026-01-01T12:34:56Z',
    ]);

    $category = Category::factory()->for($user)->create();

    TodoPosition::factory()->for($todo)->create([
        'date' => '2026-01-02',
        'position' => 1,
    ]);

    $data = UpdateTodoDataFactory::make([
        'scheduledAt' => '2026-01-02T12:34:56Z',
        'rrule' => 'FREQ=WEEKLY',
        'occursAt' => '2026-01-02',
        'categoryId' => $category->id,
    ]);

    UpdateTodo::make()->handle($todo, $data, 'Europe/Moscow');

    assertDatabaseMissing('todo_positions', ['todo_id' => $todo->id]);
});
