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
// Acceder en cualquier proyecto hijo: http://localhost:PORT/ui-preview
if (app()->environment(['local', 'development', 'testing'])) {
    Route::get('/ui-preview', [PreviewController::class, 'index'])
        ->name('idcgames.ui.preview');
}

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
