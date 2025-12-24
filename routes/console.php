<?php

declare(strict_types=1);

use App\Console\Commands\RemindEvents;
use Illuminate\Support\Facades\Schedule;

Schedule::command(RemindEvents::class)->everyMinute()->withoutOverlapping();
