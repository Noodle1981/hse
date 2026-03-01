<?php

return [
    /*
    |--------------------------------------------------------------------------
    | EPP Exchange Mode
    |--------------------------------------------------------------------------
    |
    | 'mock' para desarrollo local (lee JSONs).
    | 'api' para producción (conecta con el endpoint real de EPP Exchange).
    |
    */
    'mode' => env('EPP_EXCHANGE_MODE', 'mock'),

    /*
    |--------------------------------------------------------------------------
    | Autenticación EPP Exchange (Solo para modo API)
    |--------------------------------------------------------------------------
    |
    */
    'api_url' => env('EPP_EXCHANGE_API_URL', 'https://api.eppexchange.com/v1'),
    'api_key' => env('EPP_EXCHANGE_API_KEY', ''),
];
