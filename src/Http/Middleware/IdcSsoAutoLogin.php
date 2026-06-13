<?php

namespace IDCGames\UI\Http\Middleware;

use Closure;
use IDCGames\UI\Services\IdcSsoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * IdcSsoAutoLogin — Middleware canónico SSO para proyectos IDCGames
 *
 * Flujo:
 *   1. Si el usuario ya está autenticado → pasa sin hacer nada.
 *   2. Lee las cookies de sesión del dominio .idcgames.com (id, nick, token).
 *   3. Valida el token contra auth.idcgames.com vía IdcSsoService (con caché 5 min).
 *   4. Si es válido → resuelve/crea el usuario local y hace Auth::login().
 *   5. Ejecuta el hook post-login si está configurado (ej: generar Sanctum token).
 *
 * Registro automático en el ServiceProvider como alias 'idc.sso.auto'.
 *
 * ── Personalización por proyecto ─────────────────────────────────────────────
 *
 * Resolver de usuario (AppServiceProvider del proyecto):
 *
 *   app()->singleton('idcgames.sso.resolver', fn() =>
 *       function (array $idcData): ?\Illuminate\Database\Eloquent\Model {
 *           // $idcData: ['useridc', 'nick', 'email', 'token']
 *           return \App\Models\User::firstOrCreate(
 *               ['useridc' => $idcData['useridc']],
 *               ['username' => $idcData['nick'], ...]
 *           );
 *       }
 *   );
 *
 * Hook post-login (para Sanctum token, cookies extra, etc.):
 *
 *   app()->singleton('idcgames.sso.after_login', fn() =>
 *       function (\Illuminate\Database\Eloquent\Model $user, Request $request): void {
 *           $token = $user->createToken('app')->plainTextToken;
 *           $request->session()->put('sanctum_token', $token);
 *       }
 *   );
 */
class IdcSsoAutoLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        // ── Ya autenticado ────────────────────────────────────────
        if (Auth::check()) {
            return $next($request);
        }

        // ── Leer cookies IDC ──────────────────────────────────────
        $useridc  = (string) ($request->cookie('id')    ?? '');
        $nickname = (string) ($request->cookie('nick')  ?? '');
        $idcToken = (string) ($request->cookie('token') ?? '');

        if (! $useridc || ! $nickname || ! $idcToken) {
            return $next($request);
        }

        $nickname = urldecode($nickname);

        // ── Verificar token contra auth.idcgames.com ──────────────
        $verified = IdcSsoService::verifyToken($idcToken, $useridc);

        if (! $verified) {
            Log::warning("[IDCGames SSO] Token inválido — useridc={$useridc}");
            return $next($request);
        }

        // ── Resolver usuario local ────────────────────────────────
        try {
            $idcData = [
                'useridc' => $verified['useridc'],
                'nick'    => $verified['nick'] ?? $nickname,
                'email'   => $verified['email'] ?? null,
                'token'   => $idcToken,
            ];

            $user = $this->resolveUser($idcData);

            if (! $user) {
                return $next($request);
            }

            // ── Login ─────────────────────────────────────────────
            Auth::login($user, remember: true);
            $request->session()->regenerate();

            Log::info('[IDCGames SSO] Auto-login: ' . $idcData['nick'] . ' (useridc=' . $idcData['useridc'] . ')');

            // ── Hook post-login (Sanctum token, cookies extra…) ───
            if (app()->bound('idcgames.sso.after_login')) {
                app('idcgames.sso.after_login')($user, $request);
            }

        } catch (\Throwable $e) {
            Log::warning('[IDCGames SSO] Auto-login fallido: ' . $e->getMessage());
        }

        return $next($request);
    }

    // ── Resolución de usuario local ───────────────────────────────────────

    private function resolveUser(array $idcData): ?object
    {
        // Resolver personalizado del proyecto
        if (app()->bound('idcgames.sso.resolver')) {
            return app('idcgames.sso.resolver')($idcData);
        }

        // Resolver por defecto: usa config('auth.providers.users.model')
        // y rellena solo los campos en $fillable del modelo.
        $modelClass = config('auth.providers.users.model', \App\Models\User::class);

        $user = $modelClass::where('useridc', $idcData['useridc'])->first();

        if (! $user) {
            $user = $this->createUser($modelClass, $idcData);
            Log::info('[IDCGames SSO] Usuario creado: ' . $idcData['nick'] . ' (useridc=' . $idcData['useridc'] . ')');
        } else {
            // Actualizar token si cambió
            $fillable = (new $modelClass)->getFillable();
            if (in_array('idc_token', $fillable) && $user->idc_token !== $idcData['token']) {
                $user->idc_token = $idcData['token'];
                $user->save();
            }
        }

        return $user;
    }

    /**
     * Crea el usuario con los campos que el modelo tenga en $fillable.
     * Soporta modelos con distintos nombres de campo (password/hash, etc.).
     */
    private function createUser(string $modelClass, array $d): object
    {
        $fillable = (new $modelClass)->getFillable();
        $has      = fn(string $field) => in_array($field, $fillable);

        $data = array_filter([
            'useridc'           => $has('useridc')           ? $d['useridc']                        : null,
            'username'          => $has('username')          ? $d['nick']                            : null,
            'nickname'          => $has('nickname')          ? $d['nick']                            : null,
            'name'              => $has('name')              ? $d['nick']                            : null,
            'email'             => $has('email')             ? ($d['email'] ?? null)                 : null,
            'idc_token'         => $has('idc_token')         ? $d['token']                           : null,
            'password'          => $has('password')          ? bcrypt(str()->random(32))             : null,
            'hash'              => $has('hash')              ? bcrypt(str()->random(32))             : null,
            'color'             => $has('color')             ? '#' . substr(md5($d['nick']), 0, 6)   : null,
            'email_verified_at' => $has('email_verified_at') ? now()                                 : null,
        ], fn($v) => $v !== null);

        return $modelClass::create($data);
    }
}
