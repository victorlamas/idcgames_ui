{{--
    IDCGames Navbar — dark gaming theme
    Props:
      $services        array  — lista de servicios del config
      $currentService  string — slug del servicio activo (gifts|gamer|forum)
      $user            ?User  — usuario autenticado o null
--}}
<nav
    x-data="{ open: false, userOpen: false }"
    class="fixed top-0 inset-x-0 z-50 h-16 bg-idc-surface backdrop-blur-sm"
    style="border-bottom: 1px solid #00ff7f;"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between gap-4">

        {{-- ── Logo ────────────────────────────────────────── --}}
        <a href="https://idcgames.com" class="flex items-center gap-2 shrink-0">
            <img
                src="{{ config('idcgames-ui.logo_url') }}"
                alt="IDCGames"
                class="h-8 w-auto"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            >
            <span
                class="hidden items-center font-display font-bold text-xl tracking-wide text-white"
                style="display:none"
            >
                IDC<span class="text-idc-accent">Games</span>
            </span>
        </a>

        {{-- ── Servicios (desktop) ──────────────────────────── --}}
        <div class="hidden md:flex items-center gap-1">
            @foreach($services as $key => $service)
                <a
                    href="{{ $service['url'] }}"
                    class="
                        px-4 py-1.5 rounded-md text-sm font-medium transition-colors duration-150
                        {{ $currentService === $key
                            ? 'bg-idc-accent/15 text-idc-accent border border-idc-accent/30'
                            : 'text-idc-muted hover:text-white hover:bg-white/5' }}
                    "
                >
                    {{ $service['label'] }}
                </a>
            @endforeach
        </div>

        {{-- ── Derecha: Auth / User ─────────────────────────── --}}
        <div class="flex items-center gap-3 shrink-0">

            @auth
                {{-- Usuario autenticado --}}
                <div class="relative" x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-md text-sm font-medium text-idc-muted hover:text-white hover:bg-white/5 transition-colors"
                    >
                        {{-- Avatar --}}
                        @if($user->profile_photo_path ?? false)
                            <img src="{{ $user->profile_photo_path }}" class="w-7 h-7 rounded-full object-cover ring-1 ring-idc-border" alt="">
                        @else
                            <span class="w-7 h-7 rounded-full bg-idc-accent/20 text-idc-accent flex items-center justify-center text-xs font-bold uppercase ring-1 ring-idc-accent/30">
                                {{ substr($user->nickname ?? $user->name ?? '?', 0, 1) }}
                            </span>
                        @endif
                        <span class="hidden sm:inline max-w-[120px] truncate">{{ $user->nickname ?? $user->name }}</span>
                        {{-- Chevron --}}
                        <svg class="w-3 h-3 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        @click.outside="open = false"
                        class="absolute right-0 mt-2 w-48 bg-idc-surface border border-idc-border rounded-lg shadow-xl shadow-black/40 py-1 z-50"
                        style="display:none"
                    >
                        <div class="px-3 py-2 border-b border-idc-border">
                            <p class="text-xs text-idc-muted">Signed in as</p>
                            <p class="text-sm font-medium text-white truncate">{{ $user->nickname ?? $user->name }}</p>
                        </div>

                        {{-- Links según el servicio activo --}}
                        <a href="/profile" class="flex items-center gap-2 px-3 py-2 text-sm text-idc-muted hover:text-white hover:bg-white/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profile
                        </a>

                        <div class="border-t border-idc-border mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-idc-muted hover:text-red-400 hover:bg-red-500/5 transition-colors text-left">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            @else
                {{-- No autenticado --}}
                <a
                    href="{{ route('login') }}"
                    class="px-4 py-1.5 text-sm font-medium text-idc-muted hover:text-white transition-colors"
                >
                    Sign in
                </a>
                <a
                    href="{{ route('login') }}?tab=register"
                    class="px-4 py-1.5 bg-idc-accent hover:bg-idc-accent-hover text-white text-sm font-semibold rounded-md transition-colors"
                >
                    Join free
                </a>
            @endauth

            {{-- ── Hamburger (mobile) ───────────────────────── --}}
            <button
                @click="open = !open"
                class="md:hidden p-1.5 rounded-md text-idc-muted hover:text-white hover:bg-white/5 transition-colors"
                aria-label="Toggle menu"
            >
                <svg x-show="!open" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open"  class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- ── Mobile menu ──────────────────────────────────────── --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="md:hidden absolute top-16 inset-x-0 bg-idc-surface border-b border-idc-border shadow-xl"
        style="display:none"
    >
        <div class="px-4 py-3 space-y-1">
            @foreach($services as $key => $service)
                <a
                    href="{{ $service['url'] }}"
                    class="
                        block px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ $currentService === $key
                            ? 'bg-idc-accent/15 text-idc-accent'
                            : 'text-idc-muted hover:text-white hover:bg-white/5' }}
                    "
                >
                    {{ $service['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</nav>

{{-- Alpine.js (si no está cargado en el proyecto) --}}
@once
    @push('scripts')
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endpush
@endonce
