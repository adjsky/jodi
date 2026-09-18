<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase\Configuration;

use App\Support\Data\JodiDto;

class FirebaseWebConfiguration extends JodiDto
{
    public function __construct(
        public string $apiKey,
        public string $authDomain,
        public string $projectId,
        public string $storageBucket,
        public string $messagingSenderId,
        public string $appId,
    ) {}
}
