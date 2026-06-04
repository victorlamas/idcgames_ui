<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in — {{ config('idcgames-ui.app_name', config('app.name')) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-idc-dark flex items-center justify-center p-4">

    <div class="w-full max-w-sm space-y-6">

        {{-- Logo --}}
        <div class="text-center space-y-2">
            <a href="https://idcgames.com" class="inline-flex items-center justify-center">
                <img
                    src="{{ config('idcgames-ui.logo_url') }}"
                    alt="IDCGames"
                    class="h-10 w-auto"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >
                <span
                    class="hidden items-center font-display font-bold text-2xl text-white"
                    style="display:none"
                >
                    IDC<span class="text-idc-accent">Games</span>
                </span>
            </a>
            <p class="text-sm text-idc-muted">
                Sign in to continue to
                <span class="text-white font-medium">{{ config('app.name') }}</span>
            </p>
        </div>

        {{-- Card --}}
        <div class="bg-idc-surface border border-idc-border rounded-xl p-6 shadow-xl shadow-black/30">

            {{-- Errores --}}
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                    <p class="text-sm text-red-400">{{ $errors->first() }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                    <p class="text-sm text-red-400">{{ session('error') }}</p>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 rounded-lg">
                    <p class="text-sm text-green-400">{{ session('status') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                {{-- idc_token oculto (lo puede inyectar el cliente IDC) --}}
                <input type="hidden" name="idctoken" id="idctoken" value="{{ request('idctoken') }}">

                {{-- Username --}}
                <div class="space-y-1.5">
                    <label for="username" class="block text-sm font-medium text-idc-light">
                        Username
                    </label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        autocomplete="username"
                        required
                        autofocus
                        value="{{ old('username') }}"
                        class="
                            w-full px-3 py-2.5 rounded-lg text-sm
                            bg-idc-dark border border-idc-border
                            text-white placeholder-idc-muted
                            focus:outline-none focus:ring-2 focus:ring-idc-accent/50 focus:border-idc-accent
                            transition-colors
                            @error('username') border-red-500/60 @enderror
                        "
                        placeholder="Your IDCGames username"
                    >
                </div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <label for="password" class="block text-sm font-medium text-idc-light">
                        Password
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="
                            w-full px-3 py-2.5 rounded-lg text-sm
                            bg-idc-dark border border-idc-border
                            text-white placeholder-idc-muted
                            focus:outline-none focus:ring-2 focus:ring-idc-accent/50 focus:border-idc-accent
                            transition-colors
                            @error('password') border-red-500/60 @enderror
                        "
                        placeholder="••••••••"
                    >
                </div>

                {{-- Remember me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded border-idc-border bg-idc-dark text-idc-accent focus:ring-idc-accent/50"
                        >
                        <span class="text-sm text-idc-muted">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-idc-accent hover:text-idc-accent-hover transition-colors">
                            Forgot password?
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full py-2.5 px-4 bg-idc-accent hover:bg-idc-accent-hover text-white text-sm font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-idc-accent/50 focus:ring-offset-2 focus:ring-offset-idc-surface"
                >
                    Sign in
                </button>
            </form>
        </div>

        {{-- Footer del login --}}
        <p class="text-center text-xs text-idc-muted">
            Don't have an account?
            <a href="https://idcgames.com/register" class="text-idc-accent hover:text-idc-accent-hover transition-colors font-medium">
                Create one at IDCGames.com
            </a>
        </p>
    </div>

</body>
</html>
