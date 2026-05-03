<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Identity\Models\UserOneTimePasswords;
use App\Support\Commands\JodiCommand;

class PruneOneTimePasswordsCommand extends JodiCommand
{
    protected $signature = 'jodi:prune:otp';

    protected $description = 'Delete expired one time passwords.';

    public function handle(): void
    {
        UserOneTimePasswords::where('expires_at', '<=', now())->delete();
    }
}
