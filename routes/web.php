<?php

use Illuminate\Support\Facades\Route;
use IDCGames\UI\Http\Controllers\AuthController;
use IDCGames\UI\Http\Controllers\PreviewController;

/*
|--------------------------------------------------------------------------
| IDCGames UI — Auth Routes (compartidas en todos los proyectos)
|--------------------------------------------------------------------------
| Estos son los endpoints de auth que cada proyecto hereda al instalar
| el package. Si un proyecto necesita override, puede redefinir las rutas
| en su propio routes/web.php después de cargar el package.
*/

// ── UI Preview (solo entornos no-producción) ────────────────────────────
if (app()->environment(['local', 'development', 'testing'])) {
    Route::get('/ui-preview', [PreviewController::class, 'index'])
        ->name('idcgames.ui.preview');
}

// ── IDC Auth Proxy ───────────────────────────────────────────────────────
// Proxea /idc-auth/* y /social/* al auth server para evitar CORS.
// El browser usa forum.idcgames.com/idc-auth como base (mismo origen).
// El callback OAuth (Google, etc.) va directo a auth.idcgames.com — no pasa por aquí.
// IDC_AUTH_URL        = https://auth.idcgames.com  (en .env de cada proyecto)
// IDC_AUTH_PUBLIC_URL = https://{proyecto}.idcgames.com/idc-auth
Route::middleware('web')->group(function () {
    $makeProxy = function (string $prefix = '') {
        $target = config('services.idc_auth.url', 'https://auth.idcgames.com');
        return function (string $path) use ($target, $prefix) {
            $url = rtrim($target, '/') . '/' . ltrim($prefix . '/' . $path, '/')
                 . (request()->getQueryString() ? '?' . request()->getQueryString() : '');

            $response = \Illuminate\Support\Facades\Http::withHeaders(
                collect(request()->headers->all())
                    ->except(['host', 'content-length', 'origin', 'referer'])
                    ->map(fn($v) => $v[0])
                    ->toArray()
            )->withOptions(['verify' => false, 'allow_redirects' => false, 'timeout' => 30])
             ->send(request()->method(), $url, ['body' => request()->getContent()]);

            return response($response->body(), $response->status())
                ->header('Access-Control-Allow-Origin', request()->header('Origin', '*'))
                ->header('Access-Control-Allow-Credentials', 'true')
                ->withHeaders(
                    collect($response->headers())
                        ->except(['set-cookie', 'transfer-encoding'])
                        ->map(fn($v) => is_array($v) ? $v[0] : $v)
                        ->toArray()
                );
        };
    };

    // /idc-auth/api/web/login  →  auth.idcgames.com/api/web/login
    Route::any('/idc-auth/{path}', $makeProxy())->where('path', '.*');
    // /social/login/GP         →  auth.idcgames.com/social/login/GP
    Route::any('/social/{path}',   $makeProxy('social'))->where('path', '.*');
});

Route::middleware('web')->group(function () {

    // ── Login / Logout ──────────────────────────────────────
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->middleware('guest')
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('guest')
        ->name('login.post');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    // ── API Auth (para SPAs / Inertia) ──────────────────────
    Route::prefix('api/v1')->group(function () {

        Route::post('/login', [AuthController::class, 'apiLogin'])
            ->name('api.login');

        Route::post('/logout/{username}', [AuthController::class, 'apiLogout'])
            ->middleware('auth:sanctum')
            ->name('api.logout');

        Route::get('/user', [AuthController::class, 'currentUser'])
            ->middleware('auth:sanctum')
            ->name('api.user');
    });
});
