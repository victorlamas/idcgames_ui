<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IDCGames Platform URLs
    |--------------------------------------------------------------------------
    | URLs de los servicios extra de IDCGames. Se usan en el NavBar
    | para los links y en el middleware de validación del idc_token.
    */

    'services' => [
        'gifts' => [
            'url'   => env('IDCGAMES_GIFTS_URL', 'https://gifts.idcgames.com'),
            'label' => 'Gifts',
            'icon'  => 'gift',
        ],
        'gamer' => [
            'url'   => env('IDCGAMES_GAMER_URL', 'https://gamer.idcgames.com'),
            'label' => 'Gamer',
            'icon'  => 'gamepad',
        ],
        'forum' => [
            'url'   => env('IDCGAMES_FORUM_URL', 'https://forum.idcgames.com'),
            'label' => 'Forum',
            'icon'  => 'forum',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IDCGames Central Auth API
    |--------------------------------------------------------------------------
    | Endpoint del sistema central IDC que valida el idc_token
    | y devuelve los datos del usuario (email, etc.).
    */

    /*
    |--------------------------------------------------------------------------
    | /idc-auth browser proxy
    |--------------------------------------------------------------------------
    | En producción (support, mud, etc.) el proxy suele hacerlo nginx (ver
    | deploy/nginx-idc-auth-proxy.conf.example). Pon IDC_AUTH_LARAVEL_PROXY=false
    | para no registrar la ruta PHP duplicada.
    */
    'idc_auth' => [
        'laravel_proxy' => env('IDC_AUTH_LARAVEL_PROXY', true),
    ],

    'idc_api' => [
        // Auth service — servidor canónico de validación de tokens IDC (server-side, no CORS)
        'auth_url'          => env('IDC_AUTH_URL', 'https://auth.idcgames.com'),
        // Path del endpoint de verificación server-to-server sobre auth_url
        'verify_token_path' => env('IDC_VERIFY_TOKEN_PATH', '/api/web/verify-token'),
        // Clave compartida para llamadas servidor-a-servidor (nunca expuesta al browser)
        // Se envía como header X-Internal-Key. Debe coincidir con la del auth server.
        'internal_key'      => env('INTERNAL_API_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session / Sanctum — Dominio compartido
    |--------------------------------------------------------------------------
    | Para que la cookie de sesión funcione en todos los subdominios,
    | SESSION_DOMAIN debe ser ".idcgames.com" en cada proyecto hijo.
    */

    'session_domain'    => env('SESSION_DOMAIN', '.idcgames.com'),
    'sanctum_domains'   => env('SANCTUM_STATEFUL_DOMAINS', 'gifts.idcgames.com,gamer.idcgames.com,forum.idcgames.com,localhost'),

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    */

    'app_name' => env('APP_NAME', 'IDCGames'),
    'logo_url' => env('IDCGAMES_LOGO_URL', '/images/idcgames-logo.png'),

];
