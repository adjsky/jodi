<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Identity\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class MakeUserCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'jodi:make:user {email} {--name=John Doe}';

    protected $description = 'Create a user.';

    public function handle(): void
    {
        User::create([
            'email' => $this->argument('email'),
            'name' => $this->option('name'),
            'preferences' => [
                ...config('jodi.preferences'),
                'locale' => config('app.locale'),
            ],
        ]);
    }
}
