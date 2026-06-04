<?php

namespace IDCGames\UI\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * ValidateIDCToken Middleware
 *
 * Verifica que el request tenga un idc_token válido o que el usuario
 * ya esté autenticado via Sanctum/sesión.
 *
 * Uso en rutas:
 *   Route::middleware('idc.token')->group(function () { ... });
 *
 * El middleware permite el paso si:
 *   1. El usuario ya está autenticado (Sanctum token o sesión)
 *   2. Se envía un idc_token válido en el header X-IDC-Token o en el body
 *
 * Si ninguna condición se cumple, devuelve 401.
 */
class ValidateIDCToken
{
    public function handle(Request $request, Closure $next): Response
    {
        // ── Ya autenticado ─────────────────────────────────────────────
        if (Auth::check()) {
            return $next($request);
        }

        // ── idc_token en header o body ─────────────────────────────────
        $idcToken = $request->header('X-IDC-Token')
            ?? $request->input('idctoken')
            ?? $request->input('idc_token')
            ?? null;

        if (! $idcToken || strlen($idcToken) < 20) {
            return $this->unauthorized($request, 'IDC token required');
        }

        // ── Intentar autenticar con el idc_token ───────────────────────
        $userModel = config('auth.providers.users.model', \App\Models\User::class);
        $user = $userModel::where('idc_token', $idcToken)->first();

        if (! $user) {
            // El token no está en BD local — podría ser un usuario nuevo
            // En ese caso el proyecto debe manejar el registro.
            Log::info('[IDCGames UI] ValidateIDCToken: token not found in local DB');
            return $this->unauthorized($request, 'Unknown IDC token');
        }

        // Autenticar sin sesión (solo para este request)
        Auth::login($user);

        return $next($request);
    }

    private function unauthorized(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 401);
        }

        return redirect()->route('login')
            ->with('error', 'You must be logged in to access this page.');
    }
}
