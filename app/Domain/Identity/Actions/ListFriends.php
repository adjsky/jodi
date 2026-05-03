<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Output\FriendData;
use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class ListFriends extends JodiAction
{
    public function handle(User $user): Collection
    {
        return FriendData::collect($user->friends()->get());
    }

    public function asController(): JsonResponse
    {
        $friends = $this->handle($this->user());

        return response()->json($friends);
    }
}
