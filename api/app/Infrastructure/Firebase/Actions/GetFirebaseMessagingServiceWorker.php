<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase\Actions;

use App\Infrastructure\Firebase\Configuration\FirebaseConfigurationProvider;
use App\Support\Actions\JodiAction;
use Illuminate\Http\Response;

class GetFirebaseMessagingServiceWorker extends JodiAction
{
    public function handle(): string
    {
        $config = FirebaseConfigurationProvider::web();

        return <<<EOF
importScripts("https://www.gstatic.com/firebasejs/12.19.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/12.19.0/firebase-messaging-compat.js");

firebase.initializeApp({
    apiKey: "{$config->apiKey}",
    authDomain: "{$config->authDomain}",
    projectId: "{$config->projectId}",
    storageBucket: "{$config->storageBucket}",
    messagingSenderId: "{$config->messagingSenderId}",
    appId: "{$config->appId}"
});

firebase.messaging();
EOF;
    }

    public function asController(): Response
    {
        return response($this->handle())
            ->header('Content-Type', 'application/javascript');
    }
}
