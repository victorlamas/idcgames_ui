<?php

namespace IDCGames\UI;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use IDCGames\UI\View\Components\Layout;
use IDCGames\UI\View\Components\Navbar;
use IDCGames\UI\View\Components\Footer;
use IDCGames\UI\Support\IdcAuthBrowserUrl;

class IDCGamesUIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/idcgames-ui.php',
            'idcgames-ui'
        );

        $this->app->booted(function (): void {
            $existing = config('services.idc_auth');
            if (! is_array($existing)) {
                $existing = [];
            }

            $appUrl = rtrim((string) config('app.url', ''), '/');
            $publicProxy = $appUrl !== '' ? $appUrl.'/idc-auth' : null;

            $merged = array_merge([
                'url' => env('IDC_AUTH_URL', 'https://auth.idcgames.com'),
                'public_url' => env('IDC_AUTH_PUBLIC_URL', $publicProxy),
                'widget_url' => env('IDC_AUTH_WIDGET_URL')
                    ?: env('IDC_AUTH_PUBLIC_URL', $publicProxy)
                    ?: env('IDC_AUTH_URL', 'https://auth.idcgames.com'),
            ], $existing);

            $merged['widget_url'] = IdcAuthBrowserUrl::resolve(
                $merged['widget_url'] ?? $merged['public_url'] ?? null,
                $appUrl
            );

            config(['services.idc_auth' => $merged]);
        });
    }

    public function boot(): void
    {
        // ── Views ──────────────────────────────────────────────
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'idcgames');

        // ── Blade Components ───────────────────────────────────
        Blade::component('idcgames::components.layout', Layout::class);
        Blade::component('idcgames::components.navbar', Navbar::class);
        Blade::component('idcgames::components.footer', Footer::class);

        // Aliases cortos: <x-idcgames::layout>, <x-idcgames::navbar>, etc.
        Blade::componentNamespace('IDCGames\\UI\\View\\Components', 'idcgames');

        // ── Routes ─────────────────────────────────────────────
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // ── Middleware ─────────────────────────────────────────
        // idc.token  — protege rutas que requieren un idc_token válido (API)
        $this->app['router']->aliasMiddleware(
            'idc.token',
            \IDCGames\UI\Http\Middleware\ValidateIDCToken::class
        );

        // idc.sso.auto — auto-login desde cookies de sesión IDCGames.com
        // Se aplica en rutas públicas: no bloquea, pero logea si hay cookies
        $this->app['router']->aliasMiddleware(
            'idc.sso.auto',
            \IDCGames\UI\Http\Middleware\IdcSsoAutoLogin::class
        );

        // ── Publishes ──────────────────────────────────────────
        if ($this->app->runningInConsole()) {
            // php artisan vendor:publish --tag=idcgames-ui-views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/idcgames'),
            ], 'idcgames-ui-views');

            // php artisan vendor:publish --tag=idcgames-ui-config
            $this->publishes([
                __DIR__ . '/../config/idcgames-ui.php' => config_path('idcgames-ui.php'),
            ], 'idcgames-ui-config');

            // php artisan vendor:publish --tag=idcgames-ui-assets
            $this->publishes([
                __DIR__ . '/../resources/css' => resource_path('css/idcgames'),
                __DIR__ . '/../resources/js'  => resource_path('js/idcgames'),
            ], 'idcgames-ui-assets');
        }
    }
}
