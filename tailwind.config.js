/**
 * IDCGames UI — Tailwind Config Base
 *
 * Cada proyecto hijo EXTIENDE este config:
 *
 *   // tailwind.config.js del proyecto hijo
 *   const idcBase = require('../idcgames_ui/tailwind.config.js')
 *   module.exports = {
 *     presets: [idcBase],
 *     content: ['./resources/**\/*.{blade.php,js,vue}'],
 *   }
 */

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        // Vistas del package
        './resources/views/**/*.blade.php',
        // El proyecto hijo debe añadir sus propios paths en su config
    ],

    theme: {
        extend: {
            // ── IDCGames Design Tokens ───────────────────────────────
            // Extraídos directamente de idcgames.com CSS (full_main_min_v2.css)
            colors: {
                'idc-dark':         '#13181d',   // body bg — rgb(19,24,29)
                'idc-surface':      '#1b1d2c',   // cards, navbar — rgb(27,29,44)
                'idc-surface-2':    '#0b2e3e',   // hover bg, elevado — rgb(11,46,62)
                'idc-border':       '#314954',   // bordes — rgb(49,73,84)
                'idc-light':        '#e8eaf0',   // texto principal
                'idc-muted':        '#839298',   // texto secundario — rgb(131,146,152)
                'idc-accent':       '#00ff7f',   // Spring Green — color principal IDC
                'idc-accent-hover': '#00cc66',   // verde hover (más oscuro)
                'idc-accent-light': '#66ffaa',   // verde claro (highlights)
                'idc-accent-dark':  '#009944',   // verde oscuro (texto sobre claro)
                'idc-teal':         '#314954',   // teal secundario (gradientes)
                'idc-success':      '#22c55e',
                'idc-warning':      '#f59e0b',
                'idc-danger':       '#ef4444',
            },

            fontFamily: {
                sans:    ['Inter', 'system-ui', 'sans-serif'],
                display: ['Rajdhani', 'Inter', 'sans-serif'],  // headings gaming
            },

            borderRadius: {
                'xl':  '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },

            boxShadow: {
                'idc':    '0 4px 24px rgba(0,0,0,0.4)',
                'idc-lg': '0 8px 40px rgba(0,0,0,0.6)',
            },

            // ── Animaciones gaming ───────────────────────────────────
            keyframes: {
                'fade-in': {
                    '0%':   { opacity: '0', transform: 'translateY(4px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'pulse-accent': {
                    '0%, 100%': { boxShadow: '0 0 0 0 rgba(0,255,127,0.4)' },
                    '50%':      { boxShadow: '0 0 0 8px rgba(0,255,127,0)' },
                },
                'glow-green': {
                    '0%, 100%': { textShadow: '0 0 8px rgba(0,255,127,0.6)' },
                    '50%':      { textShadow: '0 0 20px rgba(0,255,127,0.9)' },
                },
            },
            animation: {
                'fade-in':      'fade-in 0.15s ease-out',
                'pulse-accent': 'pulse-accent 2s ease-in-out infinite',
                'glow-green':   'glow-green 2s ease-in-out infinite',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
