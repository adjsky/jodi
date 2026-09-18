<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase\Data;

use App\Infrastructure\Firebase\Configuration\FirebaseWebConfiguration;
use App\Support\Data\JodiData;

class FirebaseWebPushConfigurationData extends JodiData
{
    public function __construct(
        public FirebaseWebConfiguration $config,
        public string $vapidKey
    ) {}
}
