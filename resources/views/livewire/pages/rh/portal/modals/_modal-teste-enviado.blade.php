    {{-- ── Modal: Teste enviado (Alpine — disparado via Livewire dispatch) ── --}}
<div x-data="{ show: false, link: '', nome: '', copied: false }"
     x-on:teste-enviado.window="
         link = $event.detail.link;
         nome = $event.detail.nome;
         copied = false;
         show = true;
     "
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-on:click.self="show = false">

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-end px-6 pt-5">
            <button type="button" x-on:click="show = false"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-600 dark:hover:text-slate-300 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        {{-- Conteúdo --}}
        <div class="px-8 pt-2 pb-8 flex flex-col items-center text-center">

            {{-- Ícone --}}
            <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-4">
                <x-lucide-send class="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
            </div>

            <h2 class="text-lg lato-black text-slate-800 dark:text-white mb-1">Teste enviado!</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mb-6">
                Compartilhe o link abaixo com
                <strong class="text-slate-700 dark:text-slate-300" x-text="nome"></strong>.
                O link expira em <strong>7 dias</strong>.
            </p>

            {{-- Link copiável --}}
            <div class="w-full mb-4">
                <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                    <x-lucide-link class="w-4 h-4 text-slate-400 shrink-0" />
                    <span class="flex-1 text-xs text-slate-600 dark:text-slate-300 lato-regular truncate text-left" x-text="link"></span>
                    <button type="button"
                            x-on:click="
                                navigator.clipboard.writeText(link);
                                copied = true;
                                setTimeout(() => copied = false, 2500);
                            "
                            class="cursor-pointer shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs lato-bold transition"
                            :class="copied
                                ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                                : 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50'">
                        <x-lucide-copy class="w-3.5 h-3.5" x-show="!copied" />
                        <x-lucide-check class="w-3.5 h-3.5" x-show="copied" />
                        <span x-text="copied ? 'Copiado!' : 'Copiar'"></span>
                    </button>
                </div>
            </div>

            {{-- Info expiração --}}
            <div class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 mb-6">
                <x-lucide-clock class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                <p class="text-xs text-amber-700 dark:text-amber-400 lato-regular text-left">
                    Este link expira em <strong>7 dias</strong>. Após isso, o candidato não conseguirá acessar o teste.
                </p>
            </div>

            <button type="button" x-on:click="show = false"
                    class="cursor-pointer w-full px-6 py-2.5 rounded-xl bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 dark:hover:bg-slate-600 text-white text-sm lato-bold transition">
                Fechar
            </button>
        </div>
    </div>

    @once
    <script src="{{ asset('js/vaga-geo.js') }}" defer></script>
    @endonce

</div>{{-- /modal: teste enviado --}}
