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

    'idc_api' => [
        // Legacy endpoint — kept for fallback/reference only
        'unilogin_url'      => env('IDC_UNILOGIN_URL', 'https://en.idcgames.com/unilogin/SoloLoginJuegoUnico.php'),
        'base_url'          => env('IDC_API_BASE_URL', 'https://en.idcgames.com'),
        // Auth service — server-to-server legacy token validation (replaces SoloLoginJuegoUnico)
        'auth_url'          => env('IDC_AUTH_URL', 'https://auth.idcgames.com'),
        'verify_token_path' => env('IDC_VERIFY_TOKEN_PATH', '/api/web/verify-legacy-token'),
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
