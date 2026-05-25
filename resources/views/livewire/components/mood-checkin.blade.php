@if(auth()->check() && !auth()->user()->isAdmin())
<div>
<style>
    @@keyframes moodBadgeIn {
        from { opacity: 0; transform: scale(.75) translateY(1rem); }
        to   { opacity: 1; transform: scale(1)  translateY(0); }
    }
</style>

    {{-- ════════════ MODAL ════════════ --}}
    <div
        x-data="{
            open:        {{ $open      ? 'true' : 'false' }},
            respondeu:   {{ $respondeu ? 'true' : 'false' }},
            dispensou:   {{ $dispensou ? 'true' : 'false' }},
            componentId: '{{ $this->getId() }}',
            humor:   '',
            nota:    '',
            loading: false,

            gradients: {
                otimo:   'from-emerald-400 via-green-500 to-teal-500',
                bem:     'from-blue-400 via-indigo-500 to-violet-500',
                normal:  'from-amber-400 via-yellow-400 to-orange-400',
                pessimo: 'from-rose-400 via-red-500 to-pink-500',
                padrao:  'from-violet-400 via-blue-500 to-indigo-500',
            },
            msgs: {
                otimo:   'Que ótimo! Fico feliz em saber disso.',
                bem:     'Que bom! Continue assim.',
                normal:  'Tudo bem, dias assim acontecem.',
                pessimo: 'Sentimos muito. Você não está sozinho.',
            },

            get headerGradient() { return this.gradients[this.humor] || this.gradients.padrao },
            get headerMsg()      { return this.msgs[this.humor]      || '{{ now()->translatedFormat('l, d \d\e F') }}' },

            wire() { return Livewire.find(this.componentId) },

            fechar() {
                this.open     = false;
                this.dispensou = true;
                this.wire().dispensar();
            },

            async enviar() {
                if (!this.humor || this.loading) return;
                this.loading = true;
                try {
                    await this.wire().submeter(this.humor, this.nota);
                    this.open     = false;
                    this.respondeu = true;
                } catch(e) {
                    console.error('[MoodCheckin] erro ao submeter:', e);
                } finally {
                    this.loading = false;
                }
            },
        }"
        x-on:mood-abrir.window="open = true"
    >

        {{-- MODAL --}}
        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 z-[9998] flex items-end sm:items-center justify-center sm:p-4 bg-black/50"
            @click.self="fechar()"
        >
            <div class="w-full sm:max-w-md bg-white dark:bg-slate-900 sm:rounded-3xl rounded-t-3xl shadow-2xl overflow-hidden">

                {{-- Header com ícone dinâmico via x-show --}}
                <div
                    class="relative px-6 pt-8 pb-10 text-center overflow-hidden bg-gradient-to-br transition-all duration-500"
                    :class="headerGradient"
                >
                    <div class="absolute -top-6 -left-6 w-32 h-32 bg-white/10 rounded-full pointer-events-none"></div>
                    <div class="absolute -bottom-8 -right-4 w-40 h-40 bg-white/10 rounded-full pointer-events-none"></div>
                    <div class="absolute top-4 right-8 w-12 h-12 bg-white/10 rounded-full pointer-events-none"></div>

                    <button
                        type="button"
                        @click="fechar()"
                        class="absolute top-4 right-4 p-2 rounded-xl bg-white/20 hover:bg-white/30 text-white transition cursor-pointer z-10"
                    >
                        <x-lucide-x class="w-4 h-4" />
                    </button>

                    <div class="relative z-10 pointer-events-none select-none">
                        {{-- Ícone central — cada mood tem seu ícone, default quando nenhum selecionado --}}
                        <div class="flex items-center justify-center mb-3">
                            <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center">
                                <x-lucide-smile-plus class="w-9 h-9 text-white drop-shadow-md" x-show="humor === ''" />
                                <x-lucide-laugh  class="w-9 h-9 text-white drop-shadow-md" x-show="humor === 'otimo'"   x-cloak />
                                <x-lucide-smile  class="w-9 h-9 text-white drop-shadow-md" x-show="humor === 'bem'"     x-cloak />
                                <x-lucide-meh    class="w-9 h-9 text-white drop-shadow-md" x-show="humor === 'normal'"  x-cloak />
                                <x-lucide-frown  class="w-9 h-9 text-white drop-shadow-md" x-show="humor === 'pessimo'" x-cloak />
                            </div>
                        </div>
                        <h2 class="text-xl font-bold text-white drop-shadow-sm">Como você está hoje?</h2>
                        <p class="text-white/80 text-sm mt-1 font-medium" x-text="headerMsg"></p>
                    </div>
                </div>

                {{-- Corpo --}}
                <div class="px-5 pt-5 pb-6 space-y-5">

                    {{-- Opções de humor --}}
                    <div class="grid grid-cols-4 gap-2.5">

                        {{-- ÓTIMO — laugh icon (SVG inline para evitar :class em x-lucide-*) --}}
                        <button
                            type="button"
                            @click="humor = 'otimo'"
                            class="relative flex flex-col items-center gap-2 py-4 px-1 rounded-2xl border-2 transition-all duration-200 cursor-pointer"
                            :class="humor === 'otimo'
                                ? 'bg-green-50 dark:bg-green-900/30 border-green-400 dark:border-green-500 scale-105 shadow-lg shadow-green-200'
                                : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 hover:bg-white'"
                        >
                            <span x-show="humor === 'otimo'" class="absolute -top-2 -right-2 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center shadow-sm">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors"
                                  :class="humor === 'otimo' ? 'bg-green-100 dark:bg-green-900/40' : 'bg-slate-100 dark:bg-slate-700'">
                                {{-- laugh --}}
                                <svg class="w-5 h-5 transition-colors" :class="humor === 'otimo' ? 'text-green-600 dark:text-green-400' : 'text-slate-400'"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><path d="M18 13a6 6 0 0 1-6 5 6 6 0 0 1-6-5h12Z"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/>
                                </svg>
                            </span>
                            <span class="text-[11px] font-bold leading-none transition-colors"
                                  :class="humor === 'otimo' ? 'text-green-600 dark:text-green-400' : 'text-slate-400 dark:text-slate-500'">Ótimo</span>
                        </button>

                        {{-- BEM — smile icon --}}
                        <button
                            type="button"
                            @click="humor = 'bem'"
                            class="relative flex flex-col items-center gap-2 py-4 px-1 rounded-2xl border-2 transition-all duration-200 cursor-pointer"
                            :class="humor === 'bem'
                                ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-400 dark:border-blue-500 scale-105 shadow-lg shadow-blue-200'
                                : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 hover:bg-white'"
                        >
                            <span x-show="humor === 'bem'" class="absolute -top-2 -right-2 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shadow-sm">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors"
                                  :class="humor === 'bem' ? 'bg-blue-100 dark:bg-blue-900/40' : 'bg-slate-100 dark:bg-slate-700'">
                                {{-- smile --}}
                                <svg class="w-5 h-5 transition-colors" :class="humor === 'bem' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400'"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><path d="M8 13s1.5 2 4 2 4-2 4-2"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/>
                                </svg>
                            </span>
                            <span class="text-[11px] font-bold leading-none transition-colors"
                                  :class="humor === 'bem' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500'">Bem</span>
                        </button>

                        {{-- NORMAL — meh icon --}}
                        <button
                            type="button"
                            @click="humor = 'normal'"
                            class="relative flex flex-col items-center gap-2 py-4 px-1 rounded-2xl border-2 transition-all duration-200 cursor-pointer"
                            :class="humor === 'normal'
                                ? 'bg-amber-50 dark:bg-amber-900/30 border-amber-400 dark:border-amber-500 scale-105 shadow-lg shadow-amber-200'
                                : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 hover:bg-white'"
                        >
                            <span x-show="humor === 'normal'" class="absolute -top-2 -right-2 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center shadow-sm">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors"
                                  :class="humor === 'normal' ? 'bg-amber-100 dark:bg-amber-900/40' : 'bg-slate-100 dark:bg-slate-700'">
                                {{-- meh --}}
                                <svg class="w-5 h-5 transition-colors" :class="humor === 'normal' ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400'"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><line x1="8" x2="16" y1="15" y2="15"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/>
                                </svg>
                            </span>
                            <span class="text-[11px] font-bold leading-none transition-colors"
                                  :class="humor === 'normal' ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-500'">Normal</span>
                        </button>

                        {{-- PÉSSIMO — frown icon --}}
                        <button
                            type="button"
                            @click="humor = 'pessimo'"
                            class="relative flex flex-col items-center gap-2 py-4 px-1 rounded-2xl border-2 transition-all duration-200 cursor-pointer"
                            :class="humor === 'pessimo'
                                ? 'bg-rose-50 dark:bg-rose-900/30 border-rose-400 dark:border-rose-500 scale-105 shadow-lg shadow-rose-200'
                                : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 hover:bg-white'"
                        >
                            <span x-show="humor === 'pessimo'" class="absolute -top-2 -right-2 w-5 h-5 bg-rose-500 rounded-full flex items-center justify-center shadow-sm">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors"
                                  :class="humor === 'pessimo' ? 'bg-rose-100 dark:bg-rose-900/40' : 'bg-slate-100 dark:bg-slate-700'">
                                {{-- frown --}}
                                <svg class="w-5 h-5 transition-colors" :class="humor === 'pessimo' ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400'"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><path d="M16 16s-1.5-2-4-2-4 2-4 2"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/>
                                </svg>
                            </span>
                            <span class="text-[11px] font-bold leading-none transition-colors"
                                  :class="humor === 'pessimo' ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 dark:text-slate-500'">Péssimo</span>
                        </button>
                            <span class="text-[11px] font-bold leading-none transition-colors"
                                  :class="humor === 'pessimo' ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 dark:text-slate-500'">Péssimo</span>
                        </button>

                    </div>

                    {{-- Divisor --}}
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-px bg-slate-100 dark:bg-slate-800"></div>
                        <span class="text-[11px] text-slate-400 uppercase tracking-wide">Quer contar mais?</span>
                        <div class="flex-1 h-px bg-slate-100 dark:bg-slate-800"></div>
                    </div>

                    {{-- Textarea --}}
                    <div class="relative">
                        <textarea
                            x-model="nota"
                            rows="3"
                            maxlength="500"
                            placeholder="Opcional — conte um pouco mais sobre como está se sentindo..."
                            class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none placeholder-slate-300 dark:placeholder-slate-600 leading-relaxed"
                        ></textarea>
                        <span class="absolute bottom-2.5 right-3 text-[10px] text-slate-300 dark:text-slate-600 select-none" x-text="nota.length + '/500'"></span>
                    </div>

                    {{-- Botões de ação --}}
                    <form @submit.prevent="enviar()" class="flex gap-2.5 pt-1">
                        <button
                            type="button"
                            @click="fechar()"
                            class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-2xl transition cursor-pointer whitespace-nowrap"
                        >
                            Agora não
                        </button>

                        <button
                            type="submit"
                            :disabled="!humor || loading"
                            class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl text-sm font-bold text-white shadow-lg transition bg-gradient-to-r"
                            :class="[
                                headerGradient,
                                (!humor || loading) ? 'opacity-50 cursor-not-allowed' : 'hover:opacity-90 cursor-pointer'
                            ]"
                        >
                            <template x-if="!loading">
                                <span class="flex items-center gap-2">
                                    Enviar resposta
                                    <x-lucide-send class="w-4 h-4" />
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Enviando...
                                </span>
                            </template>
                        </button>
                    </form>

                    <p class="text-[11px] text-slate-400 dark:text-slate-600 text-center flex items-center justify-center gap-1">
                        <x-lucide-lock class="w-3 h-3" />
                        Suas respostas são confidenciais
                    </p>

                </div>
            </div>
        </div>

        {{-- ════════════ BADGE FLUTUANTE ════════════ --}}
        <div
            x-show="!open && !respondeu && dispensou"
            x-cloak
            class="fixed bottom-6 right-6 z-[9990]"
            style="animation: moodBadgeIn .4s ease-out forwards;"
        >
            <button
                type="button"
                @click="open = true; dispensou = false"
                class="group relative flex items-center gap-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl px-4 py-3.5 hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
            >
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500/10 to-violet-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none"></div>
                <div class="relative shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-violet-500 flex items-center justify-center shadow-md shadow-blue-500/25">
                        <x-lucide-smile class="w-5 h-5 text-white" />
                    </div>
                    <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-rose-500 border-2 border-white dark:border-slate-800"></span>
                    </span>
                </div>
                <div class="text-left relative z-10">
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Como você está hoje?</p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Check-in de humor disponível</p>
                </div>
                <x-lucide-arrow-right class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-blue-400 group-hover:translate-x-0.5 transition-all duration-200 relative z-10 shrink-0" />
            </button>
        </div>

    </div>{{-- /x-data --}}

</div>{{-- /root --}}
@endif
