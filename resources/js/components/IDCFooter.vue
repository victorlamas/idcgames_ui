<template>
    <footer class="bg-idc-surface border-t border-idc-border mt-auto">

        <!-- Social row -->
        <div class="border-b border-idc-border py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-xs font-bold uppercase tracking-widest text-idc-accent mb-4">
                    {{ t('footer_follow') }}
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a v-for="s in socials" :key="s.name" :href="s.url" target="_blank" rel="noopener noreferrer"
                        :aria-label="s.name"
                        class="w-12 h-12 rounded-full border-2 border-idc-accent/50 flex items-center justify-center text-idc-accent hover:bg-idc-accent hover:text-black hover:border-idc-accent transition-all duration-200 hover:scale-110">
                        <span class="sr-only">{{ s.name }}</span>
                        <svg style="width:20px;height:20px;display:block;flex-shrink:0" fill="currentColor" viewBox="0 0 24 24" v-html="s.icon" />
                    </a>
                </div>
            </div>
        </div>

        <!-- Main footer content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row items-start gap-8">
                <!-- Logo -->
                <div class="shrink-0">
                    <a href="https://idcgames.com" target="_blank" rel="noopener noreferrer">
                        <img src="https://cdn7.idcgames.com/img/default/logo-idc-light.png"
                            alt="IDC Games" class="h-14 w-auto" loading="lazy" />
                    </a>
                </div>

                <!-- Columns -->
                <div class="flex flex-wrap gap-4 flex-1">
                    <a v-for="col in columns" :key="col.key" :href="col.url"
                        target="_blank" rel="noopener noreferrer"
                        class="px-4 py-2 border border-idc-border text-xs font-bold uppercase tracking-wider text-idc-muted hover:text-white hover:border-idc-accent/50 transition-colors rounded-sm">
                        {{ t(col.key) }}
                    </a>
                </div>
            </div>

            <!-- Distribute CTA -->
            <div class="mt-6">
                <a href="https://about.idcgames.com/distribute-your-game-with-idc-games"
                    target="_blank" rel="noopener noreferrer"
                    class="inline-block px-6 py-2.5 border border-white/30 text-xs font-bold uppercase tracking-widest text-white hover:bg-white hover:text-black transition-colors rounded-sm">
                    {{ t('footer_distribute') }}
                </a>
            </div>
        </div>

        <!-- Legal row -->
        <div class="border-t border-idc-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex flex-wrap justify-center md:justify-start gap-x-4 gap-y-1">
                    <a v-for="l in legalLinks" :key="l.key" :href="l.url" target="_blank" rel="noopener noreferrer"
                        class="text-xs text-idc-muted hover:text-white transition-colors uppercase tracking-wide">
                        {{ t(l.key) }}
                    </a>
                </div>
                <p class="text-xs text-idc-muted">© IDC GAMES {{ currentYear }}. {{ t('footer_rights', 'All rights reserved.') }}</p>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const page  = usePage()
const lang  = computed(() => page.props.locale ?? 'en')
const currentYear = new Date().getFullYear()

const socials = [
    { name: 'Facebook',  url: 'https://www.facebook.com/IDCgames',           icon: '<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>' },
    { name: 'X',         url: 'https://twitter.com/IDCgames',                icon: '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>' },
    { name: 'Instagram', url: 'https://www.instagram.com/idcgames/',         icon: '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path fill="none" stroke="currentColor" stroke-width="2" d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line fill="none" stroke="currentColor" stroke-width="2" x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>' },
    { name: 'Pinterest', url: 'https://www.pinterest.com/IDCgames/',         icon: '<path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/>' },
    { name: 'YouTube',   url: 'https://www.youtube.com/user/IDCgames',       icon: '<path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/>' },
    { name: 'Twitch',    url: 'https://www.twitch.tv/idcgames',              icon: '<path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/>' },
    { name: 'VK',        url: 'https://vk.com/idcgames_official',            icon: '<path d="M11.701 18.771H12.87s.373-.041.563-.245c.175-.188.169-.543.169-.543s-.024-1.659.747-1.903c.759-.24 1.734 1.604 2.768 2.312.782.535 1.376.418 1.376.418l2.766-.038s1.447-.09.760-1.221c-.056-.094-.399-.841-2.056-2.379-1.733-1.608-1.501-1.348.587-4.131 1.272-1.694 1.78-2.729 1.621-3.17-.151-.42-1.083-.309-1.083-.309l-3.113.02s-.231-.031-.402.079c-.168.109-.276.362-.276.362s-.492 1.311-1.148 2.424c-1.384 2.35-1.938 2.474-2.162 2.327-.525-.34-.394-1.366-.394-2.095 0-2.276.345-3.226-.672-3.472-.338-.082-.586-.136-1.449-.145-1.107-.011-2.044.004-2.574.264-.353.172-.625.555-.459.577.204.027.667.125.913.459.317.428.306 1.39.306 1.39s.182 2.677-.425 3.01c-.416.227-.987-.236-2.211-2.353-.628-1.086-1.103-2.287-1.103-2.287s-.091-.246-.257-.378c-.2-.158-.48-.208-.48-.208l-2.958.02s-.444.012-.607.206c-.145.172-.011.529-.011.529s2.316 5.419 4.937 8.151c2.404 2.507 5.136 2.342 5.136 2.342z"/>' },
    { name: 'TikTok',    url: 'https://www.tiktok.com/@idcgames',            icon: '<path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.34 6.34 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.65a8.16 8.16 0 004.77 1.52V6.71a4.85 4.85 0 01-1-.02z"/>' },
    { name: 'Discord',   url: 'https://discord.gg/Nztb8hSvjF',              icon: '<path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03z"/>' },
    { name: 'Reddit',    url: 'https://www.reddit.com/r/idcgames/',          icon: '<path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.701zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.094.33.33 0 0 0 0 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 0 0 .029-.463.33.33 0 0 0-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.196-2.512-.73a.326.326 0 0 0-.232-.095z"/>' },
    { name: 'LinkedIn',  url: 'https://www.linkedin.com/company/idcgames/',  icon: '<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/>' },
    { name: 'Telegram',  url: 'https://t.me/idcgamess',                      icon: '<path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>' },
]

const columns = [
    { key: 'footer_about',      url: 'https://about.idcgames.com/' },
    { key: 'footer_services',   url: 'https://about.idcgames.com/game-publishing-services-and-products/README.html' },
    { key: 'footer_tools',      url: 'https://about.idcgames.com/useful-links.html' },
    { key: 'footer_dev_corner', url: 'https://developer.idcgames.com/' },
    { key: 'footer_blog',       url: `https://idcgames.com/${lang.value}/blog/` },
]

const legalLinks = computed(() => [
    { key: 'footer_terms',   url: `https://idcgames.com/${lang.value}/doc/terms-of-use` },
    { key: 'footer_privacy', url: `https://idcgames.com/${lang.value}/doc/privacy-policy-and-cookies` },
    { key: 'footer_cookies', url: `https://idcgames.com/${lang.value}/doc/privacy-policy-and-cookies` },
    { key: 'footer_return',  url: `https://idcgames.com/${lang.value}/doc/return-policy` },
    { key: 'footer_press',   url: 'https://imgur.com/gallery/ukqY9bO' },
])
</script>
