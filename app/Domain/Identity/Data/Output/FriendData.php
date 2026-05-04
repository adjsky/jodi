<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data\Output;

use App\Domain\Identity\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FriendData extends Data
{
    public function __construct(
        public string $id,
        public string $email,
        public string $name
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            $user->sqid,
            $user->email,
            $user->name
        );
    }
}
