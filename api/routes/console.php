<?php

declare(strict_types=1);

use App\Console\Commands\PrunePushSubscriptionsCommand;
use App\Console\Commands\PruneUserChallengesCommand;
use App\Console\Commands\RemindEventsCommand;
use App\Console\Commands\RemindTodosCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(RemindEventsCommand::class)->everyMinute()->withoutOverlapping();
Schedule::command(RemindTodosCommand::class)->everyMinute()->withoutOverlapping();
Schedule::command(PruneUserChallengesCommand::class)->hourly();
Schedule::command(PrunePushSubscriptionsCommand::class)->daily();
