<?php

namespace IDCGames\UI\Support;

use Illuminate\Http\Request;

/**
 * Cookies de sesión IDC (id, nick, token, games, …).
 *
 * Las escribe el widget / auth.idcgames.com en claro sobre .idcgames.com.
 * Si EncryptCookies las trata como cookies Laravel, el decrypt falla, PHP
 * deja de verlas y el SSO no loguea aunque document.cookie sí las tenga.
 */
class IdcSessionCookies
{
    /** @var list<string> */
    public const NAMES = [
        'token',
        'auth_token',
        'id',
        'nick',
        'email',
        'currency',
        'simbol',
        'lastLogin',
        'games',
        'membership_tier',
        'membership_badge',
    ];

    public static function get(Request $request, string $name): string
    {
        $value = $request->cookies->get($name);
        if (is_string($value) && $value !== '') {
            return $value;
        }

        $header = (string) $request->headers->get('Cookie', '');
        if ($header === '') {
            return '';
        }

        foreach (explode(';', $header) as $part) {
            $part = trim($part);
            $eq = strpos($part, '=');
            if ($eq === false) {
                continue;
            }
            if (substr($part, 0, $eq) !== $name) {
                continue;
            }

            return rawurldecode(substr($part, $eq + 1));
        }

        return '';
    }
}
