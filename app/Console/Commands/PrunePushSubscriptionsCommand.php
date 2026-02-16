<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\PushSubscription;
use Illuminate\Console\Command;

class PrunePushSubscriptionsCommand extends Command
{
    protected $signature = 'jodi:prune:push-subscriptions';

    protected $description = 'Delete stale push subscriptions.';

    public function handle(): void
    {
        PushSubscription::where('updated_at', '<', now()->subDays(60))->delete();
    }
}
