<template>
    <nav v-if="!isLauncher" class="fixed top-0 inset-x-0 z-50 h-16 bg-idc-surface"
        style="border-bottom: 2px solid #00ff7f;">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between gap-3">

            <!-- LEFT: IDC Logo -->
            <a href="https://idcgames.com" class="shrink-0 flex items-center gap-2">
                <img src="https://cdn7.idcgames.com/img/default/logo-idc-light.png"
                    alt="IDC Games" class="h-8 w-auto" loading="lazy" />
            </a>

            <!-- CENTER: Project menu (desktop) -->
            <div class="hidden lg:flex items-center gap-1 flex-1 justify-center">
                <slot name="nav-items" />
            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-2 shrink-0">

                <!-- Language selector -->
                <div class="relative" ref="langRef">
                    <button @click="langOpen = !langOpen"
                        class="flex items-center gap-1.5 px-2 py-1.5 rounded-md text-sm text-idc-muted hover:text-white hover:bg-white/5 transition-colors">
                        <span class="fi" :class="`fi-${currentFlag}`" style="font-size:1.1rem"></span>
                        <span class="hidden sm:inline uppercase text-xs font-semibold">{{ locale }}</span>
                        <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': langOpen }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <Transition name="dropdown">
                        <div v-if="langOpen"
                            class="absolute right-0 mt-1 w-56 bg-idc-surface border border-idc-border rounded-lg shadow-xl py-1 z-50 max-h-80 overflow-y-auto">
                            <a v-for="l in LANGS" :key="l.code"
                                :href="switchLangUrl(l.code)"
                                @click="langOpen = false"
                                class="flex items-center gap-2.5 px-3 py-1.5 text-sm text-idc-muted hover:text-white hover:bg-white/5 transition-colors"
                                :class="{ 'text-idc-accent font-semibold': l.code === locale }">
                                <span class="fi" :class="`fi-${l.flag}`" style="font-size:1rem"></span>
                                <span class="uppercase font-medium text-xs w-6">{{ l.code }}</span>
                                <span class="text-xs opacity-60">{{ l.name }}</span>
                            </a>
                        </div>
                    </Transition>
                </div>

                <!-- When logged in: notifications + user menu from project slot, or IDC session fallback -->
                <template v-if="isLoggedIn">
                    <slot name="notifications" />

                    <!-- Project-specific user menu (e.g. GiftsNavbar, ForumNavbar) -->
                    <slot name="user-menu">
                        <!-- Fallback: IDC widget session display -->
                        <div class="relative" ref="idcMenuRef">
                            <button @click="idcMenuOpen = !idcMenuOpen"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm text-idc-muted hover:text-white hover:bg-white/5 transition-colors">
                                <span class="w-7 h-7 rounded-full bg-idc-accent/20 text-idc-accent flex items-center justify-center text-xs font-bold uppercase">
                                    {{ idcSession?.nick?.charAt(0) ?? '?' }}
                                </span>
                                <span class="hidden sm:block max-w-[100px] truncate text-idc-light">{{ idcSession?.nick }}</span>
                                <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': idcMenuOpen }"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <Transition name="dropdown">
                                <div v-if="idcMenuOpen"
                                    class="absolute right-0 mt-1 w-52 bg-idc-surface border border-idc-border rounded-lg shadow-xl py-1 z-50">
                                    <div class="px-3 py-2 border-b border-idc-border">
                                        <p class="text-xs text-idc-muted">{{ t('signed_in_as', 'Signed in as') }}</p>
                                        <p class="text-sm font-semibold text-white truncate">{{ idcSession?.nick }}</p>
                                        <p class="text-xs text-idc-muted truncate">{{ idcSession?.email }}</p>
                                    </div>
                                    <a :href="`https://idcgames.com/${locale}/info/my-account`" target="_blank"
                                        class="dropdown-item">{{ t('my_account', 'My Account') }}</a>
                                    <button @click="doLogout"
                                        class="dropdown-item text-left w-full text-red-400 hover:text-red-300">{{ t('sign_out', 'Sign out') }}</button>
                                </div>
                            </Transition>
                        </div>
                    </slot>
                </template>

                <!-- When NOT logged in: Sign in + Join free -->
                <template v-else>
                    <slot name="notifications" />
                    <slot name="user-menu" />
                    <button @click="openLogin"
                        class="hidden sm:block px-4 py-1.5 text-sm font-medium text-idc-muted hover:text-white transition-colors">
                        {{ t('sign_in', 'Sign in') }}
                    </button>
                    <button @click="openRegister"
                        class="px-4 py-1.5 bg-idc-accent hover:bg-idc-accent-hover text-black text-sm font-bold rounded-md transition-colors">
                        {{ t('join_free', 'Join free') }}
                    </button>
                </template>

                <!-- Mobile hamburger -->
                <button @click="mobileOpen = !mobileOpen"
                    class="lg:hidden p-1.5 rounded-md text-idc-muted hover:text-white hover:bg-white/5 transition-colors ml-1">
                    <svg v-if="!mobileOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <Transition name="mobile-menu">
            <div v-if="mobileOpen"
                class="lg:hidden absolute top-16 inset-x-0 bg-idc-surface border-b border-idc-border shadow-xl z-40">
                <div class="px-4 py-3 space-y-1">
                    <slot name="mobile-nav-items" />
                    <template v-if="!isLoggedIn">
                        <button @click="openLogin(); mobileOpen = false"
                            class="mobile-nav-link w-full text-left">
                            {{ t('sign_in', 'Sign in') }}
                        </button>
                        <button @click="openRegister(); mobileOpen = false"
                            class="mobile-nav-link w-full text-left text-idc-accent font-semibold">
                            {{ t('join_free', 'Join free') }}
                        </button>
                    </template>
                    <template v-else>
                        <a :href="`https://idcgames.com/${locale}/info/my-account`" target="_blank"
                            class="mobile-nav-link">{{ idcSession?.nick ?? t('my_account', 'My Account') }}</a>
                        <button @click="doLogout" class="mobile-nav-link w-full text-left text-red-400">{{ t('sign_out', 'Sign out') }}</button>
                    </template>
                </div>
            </div>
        </Transition>
    </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t }  = useI18n()
