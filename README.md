# idcgames/ui — Shared UI Package

Package Laravel compartido para todos los servicios de IDCGames.
Proporciona: NavBar, Footer, Layout, Auth views, Tailwind design tokens, middleware de autenticación IDC.

---

## Instalación en un proyecto hijo (gifts, gamer, forum)

### 1. Añadir el package via `composer.json`

El package vive en `https://github.com/victorlamas/idcgames_ui.git` (repositorio público) y
se versiona con tags `vX.Y.Z` (semver). Cada proyecto hijo debe apuntar a ese repo con un
repositorio `vcs`, **no** a una ruta local — así los cambios llegan con `composer update`
en vez de tener que editar el vendor a mano.

```json
// composer.json del proyecto hijo
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/victorlamas/idcgames_ui.git"
        }
    ],
    "require": {
        "idcgames/ui": "^1.0"
    }
}
```

Luego:
```bash
composer require idcgames/ui:^1.0
```

Para traer una nueva versión publicada (tras un `git tag vX.Y.Z` en el package):
```bash
composer update idcgames/ui
```

> **Desarrollo local del package:** si estás editando `idcgames/ui` y `gifts`/`gamer`/`forum`
> a la vez y quieres ver los cambios sin publicar un tag, usa temporalmente un repositorio
> `path` en su lugar (`{"type": "path", "url": "../idcgames_ui", "options": {"symlink": true}}`
> con `"idcgames/ui": "*"`). Es solo para desarrollo — antes de desplegar, vuelve a la
> configuración `vcs` + versión de arriba.
>
> **Versionado:** cada cambio publicado en `main` debe subir la `version` en
> `composer.json` del package y crear su tag (`git tag vX.Y.Z && git push --tags`).
> Sin tag, `composer update` no verá el cambio si el proyecto hijo está fijado a una
> versión (`^1.0`); solo lo vería con `dev-main`, que no se recomienda en producción
> porque cualquier push a `main` afectaría a todos los hijos sin control de versión.

### 2. Publicar el config

```bash
php artisan vendor:publish --tag=idcgames-ui-config
```

Esto crea `config/idcgames-ui.php` — personalizar las URLs de cada servicio.

### 3. Variables de entorno (`.env`)

```env
# Sesión compartida entre subdominios
SESSION_DOMAIN=.idcgames.com
SANCTUM_STATEFUL_DOMAINS=gifts.idcgames.com,gamer.idcgames.com,forum.idcgames.com

# URLs de los servicios (para el navbar)
IDCGAMES_GIFTS_URL=https://gifts.idcgames.com
IDCGAMES_GAMER_URL=https://gamer.idcgames.com
IDCGAMES_FORUM_URL=https://forum.idcgames.com

# Auth service IDC — valida el idc_token via POST server-to-server
IDC_AUTH_URL=https://auth.idcgames.com
IDC_VERIFY_TOKEN_PATH=/api/web/verify-token

# Clave compartida para las llamadas servidor-a-servidor a IDC_AUTH_URL
# (verify-token, etc.). Nunca se expone al browser. Debe coincidir con la
# clave configurada en el auth server.
INTERNAL_API_KEY=

# Legacy unilogin (fallback automático si auth service no responde, no es necesario cambiar)
# IDC_UNILOGIN_URL=https://en.idcgames.com/unilogin/SoloLoginJuegoUnico.php
```

> **Migración unilogin → auth service:** el middleware `IdcSsoAutoLogin` llama primero a
> `auth.idcgames.com` + `IDC_VERIFY_TOKEN_PATH` (POST con `auth_token`, `token` y `useridc`,
> más el header `X-Internal-Key: {INTERNAL_API_KEY}`).
> Si el auth service no responde (timeout / 5xx), cae automáticamente al endpoint legacy
> `SoloLoginJuegoUnico.php`, por lo que el SSO no se rompe durante la transición.
> Una vez que el auth service esté estable, se puede eliminar la key `unilogin_url`.

### 3.1 URLs de auth: server-side vs. browser (evitar CORS)

El package expone dos conceptos de URL distintos para el auth service — **no son intercambiables**:

