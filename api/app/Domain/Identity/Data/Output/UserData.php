<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Output;

use App\Domain\Identity\Models\User;
use App\Domain\Identity\ValueObjects\UserPreferences;
use App\Support\Data\JodiData;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UserData extends JodiData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public UserPreferences $preferences
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            $user->id,
            $user->name,
            $user->email,
            $user->preferences
        );
    }
}
