<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Permite que el front (Next) consuma esta API desde otro origen.
    | En dev: http://localhost:4000. En prod: https://nicolasngonzalez.com
    | (se setea con FRONTEND_URL en el .env).
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_filter([
        env('FRONTEND_URL'),                    // prod: https://nicolasngonzalez.com
        'https://www.nicolasngonzalez.com',     // variante con www (por si entran por ahí)
        'http://localhost:4000',
        'http://127.0.0.1:4000',
    ])),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Usamos auth por token (header Authorization), no cookies → no hace falta.
    'supports_credentials' => false,

];
