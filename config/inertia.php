<?php

declare(strict_types=1);

return [
    'ensure_pages_exist' => false,
    'history' => [
        'encrypt' => (bool) env('INERTIA_ENCRYPT_HISTORY', true),
    ],
];
