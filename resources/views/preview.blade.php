<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IDCGames UI — Preview</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Tailwind CDN (preview only — each project compiles its own) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'idc-dark':         '#13181d',
                        'idc-surface':      '#1b1d2c',
                        'idc-surface-2':    '#0b2e3e',
                        'idc-border':       '#314954',
                        'idc-light':        '#e8eaf0',
                        'idc-muted':        '#839298',
                        'idc-accent':       '#00ff7f',
                        'idc-accent-hover': '#00cc66',
                        'idc-accent-light': '#66ffaa',
                        'idc-accent-dark':  '#009944',
                        'idc-success':      '#22c55e',
                        'idc-warning':      '#f59e0b',
                        'idc-danger':       '#ef4444',
                    },
                    fontFamily: {
                        sans:    ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Rajdhani', 'Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'idc':    '0 4px 24px rgba(0,0,0,0.4)',
                        'idc-lg': '0 8px 48px rgba(0,0,0,0.6)',
                    },
                }
            }
        }
    </script>

    {{-- Alpine.js for navbar dropdowns --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { background: #13181d; color: #e8eaf0; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #13181d; }
        ::-webkit-scrollbar-thumb { background: #314954; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #00ff7f; }
        /* Section anchor offset for sticky navbar */
        [id] { scroll-margin-top: 80px; }
    </style>
</head>
<body class="min-h-full font-sans antialiased">

{{-- ══════════════════════════════════════════════════════════════════════
     NAVBAR (cargado desde el Blade component del package)
     Para editar: idcgames_ui/resources/views/components/navbar.blade.php
     ══════════════════════════════════════════════════════════════════════ --}}
<x-idcgames::navbar active="gifts" />

{{-- ══════════════════════════════════════════════════════════════════════
     CONTENIDO DE PREVIEW
     ══════════════════════════════════════════════════════════════════════ --}}
<main class="pt-16">

    {{-- ── Banner de aviso ──────────────────────────────────────────── --}}
    <div class="bg-idc-accent/10 border-b border-idc-accent/20 text-center py-2.5 px-4">
        <p class="text-xs font-medium" style="color:#00ff7f;">
            🎨  <strong>IDCGames UI Preview</strong> — Esta página solo está disponible en entorno
            <code class="bg-idc-surface px-1.5 py-0.5 rounded text-xs">local</code>.
            Edita los componentes en <code class="bg-idc-surface px-1.5 py-0.5 rounded text-xs">idcgames_ui/resources/views/components/</code>
            y recarga.
        </p>
    </div>

    {{-- ── Índice rápido ─────────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="flex flex-wrap gap-2 text-xs">
            @foreach(['navbar','footer','colors','typography','buttons','cards','badges','inputs','alerts'] as $section)
                <a href="#{{ $section }}"
                   class="px-3 py-1 rounded-full border border-idc-border text-idc-muted hover:border-idc-accent hover:text-idc-accent transition-colors">
                    #{{ $section }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: NAVBAR DEMO
         ══════════════════════════════════════════════════════════════════ --}}
    <section id="navbar" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h2 class="font-display font-bold text-xl text-white mb-1">Navbar</h2>
        <p class="text-sm text-idc-muted mb-6">
            El Navbar real está fijado arriba. Cambia <code class="bg-idc-surface px-1 rounded">active="gifts"</code>
            a <code class="bg-idc-surface px-1 rounded">gamer</code> o <code class="bg-idc-surface px-1 rounded">forum</code>
            para ver el estado activo diferente.
        </p>
        <div class="bg-idc-surface border border-idc-border rounded-xl p-4 text-sm text-idc-muted space-y-2">
            <p>📁 <strong class="text-idc-light">Editar:</strong> <code>idcgames_ui/resources/views/components/navbar.blade.php</code></p>
            <p>⚙️ <strong class="text-idc-light">Servicios:</strong> configurados en <code>config/idcgames-ui.php</code> → key <code>services</code></p>
            <p>🎨 <strong class="text-idc-light">Estilo:</strong> border-bottom <span style="color:#00ff7f">■</span> <code>#00ff7f</code> · bg <code>#1b1d2c</code></p>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: COLORES / DESIGN TOKENS
         ══════════════════════════════════════════════════════════════════ --}}
    <section id="colors" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-t border-idc-border">
        <h2 class="font-display font-bold text-xl text-white mb-6">Design Tokens</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach([
                ['idc-dark',         '#13181d', 'bg-idc-dark',         'Body bg'],
                ['idc-surface',      '#1b1d2c', 'bg-idc-surface',      'Cards, Navbar'],
                ['idc-surface-2',    '#0b2e3e', 'bg-idc-surface-2',    'Hover elevado'],
                ['idc-border',       '#314954', 'bg-idc-border',       'Bordes'],
                ['idc-light',        '#e8eaf0', 'bg-idc-light',        'Texto principal'],
                ['idc-muted',        '#839298', 'bg-idc-muted',        'Texto secundario'],
                ['idc-accent',       '#00ff7f', 'bg-idc-accent',       'Spring Green ★'],
                ['idc-accent-hover', '#00cc66', 'bg-idc-accent-hover', 'Verde hover'],
                ['idc-accent-light', '#66ffaa', 'bg-idc-accent-light', 'Verde claro'],
                ['idc-accent-dark',  '#009944', 'bg-idc-accent-dark',  'Verde oscuro'],
                ['idc-success',      '#22c55e', 'bg-idc-success',      'Éxito'],
                ['idc-warning',      '#f59e0b', 'bg-idc-warning',      'Aviso'],
                ['idc-danger',       '#ef4444', 'bg-idc-danger',       'Error'],
            ] as [$name, $hex, $class, $label])
                <div class="rounded-lg overflow-hidden border border-idc-border">
                    <div class="{{ $class }} h-12 w-full border-b border-white/5"
                         style="background:{{ $hex }}"></div>
                    <div class="bg-idc-surface px-3 py-2">
                        <p class="text-xs font-medium text-idc-light">{{ $name }}</p>
                        <p class="text-xs text-idc-muted font-mono">{{ $hex }}</p>
                        <p class="text-xs text-idc-muted mt-0.5">{{ $label }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: TIPOGRAFÍA
         ══════════════════════════════════════════════════════════════════ --}}
    <section id="typography" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-t border-idc-border">
        <h2 class="font-display font-bold text-xl text-white mb-6">Typography</h2>
        <div class="space-y-4">
            <div class="flex items-baseline gap-4 flex-wrap">
                <span class="font-display font-bold text-5xl text-white">IDCGames</span>
                <span class="text-sm text-idc-muted">font-display (Rajdhani) · text-5xl</span>
            </div>
            <div class="flex items-baseline gap-4 flex-wrap">
                <span class="font-display font-bold text-3xl text-white">Heading 3xl</span>
                <span class="text-sm text-idc-muted">font-display bold</span>
            </div>
            <div class="flex items-baseline gap-4 flex-wrap">
                <span class="font-display font-semibold text-xl text-idc-accent">Accentuado</span>
                <span class="text-sm text-idc-muted">font-display · text-idc-accent</span>
            </div>
            <div class="flex items-baseline gap-4 flex-wrap">
                <span class="font-sans font-semibold text-base text-white">Body semibold</span>
                <span class="text-sm text-idc-muted">font-sans (Inter) · text-base</span>
            </div>
            <div class="flex items-baseline gap-4 flex-wrap">
                <span class="font-sans text-sm text-idc-muted">Texto muted secundario — subtítulos y labels</span>
                <span class="text-sm text-idc-muted">text-sm · text-idc-muted</span>
            </div>
            <div class="flex items-baseline gap-4 flex-wrap">
                <code class="font-mono text-sm bg-idc-surface px-2 py-0.5 rounded text-idc-accent-light">código monospace</code>
                <span class="text-sm text-idc-muted">font-mono</span>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: BOTONES
         ══════════════════════════════════════════════════════════════════ --}}
    <section id="buttons" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-t border-idc-border">
        <h2 class="font-display font-bold text-xl text-white mb-6">Buttons</h2>

        <div class="space-y-6">
            {{-- Fila principal --}}
            <div class="flex flex-wrap items-center gap-3">
                <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm bg-idc-accent hover:bg-idc-accent-hover text-idc-dark transition-colors">
                    btn-primary
                </button>
                <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm bg-idc-surface-2 hover:bg-idc-border text-idc-light border border-idc-border transition-colors">
                    btn-secondary
                </button>
                <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm bg-red-600 hover:bg-red-700 text-white transition-colors">
                    btn-danger
                </button>
                <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm text-idc-muted hover:text-white hover:bg-white/5 transition-colors">
                    btn-ghost
                </button>
            </div>

            {{-- Tamaños --}}
            <div class="flex flex-wrap items-center gap-3">
                <button class="px-3 py-1.5 rounded-md font-semibold text-xs bg-idc-accent hover:bg-idc-accent-hover text-idc-dark transition-colors">xs</button>
                <button class="px-4 py-2 rounded-lg font-semibold text-sm bg-idc-accent hover:bg-idc-accent-hover text-idc-dark transition-colors">sm</button>
                <button class="px-5 py-2.5 rounded-lg font-semibold text-base bg-idc-accent hover:bg-idc-accent-hover text-idc-dark transition-colors">base</button>
                <button class="px-6 py-3 rounded-xl font-semibold text-lg bg-idc-accent hover:bg-idc-accent-hover text-idc-dark transition-colors">lg</button>
            </div>

            {{-- Outline variant --}}
            <div class="flex flex-wrap items-center gap-3">
                <button class="px-5 py-2.5 rounded-lg font-semibold text-sm border-2 border-idc-accent text-idc-accent hover:bg-idc-accent hover:text-idc-dark transition-colors">
                    outline-accent
                </button>
                <button class="px-5 py-2.5 rounded-lg font-semibold text-sm border border-idc-border text-idc-muted hover:border-idc-accent hover:text-idc-accent transition-colors">
                    outline-muted
                </button>
                <button disabled class="px-5 py-2.5 rounded-lg font-semibold text-sm bg-idc-surface text-idc-muted cursor-not-allowed opacity-50">
                    disabled
                </button>
            </div>

            {{-- Icon buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm bg-idc-accent hover:bg-idc-accent-hover text-idc-dark transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create
                </button>
                <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm bg-idc-surface-2 hover:bg-idc-border text-idc-light border border-idc-border transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </button>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: CARDS
         ══════════════════════════════════════════════════════════════════ --}}
    <section id="cards" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-t border-idc-border">
        <h2 class="font-display font-bold text-xl text-white mb-6">Cards</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- Card estándar --}}
            <div class="bg-idc-surface border border-idc-border rounded-xl p-6 shadow-idc">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-semibold text-idc-muted uppercase tracking-wider">card</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-idc-accent/15 text-idc-accent border border-idc-accent/20">active</span>
                </div>
                <h3 class="font-display font-bold text-lg text-white mb-2">Tarjeta Estándar</h3>
                <p class="text-sm text-idc-muted">Fondo <code class="bg-idc-dark px-1 rounded">idc-surface</code>, borde <code class="bg-idc-dark px-1 rounded">idc-border</code>.</p>
            </div>

            {{-- Card con acento --}}
            <div class="bg-idc-surface border border-idc-accent/30 rounded-xl p-6 shadow-idc" style="box-shadow: 0 0 0 1px rgba(0,255,127,0.1), 0 4px 24px rgba(0,0,0,0.4);">
                <div class="w-10 h-10 rounded-lg bg-idc-accent/15 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-idc-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="font-display font-bold text-lg text-white mb-2">Card con Acento</h3>
                <p class="text-sm text-idc-muted">Borde <code class="bg-idc-dark px-1 rounded">idc-accent/30</code>. Para destacar.</p>
            </div>

            {{-- Card pequeña --}}
            <div class="bg-idc-surface border border-idc-border rounded-lg p-4">
                <p class="text-xs font-semibold text-idc-muted uppercase tracking-wider mb-1">card-sm</p>
                <p class="font-semibold text-white">Tarjeta compacta</p>
                <p class="text-sm text-idc-muted mt-1">Padding reducido para listas.</p>
            </div>

            {{-- Card de stat --}}
            <div class="bg-idc-surface border border-idc-border rounded-xl p-6">
                <p class="text-sm text-idc-muted mb-2">Total Giveaways</p>
                <p class="font-display font-bold text-4xl text-white">1,284</p>
                <p class="text-xs text-idc-success mt-2 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    +12% este mes
                </p>
            </div>

            {{-- Card de giveaway --}}
            <div class="bg-idc-surface border border-idc-border rounded-xl overflow-hidden hover:border-idc-accent/40 transition-colors group">
                <div class="h-32 bg-gradient-to-br from-idc-surface-2 to-idc-dark flex items-center justify-center">
                    <svg class="w-12 h-12 text-idc-border group-hover:text-idc-accent/40 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="p-4">
                    <p class="font-semibold text-white text-sm">Game Key Giveaway</p>
                    <p class="text-xs text-idc-muted mt-1">48 entradas · 2d restantes</p>
                    <button class="mt-3 w-full py-1.5 text-xs font-semibold bg-idc-accent hover:bg-idc-accent-hover text-idc-dark rounded-md transition-colors">
                        Participar
                    </button>
                </div>
            </div>

            {{-- Card dark --}}
            <div class="bg-idc-dark border border-idc-border rounded-xl p-6">
                <p class="text-xs font-semibold text-idc-muted uppercase tracking-wider mb-2">card-dark</p>
                <p class="text-sm text-idc-muted">Fondo <code class="bg-idc-surface px-1 rounded">idc-dark</code> para mayor contraste dentro de una sección clara.</p>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: BADGES
         ══════════════════════════════════════════════════════════════════ --}}
    <section id="badges" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-t border-idc-border">
        <h2 class="font-display font-bold text-xl text-white mb-6">Badges &amp; Tags</h2>
        <div class="flex flex-wrap gap-3">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-idc-accent/15 text-idc-accent border border-idc-accent/20">accent</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-green-500/15 text-green-400 border border-green-500/20">success</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-yellow-500/15 text-yellow-400 border border-yellow-500/20">warning</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-red-500/15 text-red-400 border border-red-500/20">danger</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-idc-surface text-idc-muted border border-idc-border">muted</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-500/15 text-blue-400 border border-blue-500/20">info</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-idc-accent/15 text-idc-accent border border-idc-accent/20">● Live</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-idc-surface text-idc-muted border border-idc-border">Lv. 42</span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-yellow-500/10 text-yellow-300 border border-yellow-500/20">
                ★ Gold
            </span>
            <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-idc-accent text-idc-dark">PANEL</span>
            <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-idc-surface-2 text-idc-accent border border-idc-accent/30">BETA</span>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: INPUTS
         ══════════════════════════════════════════════════════════════════ --}}
    <section id="inputs" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-t border-idc-border">
        <h2 class="font-display font-bold text-xl text-white mb-6">Form Inputs</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-2xl">

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-idc-light">Username</label>
                <input type="text" placeholder="tu_nick_idc"
                    class="w-full px-3 py-2.5 rounded-lg text-sm bg-idc-dark border border-idc-border text-white placeholder-idc-muted focus:outline-none focus:ring-2 focus:ring-idc-accent/50 focus:border-idc-accent transition-colors">
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-idc-light">Password</label>
                <input type="password" value="password123"
                    class="w-full px-3 py-2.5 rounded-lg text-sm bg-idc-dark border border-idc-border text-white placeholder-idc-muted focus:outline-none focus:ring-2 focus:ring-idc-accent/50 focus:border-idc-accent transition-colors">
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-idc-light">Con error</label>
                <input type="text" value="usuario_invalido"
                    class="w-full px-3 py-2.5 rounded-lg text-sm bg-idc-dark border border-red-500/60 text-white focus:outline-none focus:ring-2 focus:ring-red-500/30 transition-colors">
                <p class="text-xs text-red-400">El username no existe.</p>
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-idc-light">Select</label>
                <select class="w-full px-3 py-2.5 rounded-lg text-sm bg-idc-dark border border-idc-border text-idc-muted focus:outline-none focus:ring-2 focus:ring-idc-accent/50 focus:border-idc-accent transition-colors">
                    <option>Todos los juegos</option>
                    <option>Steam</option>
                    <option>Epic Games</option>
                </select>
            </div>

            <div class="sm:col-span-2 space-y-1.5">
                <label class="block text-sm font-medium text-idc-light">Textarea</label>
                <textarea rows="3" placeholder="Escribe tu mensaje..."
                    class="w-full px-3 py-2.5 rounded-lg text-sm bg-idc-dark border border-idc-border text-white placeholder-idc-muted focus:outline-none focus:ring-2 focus:ring-idc-accent/50 focus:border-idc-accent transition-colors resize-none"></textarea>
            </div>

            <div class="sm:col-span-2 flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" checked class="w-4 h-4 rounded border-idc-border bg-idc-dark accent-idc-accent">
                    <span class="text-sm text-idc-muted">Recuérdame</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 rounded border-idc-border bg-idc-dark accent-idc-accent">
                    <span class="text-sm text-idc-muted">Notificaciones</span>
                </label>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: ALERTS
         ══════════════════════════════════════════════════════════════════ --}}
    <section id="alerts" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-t border-idc-border">
        <h2 class="font-display font-bold text-xl text-white mb-6">Alerts &amp; Banners</h2>
        <div class="space-y-3 max-w-2xl">
            <div class="flex items-start gap-3 p-4 rounded-lg bg-idc-accent/10 border border-idc-accent/20">
                <svg class="w-5 h-5 text-idc-accent shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-idc-accent-light">Información — Tu cuenta está verificada y lista para usar.</p>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-lg bg-green-500/10 border border-green-500/20">
                <svg class="w-5 h-5 text-green-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-green-400">Éxito — ¡Has participado en el giveaway correctamente!</p>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-lg bg-yellow-500/10 border border-yellow-500/20">
                <svg class="w-5 h-5 text-yellow-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="text-sm text-yellow-400">Aviso — Tu cuenta pendiente de verificación.</p>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-lg bg-red-500/10 border border-red-500/20">
                <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-red-400">Error — Credenciales incorrectas. Inténtalo de nuevo.</p>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION: LOGIN CARD (preview)
         ══════════════════════════════════════════════════════════════════ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-t border-idc-border">
        <h2 class="font-display font-bold text-xl text-white mb-6">Auth Card (Login)</h2>
        <div class="max-w-sm">
            <div class="bg-idc-surface border border-idc-border rounded-2xl p-8 shadow-idc-lg">
                {{-- Logo --}}
                <div class="text-center mb-8">
                    <span class="font-display font-bold text-2xl text-white">
                        IDC<span style="color:#00ff7f">Games</span>
                    </span>
                    <span class="block text-xs text-idc-muted mt-1 uppercase tracking-widest">Gifts · Sign in</span>
                </div>
                {{-- Form --}}
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-idc-muted uppercase tracking-wider">Username</label>
                        <input type="text" placeholder="tu_nick_idc"
                            class="w-full px-3 py-2.5 rounded-lg text-sm bg-idc-dark border border-idc-border text-white placeholder-idc-muted focus:outline-none focus:ring-2 focus:ring-idc-accent/50 focus:border-idc-accent transition-colors">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-idc-muted uppercase tracking-wider">Password</label>
                        <input type="password" placeholder="••••••••"
                            class="w-full px-3 py-2.5 rounded-lg text-sm bg-idc-dark border border-idc-border text-white placeholder-idc-muted focus:outline-none focus:ring-2 focus:ring-idc-accent/50 focus:border-idc-accent transition-colors">
                    </div>
                    <button class="w-full py-2.5 rounded-lg font-semibold text-sm bg-idc-accent hover:bg-idc-accent-hover text-idc-dark transition-colors">
                        Sign in
                    </button>
                </div>
                <p class="text-center text-xs text-idc-muted mt-6">
                    ¿Sin cuenta?
                    <a href="#" style="color:#00ff7f" class="font-medium hover:underline">Regístrate en IDCGames.com</a>
                </p>
            </div>
        </div>
    </section>

    {{-- Espacio antes del footer --}}
    <div class="h-16"></div>

</main>

{{-- ══════════════════════════════════════════════════════════════════════
     FOOTER (cargado desde el Blade component del package)
     Para editar: idcgames_ui/resources/views/components/footer.blade.php
     ══════════════════════════════════════════════════════════════════════ --}}
<div id="footer">
    <x-idcgames::footer />
</div>

</body>
</html>
