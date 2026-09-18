<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Identity\Models\UserChallenge;
use App\Support\Commands\JodiCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('jodi:prune:otp')]
#[Description('Delete expired user challenges.')]
class PruneUserChallengesCommand extends JodiCommand
{
    public function handle(): void
    {
        UserChallenge::where('expires_at', '<=', now())->delete();
    }
}