const page   = usePage()
const locale = computed(() => page.props.locale ?? 'en')
const isLauncher = computed(() => /idclauncher/i.test(navigator.userAgent))

// ── IDC Auth Session ──────────────────────────────────────────────────────
const idcSession  = ref(null)
const idcMenuOpen = ref(false)
const idcMenuRef  = ref(null)

function refreshSession() {
    const s = window.IDCAuthWidget?.getSession?.()
    idcSession.value = (s && s.id) ? s : null
}

const isLoggedIn = computed(() => {
    // 1. Laravel session (if middleware set auth.user)
    if (page.props.auth?.user) return true
    // 2. IDC widget session
    if (idcSession.value) return true
    return false
})

function openLogin()    { window.IDCAuthWidget?.open('login')    }
function openRegister() { window.IDCAuthWidget?.open('register') }

async function doLogout() {
    idcMenuOpen.value = false
    mobileOpen.value  = false
    const token = idcSession.value?.token ?? ''

    // 1. POST logout — API returns client_cleanup.cookies list to delete
    try {
        const res  = await fetch(`${IDC_AUTH_URL}/api/web/logout`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: token ? `token=${encodeURIComponent(token)}` : '',
        })
        const data = await res.json()

        // 2. Delete every cookie the server says to clean up
        const cleanup = data?.client_cleanup ?? {}
        const cookies = cleanup.cookies ?? []
        const paths   = cleanup.paths?.length ? cleanup.paths : ['/']
        const domains = cleanup.domains?.filter(Boolean) ?? []  // empty string = no domain attr

        const past = 'Thu, 01 Jan 1970 00:00:00 GMT'
        cookies.forEach(name => {
            paths.forEach(path => {
                // Delete without domain (current host)
                document.cookie = `${name}=; expires=${past}; path=${path}`
                // Delete with each explicit domain
                domains.forEach(domain => {
                    document.cookie = `${name}=; expires=${past}; path=${path}; domain=${domain}`
                })
            })
        })
    } catch {}

    // 3. Clear local session state — navbar shows Sign in / Join free instantly
    idcSession.value = null

    // 4. Destroy current widget instance (session still in memory)
    document.querySelector('script[src*="idc-auth-widget"]')?.remove()
    window.IDCAuthWidget = undefined

    // 5. Reload widget fresh — now without valid token, getSession() = null
    //    Widget is ready for Sign In / Join Free clicks immediately
    loadAuthWidget()
}

// ── Language ─────────────────────────────────────────────────────────────
const langOpen = ref(false)
const langRef  = ref(null)

