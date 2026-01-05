<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\UserOneTimePasswords;
use Illuminate\Console\Command;

class ClearOneTimePasswords extends Command
{
    protected $signature = 'jodi:clear:otp';

    protected $description = 'Delete expired one time passwords.';

    public function handle(): void
    {
        UserOneTimePasswords::whereDate('expires_at', '<=', now())->delete();
    }
}
