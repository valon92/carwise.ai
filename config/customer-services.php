<?php

return [
    'margin' => 0.30,
    'costs' => [
        'diagnosis' => 7.99,
        'report' => 14.99,
        'predictive' => 19.99,
        'expert' => 2.99,
    ],
    // Internal spend per API. Never send these values to the client.
    'api_coins' => [
        'diagnosis' => 7.99,
        'vision' => 1.49,
        'whisper' => 0.99,
        'video' => 2.49,
    ],
    // USD per 1M tokens. Used only to price a real model call. Never send to the client.
    'token_rates' => [
        'gpt-4o' => ['input' => 2.50, 'output' => 10.00],
        'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
        'gpt-4.1' => ['input' => 2.00, 'output' => 8.00],
        'default' => ['input' => 2.50, 'output' => 10.00],
    ],
    'whisper_per_minute' => 0.006,
];
