<?php

return [
    'base_url' => env('PRICELABS_BASE_URL', 'https://setup.easyrentit.com/api/pricelabs'),
    'timeout' => (int) env('PRICELABS_TIMEOUT', 300),
    'retry' => [
        'times' => (int) env('PRICELABS_RETRY_TIMES', 0),
        'sleep' => (int) env('PRICELABS_RETRY_SLEEP', 100),
    ],
];

