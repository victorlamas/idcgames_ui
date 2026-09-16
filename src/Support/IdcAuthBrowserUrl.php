<?php

namespace IDCGames\UI\Support;

/**
 * Base URL the browser should use for idc-auth-widget.js and /api/web/* calls.
 * On project subdomains (*.idcgames.com), always same-origin /idc-auth — never auth.* direct.
 */
class IdcAuthBrowserUrl
{
    public static function resolve(?string $configured, ?string $appUrl = null): string
    {
        $appUrl = rtrim((string) ($appUrl ?? config('app.url', '')), '/');
        $configured = $configured !== null ? rtrim($configured, '/') : '';

        if ($configured === '') {
            return self::fallback($appUrl);
        }

        $appHost = self::hostFromUrl($appUrl);
        if ($appHost === '' || self::isAuthHost($appHost) || ! self::isIdcGamesHost($appHost)) {
            return $configured;
        }

        $widgetHost = self::hostFromUrl($configured);
        if ($widgetHost !== '' && self::isAuthHost($widgetHost)) {
            return $appUrl.'/idc-auth';
        }

        return $configured;
    }

    public static function fallback(string $appUrl): string
    {
        $appUrl = rtrim($appUrl, '/');
        if ($appUrl !== '') {
            $host = self::hostFromUrl($appUrl);
            if ($host !== '' && ! self::isAuthHost($host) && self::isIdcGamesHost($host)) {
                return $appUrl.'/idc-auth';
            }
        }

        return 'https://auth.idcgames.com';
    }

    private static function hostFromUrl(string $url): string
    {
        if ($url === '') {
            return '';
        }

        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) ? strtolower($host) : '';
    }

    private static function isAuthHost(string $host): bool
    {
        return in_array($host, ['auth.idcgames.com', 'auth.idcgames.net'], true);
    }

    private static function isIdcGamesHost(string $host): bool
    {
        return $host === 'idcgames.com'
            || $host === 'idcgames.net'
            || str_ends_with($host, '.idcgames.com')
            || str_ends_with($host, '.idcgames.net');
    }
}
