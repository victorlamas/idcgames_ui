<?php

namespace IDCGames\UI\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * IdcSsoService — Verificación canónica de tokens IDC
 *
 * Punto único para validar un idc_token contra auth.idcgames.com.
 * Usado por el middleware IdcSsoAutoLogin y por cualquier controlador
 * que necesite verificar un token (mud WebGameController, forum singleSign, etc.)
 *
 * Requiere INTERNAL_API_KEY en el .env de cada proyecto hijo.
 */
class IdcSsoService
{
    /**
     * Verifica un idc_token contra auth.idcgames.com (idcgames-ui.idc_api.verify_token_path,
     * por defecto /api/web/verify-token).
     *
     * @param  string  $token    idc_token a verificar
     * @param  string  $useridc  ID del usuario (para validación cruzada)
     * @return array|null  ['useridc' => string, 'nick' => string|null, 'email' => string|null]
     *                     o null si el token es inválido / el servicio no responde.
     */
    /**
     * Pick auth_token (JWT) when present, else legacy token cookie.
     */
    public static function tokenFromRequest(\Illuminate\Http\Request $request): string
    {
        $auth = trim((string) ($request->cookie('auth_token') ?? ''));
        if ($auth !== '') {
            try {
                return rawurldecode($auth);
            } catch (\Throwable) {
                return $auth;
            }
        }

        $legacy = trim((string) ($request->cookie('token') ?? ''));

        return $legacy !== '' ? rawurldecode($legacy) : '';
    }

    public static function verifyToken(string $token, string $useridc): ?array
    {
        $token = trim($token);
        if ($token !== '') {
            try {
                $token = rawurldecode($token);
            } catch (\Throwable) {
                // keep raw
            }
        }

        if (! $token || ! $useridc) {
            return null;
        }

        $cacheKey = 'idc_sso_token_' . sha1($token . '|' . $useridc);

        return Cache::remember($cacheKey, 300, function () use ($token, $useridc) {
            return static::callVerifyEndpoint($token, $useridc);
        });
    }

    /**
     * Igual que verifyToken() pero sin caché.
     * Útil para forzar revalidación (ej: después de un login manual).
     */
    public static function verifyTokenFresh(string $token, string $useridc): ?array
    {
        $cacheKey = 'idc_sso_token_' . sha1($token . '|' . $useridc);
        Cache::forget($cacheKey);

        return static::verifyToken($token, $useridc);
    }

    // ── Implementación interna ────────────────────────────────────────────

    private static function callVerifyEndpoint(string $token, string $useridc): ?array
    {
        $authBase    = rtrim((string) config('idcgames-ui.idc_api.auth_url', 'https://auth.idcgames.com'), '/');
        $verifyPath  = '/' . ltrim((string) config('idcgames-ui.idc_api.verify_token_path', '/api/web/verify-token'), '/');
        $internalKey = (string) config('idcgames-ui.idc_api.internal_key', '');

        $headers = ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'];
        if ($internalKey) {
            $headers[] = 'X-Internal-Key: ' . $internalKey;
        }

        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => $authBase . $verifyPath,
                CURLOPT_POST           => true,
                // auth_token + token: compat con distintos nombres de parámetro del auth server
                CURLOPT_POSTFIELDS     => http_build_query([
                    'auth_token' => $token,
                    'token'      => $token,
                    'useridc'    => $useridc,
                ]),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_HTTPHEADER     => $headers,
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($curl);
            curl_close($curl);

            if ($curlErr || ! $response || $httpCode !== 200) {
                Log::warning('[IDCGames SSO] verify-legacy-token no disponible', [
                    'http_code' => $httpCode,
                    'curl_err'  => $curlErr,
                ]);
                return null;
            }

            $data = json_decode($response, true);

            // Token inválido según el servidor
            if (isset($data['valid']) && $data['valid'] === false) {
                return null;
            }

            $verifiedId = (string) ($data['useridc'] ?? $data['id_user'] ?? $data['id'] ?? '');

            // El servidor devolvió un useridc diferente al de la cookie → rechazar
            if ($verifiedId && $verifiedId !== $useridc) {
                Log::warning('[IDCGames SSO] useridc mismatch', [
                    'cookie'   => $useridc,
                    'verified' => $verifiedId,
                ]);
                return null;
            }

            return [
                'useridc' => $verifiedId ?: $useridc,
                'nick'    => $data['nick']  ?? null,
                'email'   => $data['email'] ?? null,
            ];

        } catch (\Throwable $e) {
            Log::warning('[IDCGames SSO] Error llamando verify-legacy-token: ' . $e->getMessage());
            return null;
        }
    }
}
