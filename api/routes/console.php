<?php

declare(strict_types=1);

use App\Console\Commands\PrunePushSubscriptionsCommand;
use App\Console\Commands\PruneUserChallengesCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(PruneUserChallengesCommand::class)->hourly();
Schedule::command(PrunePushSubscriptionsCommand::class)->daily();
