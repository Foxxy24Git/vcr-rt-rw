<?php

return [
    'host' => env('MIKROTIK_HOST', ''),
    'port' => (int) env('MIKROTIK_PORT', 8728),
    'user' => env('MIKROTIK_USER', ''),
    'pass' => env('MIKROTIK_PASS', ''),
    'ssl' => filter_var(env('MIKROTIK_SSL', false), FILTER_VALIDATE_BOOL),
    'timeout' => (int) env('MIKROTIK_TIMEOUT', 10),
];
