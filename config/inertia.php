<?php

declare(strict_types=1);

return [
    'pages' => [
        'ensure_pages_exist' => false,
        'paths' => [resource_path('js/pages')],
        'extensions' => ['svelte'],
    ],
    'testing' => [
        'ensure_pages_exist' => true,
    ],
    'expose_shared_prop_keys' => true,
    'history' => [
        'encrypt' => (bool) env('INERTIA_ENCRYPT_HISTORY', true),
    ],
];
