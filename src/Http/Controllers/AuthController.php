<?php

namespace IDCGames\UI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * AuthController — IDCGames UI Package
 *
 * Gestiona el login/logout unificado para todos los servicios IDCGames.
 * Flujo:
 *   1. El usuario envía username + password + idc_token (opcional)
 *   2. Se valida contra la tabla `users` local (campo `username` + `password`)
 *   3. Si el usuario no tiene email, se obtiene de la API central IDC via idc_token
 *   4. Se emite un Sanctum token (para SPAs/Inertia) y se inicia sesión
 *
 * Basado en el AuthController de idcgames_gifts (el más completo).
 */
class AuthController extends Controller
{
    // ── Vistas ─────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('idcgames::auth.login');
    }

    // ── Login web (Blade/Inertia — sesión) ─────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = $this->findAndValidateUser(
            $request->username,
            $request->password,
            $request->idctoken
        );

        if (! $user) {
            return back()
                ->withErrors(['username' => 'Credenciales incorrectas.'])
                ->withInput($request->only('username'));
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // ── API Login (Sanctum token — para SPAs) ──────────────────────────

    public function apiLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $this->findAndValidateUser(
            $request->username,
            $request->password,
            $request->idctoken
        );

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas',
                'errors'  => ['username' => ['Usuario o contraseña incorrectos.']],
            ], 401);
        }

        Auth::login($user);
        $this->markUserOnline($user);

        $token = $user->createToken('IDCGamesApp')->plainTextToken;

        return response()->json([
            'success'  => true,
            'message'  => 'User signed in',
            'data'     => $this->buildUserPayload($user, $token),
        ]);
    }

    public function apiLogout(Request $request, string $username): JsonResponse
    {
        $tokenId = Auth::user()->currentAccessToken()->id;
        Auth::user()->tokens()->where('id', $tokenId)->delete();

        return response()->json([
            'success' => true,
            'data'    => ['result' => true],
            'message' => 'User logged out',
        ]);
    }

    public function currentUser(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->buildUserPayload(Auth::user()),
        ]);
    }

    // ── Helpers privados ───────────────────────────────────────────────

    /**
     * Busca el usuario por username y valida el password.
     * Si se proporciona idc_token lo almacena.
     * Si no tiene email, lo intenta obtener de la API IDC.
     */
    private function findAndValidateUser(string $username, string $password, ?string $idcToken = null): ?object
    {
        // El modelo User puede variar por proyecto — usamos el guard default
        $userModel = config('auth.providers.users.model', \App\Models\User::class);
        $user = $userModel::where('username', $username)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        // Actualizar idc_token si se envía
        if (! empty($idcToken) && strlen($idcToken) > 10) {
            $user->idc_token = $idcToken;
            $user->save();
        }

        // Obtener email de IDC si no lo tiene
        if (empty($user->email) && ! empty($user->idc_token)) {
            $this->fetchAndStoreEmail($user);
        }

        return $user;
    }

    /**
     * Consulta el endpoint central IDC para obtener el email del usuario.
     */
    private function fetchAndStoreEmail(object $user): void
    {
        $url = config('idcgames-ui.idc_api.unilogin_url') . '?token=' . $user->idc_token;

        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_HTTPHEADER     => ['accept: */*', 'content-type: application/json'],
            ]);
            $response = curl_exec($curl);
            $err      = curl_error($curl);
            curl_close($curl);

            if ($err || ! $response) {
                Log::warning('[IDCGames UI] fetchEmail curl error: ' . $err);
                return;
            }

            $data = json_decode($response);
            if (isset($data->content->cEmailIDC)) {
                $user->email = urldecode($data->content->cEmailIDC);
                $user->save();
            }
        } catch (\Throwable $e) {
            Log::warning('[IDCGames UI] fetchEmail exception: ' . $e->getMessage());
        }
    }

    /**
     * Construye el payload estándar de respuesta de usuario.
     * Añade campos opcionales si existen en el modelo.
     */
    private function buildUserPayload(object $user, ?string $token = null): array
    {
        $payload = [
            'user_id'  => $user->id,
            'username' => $user->username,
            'nickname' => $user->nickname ?? $user->name,
            'useridc'  => $user->useridc ?? null,
            'email'    => $user->email,
            'role'     => $user->admin ?? false ? 'admin' : 'user',
            'admin'    => (bool) ($user->admin ?? false),
            'profile_photo_path' => $user->profile_photo_path ?? null,
        ];

        // Campos opcionales de gamificación (presentes en gamer/gifts)
        foreach (['points', 'cb_level', 'level'] as $field) {
            if (isset($user->$field)) {
                $payload[$field] = $user->$field;
            }
        }

        if ($token) {
            $payload['authtoken'] = $token;
        }

        return $payload;
    }

    /**
     * Marca el usuario como online (cache de 2 minutos).
     */
    private function markUserOnline(object $user): void
    {
        try {
            Cache::put('user-is-online-' . $user->id, true, Carbon::now()->addMinutes(2));
            $user->getConnection()->table($user->getTable())
                ->where('id', $user->id)
                ->update(['last_seen' => now()]);
        } catch (\Throwable $e) {
            // last_seen puede no existir en todos los proyectos
        }
    }
}
