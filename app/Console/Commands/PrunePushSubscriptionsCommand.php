<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Identity\Models\PushSubscription;
use App\Support\Commands\JodiCommand;

class PrunePushSubscriptionsCommand extends JodiCommand
{
    protected $signature = 'jodi:prune:push-subscriptions';

    protected $description = 'Delete stale push subscriptions.';

    public function handle(): void
    {
        PushSubscription::where('updated_at', '<', now()->subDays(60))->delete();
    }
}
