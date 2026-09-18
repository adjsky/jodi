<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Output\AuthSessionData;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\JsonResponse;

class GetCurrentSession extends JodiAction
{
    public function asController(JodiRequest $request): JsonResponse
    {
        $user = $this->user();

        return response()->json(AuthSessionData::from($user, $request->deviceId()));
    }
}
