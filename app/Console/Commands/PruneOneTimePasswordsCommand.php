<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Identity\Models\UserOneTimePasswords;
use App\Support\Commands\JodiCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('jodi:prune:otp')]
#[Description('Delete expired one time passwords.')]
class PruneOneTimePasswordsCommand extends JodiCommand
{
    public function handle(): void
    {
        UserOneTimePasswords::where('expires_at', '<=', now())->delete();
    }
}