| Variable | Quién la usa | Valor típico | Propósito |
|---|---|---|---|
| `IDC_AUTH_URL` | Server-side (proxy `/idc-auth/*`, `IdcSsoService`, verificación de tokens) | `https://auth.idcgames.com` | Destino real al que Laravel reenvía las peticiones del proxy y las llamadas server-to-server. |
| `IDC_AUTH_PUBLIC_URL` | Browser (widget de login/registro) | `https://{proyecto}.idcgames.com/idc-auth` | Base que el navegador usa para cargar el widget y llamar a la API de auth. Al ser mismo-origen (vía el proxy `/idc-auth/*`), evita problemas de CORS y permite que las cookies del auth server se guarden correctamente. |
| `IDC_AUTH_WIDGET_URL` *(opcional)* | Browser | — | Override explícito si se quiere apuntar el widget a otra URL distinta de `IDC_AUTH_PUBLIC_URL`. |

Cada proyecto hijo debe definir en su propio `config/services.php`:

```php
// config/services.php del proyecto hijo
'idc_auth' => [
    'url'        => env('IDC_AUTH_URL', 'https://auth.idcgames.com'),
    'public_url' => env('IDC_AUTH_PUBLIC_URL'),
    'widget_url' => env('IDC_AUTH_WIDGET_URL', env('IDC_AUTH_PUBLIC_URL', env('IDC_AUTH_URL', 'https://auth.idcgames.com'))),
],
```

Y en el `.env`:

```env
IDC_AUTH_URL=https://auth.idcgames.com
IDC_AUTH_PUBLIC_URL=https://{proyecto}.idcgames.com/idc-auth
# IDC_AUTH_WIDGET_URL=  (opcional, solo si necesitas otra URL distinta a la pública)
```

**El browser NUNCA debe apuntar directo a `auth.idcgames.com`** (widget, `data-api-base`,
meta `idc-auth-url`) — siempre debe usar `IDC_AUTH_PUBLIC_URL` / `IDC_AUTH_WIDGET_URL`, que
resuelve al proxy same-origin `/idc-auth/{path}` (ver `routes/web.php`). El proxy reenvía
la petición a `IDC_AUTH_URL` server-side y devuelve al browser **todas** las cabeceras
`Set-Cookie` de la respuesta, para que el login / `bootstrap-session` persistan la sesión.

El meta tag que consume el widget (y `IDCNavbar.vue` vía `data-api-base`) se puede añadir
manualmente en el layout del proyecto hijo:

```blade
<meta name="idc-auth-url" content="{{ rtrim(config('services.idc_auth.widget_url'), '/') }}">
```

o, para no duplicarlo en cada repo, incluyendo el partial que ya trae el package:

```blade
@include('idcgames::idc-auth-meta')
```

(ya incluido automáticamente si usas `<x-idcgames::layout>`).

### 3.2 Proxy `/idc-auth` en nginx (producción — **support / mud**)

En OVH/producción, **support y mud suelen tener ya** un `location /idc-auth/` en nginx
(reenvío a `auth.idcgames.com`). El browser usa `https://{proyecto}.idcgames.com/idc-auth`;
nginx hace el puente (GET widget, POST `token-login`, cookies).

Referencia para comparar vhosts (no implica instalar de cero):

`vendor/idcgames/ui/deploy/nginx-idc-auth-proxy.conf.example`

**Por defecto el package NO registra** la ruta PHP `/idc-auth/*` — asume nginx en producción
(support, mud, etc.). Tras `composer update idcgames/ui`: `php artisan route:clear`.

**Desarrollo local sin nginx**, activa el proxy PHP:

```env
IDC_AUTH_LARAVEL_PROXY=true
```

El **JS del widget** se carga desde `https://auth.idcgames.com/widget/idc-auth-widget.js`
(`data-api-base` sigue siendo el proxy `/idc-auth` del proyecto).

Si **`/idc-auth/widget/...` → 404** tras v1.0.4: el vhost no hace `proxy_pass` a auth y mandaba
`/idc-auth` a Laravel; corrige nginx o temporalmente `IDC_AUTH_LARAVEL_PROXY=true`.

Si **502 en POST** con nginx ya configurado:

1. **Diff mud vs support**: `proxy_pass`, `Host auth.idcgames.com`, `proxy_ssl_server_name on`.
2. **Orden de locations**: `/idc-auth/` **antes** de PHP / `try_files` (si el POST cae en Laravel → 502 HTML).
3. Confirma que no queda ruta duplicada: `php artisan route:list | grep idc-auth` → vacío en producción.

