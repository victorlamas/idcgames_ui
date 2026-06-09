# idcgames/ui — Shared UI Package

Package Laravel compartido para todos los servicios de IDCGames.
Proporciona: NavBar, Footer, Layout, Auth views, Tailwind design tokens, middleware de autenticación IDC.

---

## Instalación en un proyecto hijo (gifts, gamer, forum)

### 1. Añadir el package via `composer.json` (path local)

```json
// composer.json del proyecto hijo
{
    "repositories": [
        {
            "type": "path",
            "url": "../idcgames_ui",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "idcgames/ui": "*"
    }
}
```

Luego:
```bash
composer require idcgames/ui
```

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

# Auth service IDC — valida el idc_token via POST (reemplaza SoloLoginJuegoUnico)
IDC_AUTH_URL=https://auth.idcgames.com
IDC_VERIFY_TOKEN_PATH=/api/web/verify-legacy-token

# Legacy unilogin (fallback automático si auth service no responde, no es necesario cambiar)
# IDC_UNILOGIN_URL=https://en.idcgames.com/unilogin/SoloLoginJuegoUnico.php
```

> **Migración unilogin → auth service:** el middleware `IdcSsoAutoLogin` llama primero a
> `auth.idcgames.com/api/web/verify-legacy-token` (POST con `token` y `useridc`).
> Si el auth service no responde (timeout / 5xx), cae automáticamente al endpoint legacy
> `SoloLoginJuegoUnico.php`, por lo que el SSO no se rompe durante la transición.
> Una vez que el auth service esté estable, se puede eliminar la key `unilogin_url`.

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
