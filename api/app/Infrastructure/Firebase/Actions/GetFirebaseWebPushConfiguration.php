<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase\Actions;

use App\Infrastructure\Firebase\Configuration\FirebaseConfigurationProvider;
use App\Infrastructure\Firebase\Data\FirebaseWebPushConfigurationData;
use App\Support\Actions\JodiAction;
use Illuminate\Http\JsonResponse;

class GetFirebaseWebPushConfiguration extends JodiAction
{
    public function handle(): FirebaseWebPushConfigurationData
    {
        return new FirebaseWebPushConfigurationData(
            FirebaseConfigurationProvider::web(),
            FirebaseConfigurationProvider::vapidKey()
        );
    }

    public function asController(): JsonResponse
    {
        return response()->json($this->handle());
    }
}