Comprobación:

```bash
curl -sS -o /dev/null -w '%{http_code}\n' 'https://mud.idcgames.com/idc-auth/i18n/es.json'
curl -sS -o /dev/null -w '%{http_code}\n' -X POST \
  'https://mud.idcgames.com/idc-auth/api/web/token-login' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d 'nick=test&pass=test&game=1'
```

(i18n → **200**; login con credenciales falsas → **401/422 JSON**, no **502 HTML**.)

Sin nginx (solo local): `IDC_AUTH_LARAVEL_PROXY=true` y `idcgames/ui` **≥ 1.0.2** (proxy PHP sin CSRF).

### 4. Configurar Tailwind del proyecto hijo

```js
// tailwind.config.js del proyecto hijo
const idcBase = require('../idcgames_ui/tailwind.config.js')

module.exports = {
    presets: [idcBase],
    content: [
        './resources/**/*.{blade.php,js,vue}',
        '../idcgames_ui/resources/views/**/*.blade.php',  // incluir vistas del package
    ],
}
```

### 5. CSS del proyecto hijo

```css
/* resources/css/app.css */
@import '../../idcgames_ui/resources/css/app.css';

/* Estilos adicionales del proyecto */
```

### 6. JS del proyecto hijo

```js
// resources/js/app.js
import { setupInertiaApp } from '../../idcgames_ui/resources/js/app.js'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

setupInertiaApp({
    appName: 'Gifts',  // o 'Gamer', 'Forum'
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
})
```

---

## Uso de los Blade Components

### Layout principal
```blade
<x-idcgames::layout title="Giveaways" active="gifts">
    <p>Contenido de la página</p>
</x-idcgames::layout>
```

Props del layout:
| Prop | Tipo | Default | Descripción |
|---|---|---|---|
| `title` | string | `''` | Título de la página (aparece en `<title>`) |
| `active` | string | `''` | Servicio activo: `gifts`, `gamer` o `forum` |
| `withNavbar` | bool | `true` | Mostrar o no el navbar |
| `withFooter` | bool | `true` | Mostrar o no el footer |
| `bodyClass` | string | `''` | Clases adicionales para `<body>` |

### NavBar standalone
```blade
<x-idcgames::navbar active="forum" />
```

### Footer standalone
```blade
<x-idcgames::footer />
```

---

## Middleware

El middleware `idc.token` protege rutas que requieren un IDC token válido:

```php
// routes/web.php o routes/api.php del proyecto hijo
Route::middleware('idc.token')->group(function () {
    Route::get('/dashboard', DashboardController::class);
});
```

---

## Auth API endpoints (incluidos por el package)

| Método | Ruta | Descripción |
|---|---|---|
| `GET` | `/login` | Vista de login |
| `POST` | `/login` | Login web (sesión) |
| `POST` | `/logout` | Logout web |
| `POST` | `/api/v1/login` | Login API (Sanctum token) |
| `POST` | `/api/v1/logout/{username}` | Logout API |
| `GET` | `/api/v1/user` | Usuario autenticado actual |

---

## Clases Tailwind disponibles (design tokens)

| Token | Valor | Uso |
|---|---|---|
| `bg-idc-dark` | `#0f1117` | Fondo principal |
| `bg-idc-surface` | `#1a1d27` | Cards, navbar, footer |
| `bg-idc-surface-2` | `#22263a` | Cards elevados |
| `border-idc-border` | `#2e3248` | Bordes |
| `text-idc-light` | `#e8eaf0` | Texto principal |
| `text-idc-muted` | `#6b7280` | Texto secundario |
| `text-idc-accent` | `#6366f1` | Color principal IDC (indigo) |
| `font-display` | Rajdhani | Headings gaming |
| `font-sans` | Inter | Texto normal |

## Componentes CSS disponibles

Clases utilitarias definidas en `app.css`:

```
.btn, .btn-primary, .btn-secondary, .btn-danger, .btn-ghost
.card, .card-sm
.input, .input-error
.label
.badge, .badge-accent, .badge-success, .badge-warning, .badge-danger
.alert, .alert-error, .alert-success, .alert-info
.page-header, .page-title, .page-subtitle
.divider
```
