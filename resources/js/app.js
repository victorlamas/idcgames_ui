/**
 * IDCGames UI — Base JS
 *
 * setupInertiaApp — helper que cada proyecto hijo llama pasando su config.
 *
 * Uso en el proyecto hijo (resources/js/app.js):
 *
 *   import { setupInertiaApp } from '../../idcgames_ui/resources/js/app.js'
 *   import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
 *
 *   setupInertiaApp({
 *     appName: 'Gifts',
 *     resolve: (name) => resolvePageComponent(
 *       `./Pages/${name}.vue`,
 *       import.meta.glob('./Pages/**\/*.vue')
 *     ),
 *   })
 */

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createPinia } from 'pinia'

export function setupInertiaApp({ resolve, appName = 'IDCGames', plugins = [] } = {}) {
    createInertiaApp({
        title: (title) => title ? `${title} — ${appName}` : appName,

        resolve,

        setup({ el, App, props, plugin }) {
            const pinia = createPinia()

            const app = createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(pinia)

            plugins.forEach((p) => app.use(p))

            app.mount(el)
        },

        progress: {
            color:       '#00ff7f',  // idc-accent Spring Green
            showSpinner: false,
        },
    })
}
