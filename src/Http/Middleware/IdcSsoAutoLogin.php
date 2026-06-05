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

        // ── Validar idc_token contra la API central IDC ────────
        // Sin esta verificación, cualquier cookie forjada daría acceso.
        if (! $idcToken || ! $this->verifyTokenWithApi((string) $idcToken, $useridc)) {
            Log::warning("[IDCGames SSO] Cookie token failed API verification for useridc={$useridc}");
            return $next($request);
        }

        try {
            $userModel = config('auth.providers.users.model', \App\Models\User::class);

            // ── Buscar por useridc ─────────────────────────────
            $user = $userModel::where('useridc', $useridc)->first();

            if (! $user) {
                // ── Crear usuario automáticamente ─────────────
                $user = $userModel::create([
                    'useridc'   => $useridc,
                    'username'  => $nickname,
                    'nickname'  => $nickname,
                    'name'      => $nickname,
                    'email'     => null,
                    'password'  => bcrypt(str()->random(32)),
                    'idc_token' => $idcToken,
                ]);

                Log::info("[IDCGames SSO] Created new user from cookies: useridc={$useridc}, nick={$nickname}");
            } else {
                // ── Actualizar token si cambió ─────────────────
                if ($user->idc_token !== $idcToken) {
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

    /**
     * Verifica el idc_token contra la API central de IDC.
     * Devuelve true solo si el token es válido y pertenece al useridc dado.
     * Se cachea 5 minutos para no golpear la API en cada request.
     */
    private function verifyTokenWithApi(string $token, string $useridc): bool
    {
        $cacheKey = 'idc_sso_token_' . sha1($token);

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($token, $useridc) {
            $url = config('idcgames-ui.idc_api.unilogin_url') . '?token=' . urlencode($token);

            try {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL            => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT        => 5,
                    CURLOPT_HTTPHEADER     => ['accept: */*'],
                ]);
                $response = curl_exec($curl);
                curl_close($curl);

                if (! $response) {
                    return false;
                }

                $data = json_decode($response);

                // La API IDC devuelve el iIDUsuario del token
                $apiUserId = $data->content->iIDUsuario ?? $data->iIDUsuario ?? null;

                return $apiUserId && (string) $apiUserId === $useridc;

            } catch (\Throwable $e) {
                Log::warning('[IDCGames SSO] Token API verification failed: ' . $e->getMessage());
                return false;
            }
        });
    }
}
