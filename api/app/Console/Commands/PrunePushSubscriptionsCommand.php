<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Identity\Models\PushSubscription;
use App\Support\Commands\JodiCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('jodi:prune:push-subscriptions')]
#[Description('Delete stale push subscriptions.')]
class PrunePushSubscriptionsCommand extends JodiCommand
{
    public function handle(): void
    {
        PushSubscription::where('updated_at', '<', now()->subDays(60))->delete();
    }
}
