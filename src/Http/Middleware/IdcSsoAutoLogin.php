<?php

namespace IDCGames\UI\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * IdcSsoAutoLogin — Middleware
 *
 * Lee las cookies de sesión de IDCGames.com (dominio .idcgames.com) y
 * auto-crea/loguea al usuario en el proyecto hijo sin necesidad de
 * que el usuario rellene un formulario de login.
 *
 * Cookies que lee del dominio raíz:
 *   id    → useridc (ID del usuario en IDCGames.com)
 *   nick  → nickname
 *   token → idc_token (token de la API central)
 *
 * Comportamiento:
 *   - Si el usuario ya está autenticado → pasa sin hacer nada
 *   - Si hay cookies IDC válidas → busca user por useridc, lo crea si no existe,
 *     lo autentica via Auth::login()
 *   - Si no hay cookies → pasa sin hacer nada (el usuario accede como invitado)
 *
 * Uso en rutas:
 *   Route::middleware('idc.sso.auto')->group(function () { ... });
 *
 * Registro en el ServiceProvider:
 *   $this->app['router']->aliasMiddleware('idc.sso.auto', IdcSsoAutoLogin::class);
 */
class IdcSsoAutoLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        // ── Ya autenticado — no hacer nada ─────────────────────
        if (Auth::check()) {
            return $next($request);
        }

        // ── Leer cookies IDC ───────────────────────────────────
        $useridc  = $request->cookie('id')    ?? null;
        $nickname = $request->cookie('nick')  ?? null;
        $idcToken = $request->cookie('token') ?? null;

        // Sin cookies válidas — pasar como invitado
        if (! $useridc || ! $nickname || strlen((string) $useridc) < 1) {
            return $next($request);
        }

        $useridc  = (string) $useridc;
        $nickname = urldecode((string) $nickname);

        try {
            $userModel = config('auth.providers.users.model', \App\Models\User::class);

            // ── Buscar por useridc ─────────────────────────────
            $user = $userModel::where('useridc', $useridc)->first();

            if (! $user) {
                // ── Crear usuario automáticamente ─────────────
                // El proyecto hijo puede escuchar el evento Registered si necesita
                // hacer trabajo adicional (crear perfil gamer, etc.)
                $user = $userModel::create([
                    'useridc'   => $useridc,
                    'username'  => $nickname,
                    'nickname'  => $nickname,
                    'name'      => $nickname,
                    'email'     => null,          // se rellena luego desde IDC API
                    'password'  => bcrypt(str()->random(32)),  // contraseña aleatoria
                    'idc_token' => $idcToken,
                ]);

                Log::info("[IDCGames SSO] Created new user from cookies: useridc={$useridc}, nick={$nickname}");
            } else {
                // ── Actualizar token si cambió ─────────────────
                if ($idcToken && $user->idc_token !== $idcToken) {
                    $user->idc_token = $idcToken;
                    $user->save();
                }
            }

            // ── Autenticar (sesión web normal) ─────────────────
            Auth::login($user, remember: true);
            $request->session()->regenerate();

            Log::info("[IDCGames SSO] Auto-logged in user: {$nickname} (useridc={$useridc})");

        } catch (\Throwable $e) {
            // No interrumpir la request si el SSO falla
            Log::warning("[IDCGames SSO] Auto-login failed: " . $e->getMessage());
        }

        return $next($request);
    }
}
