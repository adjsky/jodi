<?php

declare(strict_types=1);

use App\Domain\Identity\Models\User;
use App\Domain\Todo\Actions\ListTodos;
use App\Domain\Todo\Models\Todo;
use Carbon\Carbon;

test('lists a non-recurring todo', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create([
        'scheduled_at' => '2030-01-01 18:00:00',
    ]);

    $todos = ListTodos::make()->handle(
        $user,
        '2030-01-01',
        Carbon::parse('2030-01-01 00:00:00'),
        Carbon::parse('2030-01-02 23:59:59')
    );

    expect($todos)->toHaveCount(1);
    expect($todos->first())->toMatchObject([
        'id' => $todo->id,
        'scheduledAt' => '2030-01-01 18:00:00',
    ]);
});

test('does not list a todo after it ends at the start of the day', function () {
    $user = User::factory()->create();
    Todo::factory()->for($user)->create([
        'scheduled_at' => '2030-01-01 18:00:00',
    ]);

    $todos = ListTodos::make()->handle(
        $user,
        '2030-01-01',
        Carbon::parse('2030-01-02 00:00:00'),
        Carbon::parse('2030-01-02 23:59:59')
    );

    expect($todos)->toBeEmpty();
});

test('lists a recurring occurrence', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create([
        'scheduled_at' => '2030-01-01 18:00:00',
        'rrule' => 'FREQ=WEEKLY',
    ]);

    $todos = ListTodos::make()->handle(
        $user,
        '2030-01-08',
        Carbon::parse('2030-01-08 00:00:00'),
        Carbon::parse('2030-01-08 23:59:59')
    );

    expect($todos)->toHaveCount(1);
    expect($todos[0])->toMatchObject([
        'id' => $todo->id,
        'scheduledAt' => '2030-01-08 18:00:00',
    ]);
});
