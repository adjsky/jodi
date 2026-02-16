<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class FirebaseServiceWorkerController
{
    public function serve()
    {
        $config = config('services.firebase');

        $js = <<<EOD
importScripts("https://www.gstatic.com/firebasejs/12.9.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/12.9.0/firebase-messaging-compat.js");

firebase.initializeApp({
    apiKey: "{$config['api_key']}",
    authDomain: "{$config['auth_domain']}",
    projectId: "{$config['project_id']}",
    storageBucket: "{$config['storage_bucket']}",
    messagingSenderId: "{$config['messaging_sender_id']}",
    appId: "{$config['app_id']}"
});

firebase.messaging();
EOD;

        return response($js)->header('Content-Type', 'application/javascript');
    }
}
