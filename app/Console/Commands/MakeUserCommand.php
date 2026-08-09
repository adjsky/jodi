<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Identity\Models\User;
use App\Support\Commands\JodiCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Contracts\Console\PromptsForMissingInput;

#[Signature('jodi:make:user {email} {--name=John Doe}')]
#[Description('Create a user.')]
class MakeUserCommand extends JodiCommand implements PromptsForMissingInput
{
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
