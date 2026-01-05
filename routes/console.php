<?php

declare(strict_types=1);

use App\Console\Commands\ClearOneTimePasswords;
use App\Console\Commands\RemindEventsCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(RemindEventsCommand::class)->everyMinute()->withoutOverlapping();
Schedule::command(ClearOneTimePasswords::class)->hourly()->withoutOverlapping();
