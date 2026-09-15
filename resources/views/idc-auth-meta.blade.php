{{--
    Meta tag con la base pública del widget IDC Auth.
    El browser debe usar esta URL (proxy /idc-auth o dominio público del widget),
    NUNCA auth.idcgames.com directo, para evitar CORS.

    widget_url = IDC_AUTH_WIDGET_URL ?: IDC_AUTH_PUBLIC_URL ?: IDC_AUTH_URL
    (definido en config/services.php del proyecto hijo, clave 'idc_auth.widget_url')

    Uso en el <head> del layout del proyecto hijo:
        @include('idcgames::idc-auth-meta')
--}}
<meta name="idc-auth-url" content="{{ rtrim(config('services.idc_auth.widget_url', config('services.idc_auth.url', 'https://auth.idcgames.com')), '/') }}">
