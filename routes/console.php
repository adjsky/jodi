<?php

declare(strict_types=1);

use App\Console\Commands\PruneOneTimePasswordsCommand;
use App\Console\Commands\RemindEventsCommand;
use App\Console\Commands\RemindTodosCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(RemindEventsCommand::class)->everyMinute()->withoutOverlapping();
Schedule::command(RemindTodosCommand::class)->everyMinute()->withoutOverlapping();
Schedule::command(PruneOneTimePasswordsCommand::class)->hourly()->withoutOverlapping();