const LANG_FLAGS = { cs:'cz', el:'gr', da:'dk', uk:'ua', bs:'ba', sr:'rs', vi:'vn', ja:'jp', ar:'sa', sv:'se', zh:'cn', ko:'kr' }
const LANGS = [
    {code:'ar',name:'العربية',   flag:'sa'},{code:'bs',name:'Bosanski',   flag:'ba'},
    {code:'cs',name:'Čeština',   flag:'cz'},{code:'da',name:'Dansk',      flag:'dk'},
    {code:'de',name:'Deutsch',   flag:'de'},{code:'el',name:'Ελληνικά',  flag:'gr'},
    {code:'en',name:'English',   flag:'gb'},{code:'es',name:'Español',    flag:'es'},
    {code:'fi',name:'Suomi',     flag:'fi'},{code:'fr',name:'Français',   flag:'fr'},
    {code:'hr',name:'Hrvatski',  flag:'hr'},{code:'it',name:'Italiano',   flag:'it'},
    {code:'ja',name:'日本語',    flag:'jp'},{code:'ko',name:'한국어',     flag:'kr'},
    {code:'nl',name:'Nederlands',flag:'nl'},{code:'no',name:'Norsk',      flag:'no'},
    {code:'pl',name:'Polski',    flag:'pl'},{code:'pt',name:'Português',  flag:'pt'},
    {code:'ro',name:'Română',    flag:'ro'},{code:'ru',name:'Русский',    flag:'ru'},
    {code:'sr',name:'Srpski',    flag:'rs'},{code:'sv',name:'Svenska',    flag:'se'},
    {code:'th',name:'ไทย',      flag:'th'},{code:'tr',name:'Türkçe',     flag:'tr'},
    {code:'uk',name:'Українська',flag:'ua'},{code:'vi',name:'Tiếng Việt',flag:'vn'},
    {code:'zh',name:'中文',      flag:'cn'},
]
const currentFlag = computed(() => LANG_FLAGS[locale.value] ?? locale.value)
function switchLangUrl(newLang) {
    return window.location.pathname.replace(/^\/([a-z]{2})\b/, `/${newLang}`)
}

// ── Mobile menu ───────────────────────────────────────────────────────────
const mobileOpen = ref(false)

// ── Click outside ─────────────────────────────────────────────────────────
function handleClickOutside(e) {
    if (langRef.value && !langRef.value.contains(e.target))   langOpen.value   = false
    if (idcMenuRef.value && !idcMenuRef.value.contains(e.target)) idcMenuOpen.value = false
}

// ── Load IDC Auth Widget from the navbar ──────────────────────────────────
// Read auth URL from meta tag (set by blade from .env IDC_AUTH_URL)
const IDC_AUTH_URL = document.querySelector('meta[name="idc-auth-url"]')?.content ?? 'https://auth.idcgames.com'

function loadAuthWidget() {
    if (window.IDCAuthWidget) { refreshSession(); return }
    const s = document.createElement('script')
    s.setAttribute('data-lang', locale.value)
    s.setAttribute('data-api-base', IDC_AUTH_URL)
    s.setAttribute('data-redirect', window.location.origin + '/' + locale.value)
    s.setAttribute('data-css', 'tailwind')
    s.src = IDC_AUTH_URL + '/widget/idc-auth-widget.js'
    s.onload = () => refreshSession()
    document.head.appendChild(s)
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    loadAuthWidget()
})
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.nav-link        { @apply px-3 py-1.5 rounded-md text-sm font-medium text-idc-muted hover:text-white hover:bg-white/5 transition-colors; }
.nav-link.active { @apply text-idc-accent bg-idc-accent/10 border border-idc-accent/20; }
.mobile-nav-link { @apply block px-3 py-2 rounded-md text-sm font-medium text-idc-muted hover:text-white hover:bg-white/5 transition-colors; }
.dropdown-item   { @apply flex items-center gap-2 px-3 py-2 text-sm text-idc-muted hover:text-white hover:bg-white/5 transition-colors cursor-pointer; }
.dropdown-enter-active, .dropdown-leave-active { transition: opacity 0.1s ease, transform 0.1s ease; }
.dropdown-enter-from, .dropdown-leave-to       { opacity: 0; transform: translateY(-4px); }
.mobile-menu-enter-active, .mobile-menu-leave-active { transition: opacity 0.15s ease, transform 0.15s ease; }
.mobile-menu-enter-from, .mobile-menu-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
