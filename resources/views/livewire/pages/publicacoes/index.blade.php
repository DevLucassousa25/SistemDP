<div>
{{-- Quill CSS + JS --}}
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    function quillEditor() {
        return {
            editor: null,
            init() {
                // Guard: se já existe uma instância, não recria
                if (this.editor) return;

                this.editor = new Quill(this.$refs.quillDiv, {
                    theme: 'snow',
                    placeholder: 'Escreva o conteúdo da publicação aqui...',
                    modules: {
                        toolbar: [
                            [{ header: [2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['link'],
                            ['clean']
                        ]
                    }
                });

                // Popula com conteúdo existente (modo edição)
                const initial = this.$wire.content;
                if (initial) this.editor.root.innerHTML = initial;

                // Sincroniza mudanças para o Livewire + autosave debounced
                this.editor.on('text-change', () => {
                    this.$wire.set('content', this.editor.root.innerHTML);
                    // Atualiza estimativa de leitura
                    const words = this.editor.getText().trim().split(/\s+/).filter(w => w.length > 0).length;
                    const mins  = Math.max(1, Math.ceil(words / 200));
                    const el = document.getElementById('quill-reading-time');
                    if (el) el.textContent = words + ' palavras · aprox. ' + mins + ' min de leitura';
                    // Autosave (só no modo edição)
                    clearTimeout(window._quillAutosave);
                    window._quillAutosave = setTimeout(() => { this.$wire.autosave(); }, 2500);
                });

                // Atualiza o editor quando o Livewire trocar o post (openEdit)
                this.$watch('$wire.content', val => {
                    if (val !== this.editor.root.innerHTML) {
                        this.editor.root.innerHTML = val || '';
                    }
                });
            }
        };
    }
</script>
<style>
    .ql-toolbar { border-radius: 0.75rem 0.75rem 0 0 !important; border-color: rgb(226 232 240) !important; background: #f8fafc; }
    .ql-container { border-radius: 0 0 0.75rem 0.75rem !important; border-color: rgb(226 232 240) !important; font-size: 0.875rem; min-height: 180px; }
    .ql-editor { min-height: 180px; line-height: 1.7; }
    .dark .ql-toolbar { border-color: rgb(51 65 85) !important; background: rgb(15 23 42); }
    .dark .ql-container { border-color: rgb(51 65 85) !important; background: rgb(15 23 42); color: rgb(226 232 240); }
    .dark .ql-toolbar button svg, .dark .ql-toolbar .ql-picker { color: rgb(148 163 184); fill: rgb(148 163 184); stroke: rgb(148 163 184); }
    .dark .ql-toolbar button:hover svg, .dark .ql-toolbar button.ql-active svg { color: rgb(99 102 241); fill: rgb(99 102 241); stroke: rgb(99 102 241); }
    .dark .ql-picker-label { color: rgb(148 163 184) !important; }
    .dark .ql-picker-options { background: rgb(30 41 59) !important; border-color: rgb(51 65 85) !important; }
    .ql-editor.ql-blank::before { color: rgb(148 163 184); font-style: normal; }
</style>

<div class="p-4 sm:p-6 lg:p-8 space-y-6">

    {{-- ── Cabeçalho ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white lato-black tracking-tight">
                Publicações
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular mt-1">
                Gerencie notícias, comunicados e avisos para a plataforma
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button wire:click="openTagManager" type="button"
                    class="inline-flex items-center gap-2 px-3 py-2.5 rounded-xl bg-white dark:bg-slate-800
                           border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300
                           text-sm lato-bold hover:border-indigo-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition cursor-pointer">
                <x-lucide-tags class="w-4 h-4" /> Tags
            </button>
            <button wire:click="openCreate" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                           text-white text-sm lato-bold transition shadow-sm cursor-pointer">
                <x-lucide-plus class="w-4 h-4" /> Nova publicação
            </button>
        </div>
    </div>

    {{-- ── Cards de estatísticas ──────────────────────────────────────── --}}
    @php $stats = $this->stats; @endphp
    <div class="grid grid-cols-3 sm:grid-cols-3 lg:grid-cols-6 gap-3">

        @php
            $statCards = [
                ['total',     $stats['total'],     'Total',           'layout-list',    'indigo'],
                ['publicado', $stats['publicado'],  'Publicados',      'globe',          'emerald'],
                ['rascunho',  $stats['rascunho'],   'Rascunhos',       'file-pen-line',  'slate'],
                ['arquivado', $stats['arquivado'],  'Arquivados',      'archive',        'orange'],
                ['pinned',    $stats['pinned'],     'Fixados',         'pin',            'amber'],
                ['mandatory', $stats['mandatory'],  'Leit. obrigatória','book-open-check','red'],
            ];
        @endphp

        @foreach ($statCards as [$key, $value, $label, $icon, $color])
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-4 flex items-center gap-3 cursor-pointer hover:border-{{ $color }}-300 transition"
                 wire:click="{{ $key === 'total' ? 'clearFilters' : ($key === 'pinned' || $key === 'mandatory' ? '' : '$set(\'filterStatus\', \'' . $key . '\')') }}">
                <div class="w-9 h-9 rounded-xl bg-{{ $color }}-50 dark:bg-{{ $color }}-900/30 flex items-center justify-center shrink-0">
                    <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4 text-{{ $color }}-500" />
                </div>
                <div>
                    <p class="text-xl lato-black text-slate-800 dark:text-white leading-none">{{ $value }}</p>
                    <p class="text-[10px] text-slate-400 lato-regular mt-0.5 leading-tight">{{ $label }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Filtros ─────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-3">

        {{-- Busca --}}
        <div class="relative w-full">
            <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Buscar publicações..."
                   class="w-full pl-10 pr-9 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                          bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                          focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400
                          placeholder-slate-400 transition" />
            @if ($search)
                <button wire:click="$set('search','')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                    <x-lucide-x class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>

        {{-- Buscar no conteúdo (aparece quando há busca ativa) --}}
        @if ($search)
            <label class="flex items-center gap-2 text-xs lato-regular text-slate-500 dark:text-slate-400 cursor-pointer select-none -mt-1">
                <input type="checkbox" wire:model.live="searchInContent"
                       class="w-3.5 h-3.5 rounded accent-indigo-500 cursor-pointer" />
                Buscar também no conteúdo
            </label>
        @endif

        {{-- Dropdowns de filtro --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-2">

            {{-- Tipo --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button"
                        class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                        {{ $filterType ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                    <div class="flex items-center gap-1.5 truncate">
                        <x-lucide-tag class="w-3.5 h-3.5 shrink-0" />
                        <span class="truncate text-xs lato-bold">{{ match($filterType) {'noticia'=>'Notícia','comunicado'=>'Comunicado','evento'=>'Evento','aviso'=>'Aviso',default=>'Tipo'} }}</span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute mt-1 w-44 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                    <button @click="open=false; $wire.set('filterType','')" type="button" class="w-full text-left px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <x-lucide-tag class="w-3.5 h-3.5" /> Todos os tipos
                    </button>
                    <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                    @foreach (['noticia'=>['Notícia','newspaper','blue'],'comunicado'=>['Comunicado','megaphone','violet'],'evento'=>['Evento','calendar-heart','emerald'],'aviso'=>['Aviso','triangle-alert','amber']] as $val=>[$label,$icon,$c])
                        <button @click="open=false; $wire.set('filterType','{{ $val }}')" type="button"
                                class="w-full text-left px-3 py-2 text-xs flex items-center gap-2 transition
                                {{ $filterType===$val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                            <x-dynamic-component :component="'lucide-'.$icon" class="w-3.5 h-3.5 text-{{ $c }}-500" /> {{ $label }}
                            @if($filterType===$val) <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" /> @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Status --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button"
                        class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                        {{ $filterStatus ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                    <div class="flex items-center gap-1.5 truncate">
                        @php $sdot = match($filterStatus){'publicado'=>'bg-emerald-400','rascunho'=>'bg-slate-400','arquivado'=>'bg-orange-400',default=>null}; @endphp
                        @if($sdot) <span class="w-2 h-2 rounded-full {{ $sdot }} shrink-0"></span>
                        @else <x-lucide-circle-check class="w-3.5 h-3.5 shrink-0" /> @endif
                        <span class="truncate text-xs lato-bold">{{ match($filterStatus){'publicado'=>'Publicado','rascunho'=>'Rascunho','arquivado'=>'Arquivado',default=>'Status'} }}</span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute mt-1 w-40 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                    <button @click="open=false; $wire.set('filterStatus','')" type="button" class="w-full text-left px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <x-lucide-circle-check class="w-3.5 h-3.5" /> Todos
                    </button>
                    <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                    @foreach (['publicado'=>['Publicado','bg-emerald-400'],'rascunho'=>['Rascunho','bg-slate-400'],'arquivado'=>['Arquivado','bg-orange-400']] as $val=>[$label,$dot])
                        <button @click="open=false; $wire.set('filterStatus','{{ $val }}')" type="button"
                                class="w-full text-left px-3 py-2 text-xs flex items-center gap-2 transition
                                {{ $filterStatus===$val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                            <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span> {{ $label }}
                            @if($filterStatus===$val) <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" /> @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Público --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button"
                        class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                        {{ $filterAudience ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                    <div class="flex items-center gap-1.5 truncate">
                        <x-lucide-users class="w-3.5 h-3.5 shrink-0" />
                        <span class="truncate text-xs lato-bold">{{ match($filterAudience){'todos'=>'Todos','gerentes'=>'Gerentes','funcionarios'=>'Funcionários','rh'=>'RH / DP',default=>'Público'} }}</span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute mt-1 w-44 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                    <button @click="open=false; $wire.set('filterAudience','')" type="button" class="w-full text-left px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <x-lucide-users class="w-3.5 h-3.5" /> Todos
                    </button>
                    <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                    @foreach (['todos'=>'Todos','gerentes'=>'Gerentes','funcionarios'=>'Funcionários','rh'=>'RH / DP'] as $val=>$label)
                        <button @click="open=false; $wire.set('filterAudience','{{ $val }}')" type="button"
                                class="w-full text-left px-3 py-2 text-xs flex items-center justify-between transition
                                {{ $filterAudience===$val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                            {{ $label }}
                            @if($filterAudience===$val) <x-lucide-check class="w-3.5 h-3.5 text-indigo-500" /> @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Tags --}}
            @if ($this->availableTags->isNotEmpty())
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" type="button"
                            class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                            {{ $filterTag ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                        <div class="flex items-center gap-1.5 truncate">
                            <x-lucide-hash class="w-3.5 h-3.5 shrink-0" />
                            <span class="truncate text-xs lato-bold">
                                {{ $filterTag ? ($this->availableTags->find($filterTag)?->name ?? 'Tag') : 'Tag' }}
                            </span>
                        </div>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute mt-1 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1 max-h-48 overflow-y-auto">
                        <button @click="open=false; $wire.set('filterTag','')" type="button" class="w-full text-left px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <x-lucide-hash class="w-3.5 h-3.5" /> Todas as tags
                        </button>
                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                        @foreach ($this->availableTags as $tag)
                            <button @click="open=false; $wire.set('filterTag','{{ $tag->id }}')" type="button"
                                    class="w-full text-left px-3 py-2 text-xs flex items-center gap-2 transition
                                    {{ $filterTag == $tag->id ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                <span class="w-2 h-2 rounded-full {{ $tag->dot_class }} shrink-0"></span>
                                {{ $tag->name }}
                                @if($filterTag == $tag->id) <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" /> @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Ordenar por --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button"
                        class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                        {{ $sortBy !== 'recent' ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                    <div class="flex items-center gap-1.5 truncate">
                        <x-lucide-arrow-up-down class="w-3.5 h-3.5 shrink-0" />
                        <span class="truncate text-xs lato-bold">{{ match($sortBy) { 'popular' => 'Mais lidos', 'mandatory' => 'Obrigatórios', default => 'Mais recentes' } }}</span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute mt-1 w-44 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                    @foreach (['recent' => ['Mais recentes', 'clock'], 'popular' => ['Mais lidos', 'eye'], 'mandatory' => ['Obrigatórios', 'book-open-check']] as $val => [$label, $icon])
                        <button @click="open=false; $wire.set('sortBy','{{ $val }}')" type="button"
                                class="w-full text-left px-3 py-2 text-xs flex items-center gap-2 transition
                                {{ $sortBy === $val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                            <x-dynamic-component :component="'lucide-' . $icon" class="w-3.5 h-3.5" /> {{ $label }}
                            @if($sortBy === $val) <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" /> @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Limpar --}}
            @if ($search || $filterType || $filterStatus || $filterAudience || $filterTag)
                <button wire:click="clearFilters" type="button"
                        class="flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs lato-bold
                               text-red-400 hover:text-red-600 border border-red-200 dark:border-red-900/50
                               hover:border-red-400 rounded-xl bg-red-50 dark:bg-red-900/10 transition cursor-pointer">
                    <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar
                </button>
            @endif
        </div>

        {{-- Chips --}}
        @if ($search || $filterType || $filterStatus || $filterAudience || $filterTag)
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[10px] text-slate-400 lato-regular">Ativos:</span>
                @if ($search)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] lato-bold">
                        <x-lucide-search class="w-2.5 h-2.5" /> "{{ Str::limit($search,20) }}"
                        <button wire:click="$set('search','')" class="ml-0.5 hover:text-slate-900"><x-lucide-x class="w-2.5 h-2.5" /></button>
                    </span>
                @endif
                @if ($filterType)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[10px] lato-bold">
                        {{ match($filterType){'noticia'=>'Notícia','comunicado'=>'Comunicado','evento'=>'Evento','aviso'=>'Aviso',default=>$filterType} }}
                        <button wire:click="$set('filterType','')" class="ml-0.5 hover:text-indigo-900"><x-lucide-x class="w-2.5 h-2.5" /></button>
                    </span>
                @endif
                @if ($filterStatus)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[10px] lato-bold">
                        {{ match($filterStatus){'publicado'=>'Publicado','rascunho'=>'Rascunho','arquivado'=>'Arquivado',default=>$filterStatus} }}
                        <button wire:click="$set('filterStatus','')" class="ml-0.5 hover:text-indigo-900"><x-lucide-x class="w-2.5 h-2.5" /></button>
                    </span>
                @endif
                @if ($filterTag)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[10px] lato-bold">
                        <x-lucide-hash class="w-2.5 h-2.5" /> {{ $this->availableTags->find($filterTag)?->name }}
                        <button wire:click="$set('filterTag','')" class="ml-0.5 hover:text-indigo-900"><x-lucide-x class="w-2.5 h-2.5" /></button>
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- ── Grid de publicações ─────────────────────────────────────────── --}}
    @php $posts = $this->posts; @endphp

    @if ($posts->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 py-20 flex flex-col items-center justify-center text-center">
            <x-lucide-newspaper class="w-12 h-12 text-slate-200 dark:text-slate-600 mb-4" />
            <p class="text-slate-500 dark:text-slate-400 lato-bold">Nenhuma publicação encontrada</p>
            <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular mt-1">
                {{ ($search || $filterType || $filterStatus || $filterAudience || $filterTag) ? 'Tente ajustar os filtros.' : 'Crie sua primeira publicação.' }}
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($posts as $post)
                @php
                    $tc = ['noticia'=>'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300','comunicado'=>'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300','evento'=>'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300','aviso'=>'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'][$post->type] ?? 'bg-slate-100 text-slate-600';
                    $sc = ['publicado'=>'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300','rascunho'=>'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300','arquivado'=>'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'][$post->status] ?? 'bg-slate-100 text-slate-500';
                    $ti = ['noticia'=>'newspaper','comunicado'=>'megaphone','evento'=>'calendar-heart','aviso'=>'triangle-alert'][$post->type] ?? 'file-text';
                    $coverBg = ['noticia'=>'from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/40','comunicado'=>'from-blue-500 to-violet-100 dark:from-blue-500/20 dark:to-violet-900/40','evento'=>'from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/40','aviso'=>'from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-900/40'][$post->type] ?? 'from-slate-50 to-slate-100';
                    $iconColor = ['noticia'=>'text-blue-400','comunicado'=>'text-indigo-400','evento'=>'text-emerald-400','aviso'=>'text-amber-400'][$post->type] ?? 'text-slate-300';
                    $readsCount = $post->reads->count();
                @endphp

                <div class="bg-white dark:bg-slate-800 rounded-2xl border overflow-hidden flex flex-col group
                            hover:shadow-md transition-all duration-200
                            {{ $post->pinned ? 'border-amber-300 dark:border-amber-700 ring-1 ring-amber-200 dark:ring-amber-800' : 'border-slate-200 dark:border-slate-700 hover:border-indigo-200 dark:hover:border-indigo-800' }}">

                    {{-- Capa --}}
                    @if ($post->cover_image)
                        <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900 cursor-pointer"
                             style="aspect-ratio:16/7"
                             wire:click="openPreview({{ $post->id }})">
                            <img src="{{ Storage::url($post->cover_image) }}" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-300" />
                            <div class="absolute top-2 left-2 flex gap-1.5">
                                @if ($post->pinned)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-400/90 text-white text-[10px] lato-bold shadow">
                                        <x-lucide-pin class="w-2.5 h-2.5" /> Fixado
                                    </span>
                                @endif
                                @if ($post->is_mandatory_read)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-500/90 text-white text-[10px] lato-bold shadow">
                                        <x-lucide-book-open-check class="w-2.5 h-2.5" /> Obrigatório
                                    </span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="relative h-28 flex items-center justify-center bg-gradient-to-br {{ $coverBg }} cursor-pointer"
                             wire:click="openPreview({{ $post->id }})">
                            <x-dynamic-component :component="'lucide-'.$ti" class="w-10 h-10 opacity-20 {{ $iconColor }}" />
                            <div class="absolute top-2 left-2 flex gap-1.5">
                                @if ($post->pinned)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-400/90 text-white text-[10px] lato-bold shadow">
                                        <x-lucide-pin class="w-2.5 h-2.5" /> Fixado
                                    </span>
                                @endif
                                @if ($post->is_mandatory_read)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-500/90 text-white text-[10px] lato-bold shadow">
                                        <x-lucide-book-open-check class="w-2.5 h-2.5" /> Obrigatório
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Corpo --}}
                    <div class="flex flex-col flex-1 p-4 gap-3">

                        {{-- Badges tipo + status --}}
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $tc }}">
                                <x-dynamic-component :component="'lucide-'.$ti" class="w-2.5 h-2.5" /> {{ $post->type_label }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $sc }}">{{ $post->status_label }}</span>
                            @if ($post->target_audience !== 'todos')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] lato-bold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">
                                    <x-lucide-users class="w-2.5 h-2.5" /> {{ $post->audience_label }}
                                </span>
                            @endif
                        </div>

                        {{-- Tags --}}
                        @if ($post->tags->isNotEmpty())
                            <div class="flex items-center gap-1 flex-wrap">
                                @foreach ($post->tags->take(3) as $tag)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] lato-bold {{ $tag->badge_class }}">
                                        <x-lucide-hash class="w-2 h-2" />{{ $tag->name }}
                                    </span>
                                @endforeach
                                @if ($post->tags->count() > 3)
                                    <span class="text-[10px] text-slate-400 lato-regular">+{{ $post->tags->count()-3 }}</span>
                                @endif
                            </div>
                        @endif

                        {{-- Título --}}
                        <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 leading-snug line-clamp-2 cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                            wire:click="openPreview({{ $post->id }})">
                            {{ $post->title }}
                        </h3>

                        {{-- Trecho --}}
                        <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular line-clamp-2 flex-1">{{ $post->excerpt }}</p>

                        {{-- Rodapé com métricas --}}
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[9px] lato-bold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($post->author?->name ?? '?', 0, 1)) }}
                                </span>
                                <div>
                                    <p class="text-[10px] lato-bold text-slate-600 dark:text-slate-400">{{ Str::limit($post->author?->name ?? '—', 16) }}</p>
                                    <p class="text-[10px] text-slate-400 lato-regular">{{ $post->created_at->format('d/m/Y') }} · {{ $post->reading_time_minutes }} min</p>
                                </div>
                            </div>
                            <button wire:click="openReadersReport({{ $post->id }})" type="button"
                                    class="flex items-center gap-1 text-slate-400 hover:text-indigo-500 transition cursor-pointer group/reads">
                                <x-lucide-eye class="w-3 h-3" />
                                <span class="text-[10px] lato-regular group-hover/reads:lato-bold">{{ $readsCount }}</span>
                            </button>
                        </div>

                        {{-- Agendado --}}
                        @if ($post->status === 'rascunho' && $post->published_at)
                            <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-900/30">
                                <x-lucide-clock class="w-3 h-3 text-indigo-400 shrink-0" />
                                <span class="text-[10px] text-indigo-600 dark:text-indigo-400 lato-bold">
                                    Agendado: {{ $post->published_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        @endif

                        {{-- Reações --}}
                        @php
                            $userReaction  = $post->reactions->first()?->type;
                            $cardReactions = [
                                'like'  => ['thumbs-up', $post->like_count  ?? 0, 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800',   'hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:text-blue-500 hover:border-blue-200'],
                                'heart' => ['heart',     $post->heart_count ?? 0, 'bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400 border-red-200 dark:border-red-800',         'hover:bg-red-50 dark:hover:bg-red-900/10 hover:text-red-400 hover:border-red-200'],
                                'clap'  => ['zap',       $post->clap_count  ?? 0, 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800', 'hover:bg-amber-50 dark:hover:bg-amber-900/10 hover:text-amber-500 hover:border-amber-200'],
                            ];
                        @endphp
                        <div class="flex items-center gap-1 pt-1 border-t border-slate-50 dark:border-slate-700/50">
                            @foreach ($cardReactions as $rType => [$icon, $rCount, $activeClass, $hoverClass])
                                <button wire:click="toggleReaction({{ $post->id }}, '{{ $rType }}')" type="button"
                                        class="flex items-center gap-1 px-1.5 py-0.5 rounded-lg text-[10px] transition cursor-pointer border
                                        {{ $userReaction === $rType ? $activeClass . ' lato-bold' : 'border-transparent text-slate-400 dark:text-slate-500 ' . $hoverClass }}">
                                    <x-dynamic-component :component="'lucide-' . $icon" class="w-3 h-3" />
                                    @if($rCount > 0)<span>{{ $rCount }}</span>@endif
                                </button>
                            @endforeach
                            @if (($post->comments_count ?? 0) > 0)
                                <span class="ml-auto flex items-center gap-1 text-[10px] text-slate-400 lato-regular">
                                    <x-lucide-message-circle class="w-3 h-3" /> {{ $post->comments_count }}
                                </span>
                            @endif
                        </div>

                        {{-- Ações --}}
                        <div class="flex items-center gap-1 flex-wrap">
                            @if ($post->status === 'rascunho' || $post->status === 'arquivado')
                                <button wire:click="publish({{ $post->id }})" type="button"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] lato-bold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition cursor-pointer">
                                    <x-lucide-globe class="w-3 h-3" /> Publicar
                                </button>
                            @else
                                <button wire:click="unpublish({{ $post->id }})" type="button"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] lato-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition cursor-pointer">
                                    <x-lucide-eye-off class="w-3 h-3" /> Despublicar
                                </button>
                            @endif

                            @if ($post->status !== 'arquivado')
                                <button wire:click="archive({{ $post->id }})" type="button"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] lato-bold bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 hover:bg-orange-100 dark:hover:bg-orange-900/40 transition cursor-pointer">
                                    <x-lucide-archive class="w-3 h-3" /> Arquivar
                                </button>
                            @endif

                            <button wire:click="togglePin({{ $post->id }})" type="button"
                                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] lato-bold transition cursor-pointer
                                    {{ $post->pinned ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:bg-amber-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-200' }}">
                                <x-lucide-pin class="w-3 h-3" /> {{ $post->pinned ? 'Fixado' : 'Fixar' }}
                            </button>

                            <button wire:click="duplicate({{ $post->id }})" type="button"
                                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] lato-bold bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400 hover:bg-teal-100 dark:hover:bg-teal-900/40 transition cursor-pointer">
                                <x-lucide-copy class="w-3 h-3" /> Duplicar
                            </button>

                            <button wire:click="openEdit({{ $post->id }})" type="button"
                                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] lato-bold bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition cursor-pointer">
                                <x-lucide-pencil class="w-3 h-3" /> Editar
                            </button>

                            <button wire:click="confirmDelete({{ $post->id }})" type="button"
                                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] lato-bold bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition cursor-pointer ml-auto">
                                <x-lucide-trash-2 class="w-3 h-3" />
                            </button>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginação --}}
        @if ($posts->hasPages())
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-2">
                {{ $posts->links('partials.pagination') }}
            </div>
        @endif
    @endif


    {{-- ════════════════════════════════════════════════════════════════
         DRAWER — CRIAR / EDITAR
    ════════════════════════════════════════════════════════════════ --}}
    @if ($formOpen)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40" wire:click="closeForm"></div>
        <div class="fixed inset-y-0 right-0 z-50 w-full max-w-2xl flex flex-col bg-white dark:bg-slate-800 border-l border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 dark:border-slate-700 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        @if ($formMode === 'create') <x-lucide-plus class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                        @else <x-lucide-pencil class="w-4 h-4 text-indigo-600 dark:text-indigo-400" /> @endif
                    </div>
                    <div>
                        <h2 class="text-sm lato-black text-slate-800 dark:text-white">{{ $formMode === 'create' ? 'Nova publicação' : 'Editar publicação' }}</h2>
                        <p class="text-xs text-slate-400 lato-regular">Preencha os campos e escolha publicar ou salvar como rascunho</p>
                    </div>
                </div>
                <button wire:click="closeForm" type="button" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            {{-- Form --}}
            <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

                {{-- Título --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Título <span class="text-red-400">*</span></label>
                    <input wire:model="title" type="text" placeholder="Ex: Novo benefício disponível para todos os colaboradores"
                           class="w-full px-4 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                  bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition placeholder-slate-300" />
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Tipo + Público --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Tipo</label>
                        <div class="grid grid-cols-2 gap-1.5">
                            @foreach (['noticia'=>['Notícia','newspaper','blue'],'comunicado'=>['Comunicado','megaphone','violet'],'evento'=>['Evento','calendar-heart','emerald'],'aviso'=>['Aviso','triangle-alert','amber']] as $val=>[$label,$icon,$c])
                                <button type="button" wire:click="$set('type','{{ $val }}')"
                                        class="flex flex-col items-center gap-1 p-2.5 rounded-xl border text-xs lato-bold transition cursor-pointer
                                        {{ $type===$val ? 'border-'.$c.'-400 bg-'.$c.'-50 dark:bg-'.$c.'-900/20 text-'.$c.'-700 dark:text-'.$c.'-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300' }}">
                                    <x-dynamic-component :component="'lucide-'.$icon" class="w-4 h-4" />
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                        @error('type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Público-alvo</label>
                        <div class="space-y-1.5">
                            @foreach (['todos'=>'Todos','gerentes'=>'Gerentes','funcionarios'=>'Funcionários','rh'=>'RH / DP'] as $val=>$label)
                                <button type="button" wire:click="$set('target_audience','{{ $val }}')"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-xs lato-bold transition cursor-pointer
                                        {{ $target_audience===$val ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300' }}">
                                    {{ $label }}
                                    @if($target_audience===$val) <x-lucide-check class="w-3.5 h-3.5 text-indigo-500" /> @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Editor rich text (Quill) --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Conteúdo <span class="text-red-400">*</span></label>
                    <div wire:ignore
                         x-data="quillEditor()"
                         x-init="init()">
                        <div x-ref="quillDiv"></div>
                    </div>
                    <p id="quill-reading-time" class="mt-1 text-[10px] text-slate-400 lato-regular">
                        @if($content) @php $w = str_word_count(strip_tags($content)); @endphp {{ $w }} palavras · aprox. {{ max(1, ceil($w/200)) }} min de leitura @endif
                    </p>
                    @error('content') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Tags --}}
                @if ($this->availableTags->isNotEmpty())
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs lato-bold text-slate-600 dark:text-slate-400">Tags</label>
                            <button type="button" wire:click="openTagManager"
                                    class="text-[10px] text-indigo-500 hover:text-indigo-700 lato-bold transition cursor-pointer">
                                + Gerenciar tags
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($this->availableTags as $tag)
                                <button type="button" wire:click="toggleSelectedTag('{{ $tag->id }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs lato-bold transition cursor-pointer border
                                        {{ in_array((string)$tag->id, $selectedTags)
                                            ? $tag->badge_class . ' border-transparent ring-2 ring-offset-1 ring-' . $tag->color . '-400'
                                            : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 bg-white dark:bg-slate-900' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $tag->dot_class }} shrink-0"></span>
                                    {{ $tag->name }}
                                    @if(in_array((string)$tag->id, $selectedTags))
                                        <x-lucide-check class="w-3 h-3 ml-0.5" />
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                        <span class="text-xs text-slate-400 lato-regular">Nenhuma tag criada ainda.</span>
                        <button type="button" wire:click="openTagManager" class="text-xs text-indigo-500 hover:text-indigo-700 lato-bold transition cursor-pointer">+ Criar tags</button>
                    </div>
                @endif

                {{-- Imagem de capa --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Imagem de capa</label>
                    @if ($coverImage)
                        <div class="relative rounded-xl overflow-hidden h-32 mb-2">
                            <img src="{{ $coverImage->temporaryUrl() }}" class="w-full h-full object-cover" />
                            <button type="button" wire:click="$set('coverImage', null)"
                                    class="absolute top-2 right-2 p-1 rounded-full bg-black/50 text-white hover:bg-black/70 transition">
                                <x-lucide-x class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    @elseif ($existingCover)
                        <div class="relative rounded-xl overflow-hidden h-32 mb-2">
                            <img src="{{ Storage::url($existingCover) }}" class="w-full h-full object-cover" />
                            <button type="button" wire:click="$set('existingCover', null)"
                                    class="absolute top-2 right-2 p-1 rounded-full bg-black/50 text-white hover:bg-black/70 transition">
                                <x-lucide-x class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    @else
                        <label class="flex flex-col items-center justify-center h-24 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition">
                            <x-lucide-image-plus class="w-6 h-6 text-slate-300 dark:text-slate-600 mb-1" />
                            <span class="text-xs text-slate-400 lato-regular">Clique para enviar (PNG, JPG, max 4 MB)</span>
                            <input type="file" class="hidden" wire:model="coverImage" accept="image/*" />
                        </label>
                    @endif
                    @error('coverImage') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Agendamento --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Data de publicação</label>
                        <input wire:model="published_at" type="datetime-local"
                               class="w-full px-3 py-2.5 text-xs lato-regular border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                        <p class="mt-1 text-[10px] text-slate-400 lato-regular">Deixe em branco para publicar agora</p>
                        @error('published_at') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Expiração (opcional)</label>
                        <input wire:model="expires_at" type="datetime-local"
                               class="w-full px-3 py-2.5 text-xs lato-regular border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                        @error('expires_at') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Toggles: Fixar + Leitura obrigatória --}}
                <div class="space-y-2.5">
                    {{-- Fixar --}}
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/30">
                        <button type="button" wire:click="$set('pinned', {{ $pinned ? 'false' : 'true' }})"
                                class="w-9 h-5 rounded-full transition-colors duration-200 relative shrink-0 cursor-pointer {{ $pinned ? 'bg-amber-400' : 'bg-slate-300 dark:bg-slate-600' }}">
                            <span class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-all duration-200 {{ $pinned ? 'left-[18px]' : 'left-0.5' }}"></span>
                        </button>
                        <div>
                            <p class="text-xs lato-bold text-amber-700 dark:text-amber-400">Fixar publicação</p>
                            <p class="text-[10px] text-amber-600/70 dark:text-amber-500/70 lato-regular">Aparece no topo do feed</p>
                        </div>
                    </div>
                    {{-- Leitura obrigatória --}}
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/30">
                        <button type="button" wire:click="$set('is_mandatory_read', {{ $is_mandatory_read ? 'false' : 'true' }})"
                                class="w-9 h-5 rounded-full transition-colors duration-200 relative shrink-0 cursor-pointer {{ $is_mandatory_read ? 'bg-red-500' : 'bg-slate-300 dark:bg-slate-600' }}">
                            <span class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-all duration-200 {{ $is_mandatory_read ? 'left-[18px]' : 'left-0.5' }}"></span>
                        </button>
                        <div>
                            <p class="text-xs lato-bold text-red-700 dark:text-red-400">Leitura obrigatória</p>
                            <p class="text-[10px] text-red-600/70 dark:text-red-500/70 lato-regular">Usuários precisam confirmar que leram</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="shrink-0 px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex items-center gap-2 flex-wrap justify-end">
                @if ($lastAutosavedAt)
                    <span class="text-[10px] text-slate-400 lato-regular flex items-center gap-1 mr-auto">
                        <x-lucide-check class="w-3 h-3 text-emerald-400" /> Rascunho salvo às {{ $lastAutosavedAt }}
                    </span>
                @endif
                <button wire:click="closeForm" type="button"
                        class="px-4 py-2.5 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer">
                    Cancelar
                </button>
                <button wire:click="save('rascunho')" type="button"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm lato-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 transition cursor-pointer">
                    <x-lucide-file-pen-line class="w-4 h-4" /> Salvar rascunho
                </button>
                <button wire:click="save('publicado')" type="button"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm lato-bold bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white transition cursor-pointer shadow-sm">
                    <x-lucide-globe class="w-4 h-4" /> Publicar agora
                </button>
            </div>
        </div>
    @endif


    {{-- ════════════════════════════════════════════════════════════════
         MODAL — PRÉ-VISUALIZAÇÃO
    ════════════════════════════════════════════════════════════════ --}}
    @if ($previewOpen && $this->previewPost)
        @php $pv = $this->previewPost; @endphp
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">

                <div class="flex items-start justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs lato-bold {{ $pv->type_color }}">
                            <x-dynamic-component :component="'lucide-'.$pv->type_icon" class="w-3 h-3" /> {{ $pv->type_label }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs lato-bold {{ $pv->status_color }}">{{ $pv->status_label }}</span>
                        @if($pv->pinned) <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs lato-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"><x-lucide-pin class="w-3 h-3" /> Fixado</span> @endif
                        @if($pv->is_mandatory_read) <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs lato-bold bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400"><x-lucide-book-open-check class="w-3 h-3" /> Leit. obrigatória</span> @endif
                    </div>
                    <button wire:click="closePreview" type="button" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 transition cursor-pointer shrink-0 ml-2">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto">
                    @if($pv->cover_image)
                        <div class="relative w-full overflow-hidden bg-slate-100 dark:bg-slate-900" style="aspect-ratio:16/7">
                            <img src="{{ Storage::url($pv->cover_image) }}"
                                 class="w-full h-full object-cover object-top" />
                        </div>
                    @endif
                    <div class="px-6 py-5 space-y-4">
                        <h2 class="text-lg lato-black text-slate-800 dark:text-white leading-snug">{{ $pv->title }}</h2>
                        <div class="flex items-center gap-3 text-xs text-slate-400 lato-regular flex-wrap">
                            <span class="flex items-center gap-1"><x-lucide-user class="w-3 h-3" /> {{ $pv->author?->name ?? '—' }}</span>
                            <span>·</span>
                            <span class="flex items-center gap-1"><x-lucide-calendar class="w-3 h-3" /> {{ $pv->created_at->format('d/m/Y \à\s H:i') }}</span>
                            <span>·</span>
                            <span class="flex items-center gap-1"><x-lucide-eye class="w-3 h-3" /> {{ $pv->reads->count() }} leitores únicos</span>
                            <span>·</span>
                            <span class="flex items-center gap-1"><x-lucide-clock class="w-3 h-3" /> {{ $pv->reading_time_minutes }} min de leitura</span>
                        </div>
                        @if($pv->tags->isNotEmpty())
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach($pv->tags as $t)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $t->badge_class }}"><x-lucide-hash class="w-2 h-2" />{{ $t->name }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if($pv->target_audience !== 'todos')
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 text-xs lato-bold">
                                <x-lucide-users class="w-3.5 h-3.5" /> Para: {{ $pv->audience_label }}
                            </div>
                        @endif
                        <div class="prose prose-sm dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 lato-regular leading-relaxed">
                            {!! $pv->content !!}
                        </div>

                        @if($pv->published_at || $pv->expires_at)
                            <div class="flex items-center gap-4 pt-2 border-t border-slate-100 dark:border-slate-700 text-xs text-slate-400 lato-regular">
                                @if($pv->published_at) <span>Publicado em {{ $pv->published_at->format('d/m/Y H:i') }}</span> @endif
                                @if($pv->expires_at) <span>· Expira em {{ $pv->expires_at->format('d/m/Y H:i') }}</span> @endif
                            </div>
                        @endif

                        {{-- Reações --}}
                        @php
                            $myReaction = $pv->reactions->where('user_id', Auth::id())->first()?->type;
                            $pvReactions = [
                                'like'  => ['thumbs-up', 'Curtiu',   $pv->reactions->where('type','like')->count(),  'border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300',     'border-slate-200 dark:border-slate-700 hover:border-blue-200 hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:text-blue-500 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800'],
                                'heart' => ['heart',     'Amei',     $pv->reactions->where('type','heart')->count(), 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-300',           'border-slate-200 dark:border-slate-700 hover:border-red-200 hover:bg-red-50 dark:hover:bg-red-900/10 hover:text-red-400 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800'],
                                'clap'  => ['zap',       'Incrível', $pv->reactions->where('type','clap')->count(),  'border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300', 'border-slate-200 dark:border-slate-700 hover:border-amber-200 hover:bg-amber-50 dark:hover:bg-amber-900/10 hover:text-amber-500 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800'],
                            ];
                        @endphp
                        <div class="flex items-center gap-2 py-3 border-t border-slate-100 dark:border-slate-700 flex-wrap">
                            <span class="text-xs text-slate-400 lato-regular">Reagir:</span>
                            @foreach ($pvReactions as $rType => [$icon, $label, $rCount, $activeClass, $inactiveClass])
                                <button wire:click="toggleReaction({{ $pv->id }}, '{{ $rType }}')" type="button"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs transition cursor-pointer border
                                        {{ $myReaction === $rType ? $activeClass . ' lato-bold' : $inactiveClass }}">
                                    <x-dynamic-component :component="'lucide-' . $icon" class="w-3.5 h-3.5" />
                                    <span>{{ $label }}</span>
                                    @if($rCount > 0)
                                        <span class="px-1.5 py-0.5 rounded-full text-[10px] lato-bold
                                            {{ $myReaction === $rType ? 'bg-white/60 dark:bg-black/20' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                                            {{ $rCount }}
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>

                        {{-- Comentários --}}
                        <div class="border-t border-slate-100 dark:border-slate-700 pt-4 space-y-3">
                            <p class="text-xs lato-bold text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                                <x-lucide-message-circle class="w-3.5 h-3.5" />
                                Comentários ({{ $pv->comments->count() }})
                            </p>

                            @forelse ($pv->comments->sortByDesc('created_at') as $comment)
                                @php
                                    $cParts = explode(' ', trim($comment->user?->name ?? '?'));
                                    $cInit  = strtoupper(substr($cParts[0], 0, 1) . (isset($cParts[1]) ? substr($cParts[1], 0, 1) : ''));
                                    $cPal   = ['bg-indigo-400', 'bg-violet-400', 'bg-pink-400', 'bg-teal-500', 'bg-amber-500'];
                                    $cBg    = $cPal[abs(crc32($comment->user?->name ?? '')) % count($cPal)];
                                @endphp
                                <div class="flex gap-2.5">
                                    <span class="w-7 h-7 rounded-full {{ $cBg }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0 mt-0.5">{{ $cInit }}</span>
                                    <div class="flex-1 bg-slate-50 dark:bg-slate-900/50 rounded-xl px-3 py-2">
                                        <div class="flex items-center justify-between mb-1">
                                            <p class="text-[10px] lato-bold text-slate-700 dark:text-slate-200">{{ $comment->user?->name ?? '—' }}</p>
                                            <div class="flex items-center gap-2">
                                                <p class="text-[10px] text-slate-400 lato-regular">{{ $comment->created_at->diffForHumans() }}</p>
                                                <button wire:click="deleteComment({{ $comment->id }})" type="button"
                                                        class="text-slate-300 hover:text-red-400 dark:text-slate-600 dark:hover:text-red-400 transition cursor-pointer">
                                                    <x-lucide-trash-2 class="w-3 h-3" />
                                                </button>
                                            </div>
                                        </div>
                                        <p class="text-xs text-slate-600 dark:text-slate-300 lato-regular leading-relaxed">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 lato-regular text-center py-2">Nenhum comentário ainda. Seja o primeiro!</p>
                            @endforelse

                            {{-- Input novo comentário --}}
                            <div class="flex gap-2.5 pt-1">
                                @php
                                    $meParts = explode(' ', trim(Auth::user()->name ?? '?'));
                                    $meInit  = strtoupper(substr($meParts[0], 0, 1) . (isset($meParts[1]) ? substr($meParts[1], 0, 1) : ''));
                                @endphp
                                <span class="w-7 h-7 rounded-full bg-indigo-500 text-white text-[10px] lato-bold flex items-center justify-center shrink-0">{{ $meInit }}</span>
                                <div class="flex-1 flex gap-2">
                                    <input wire:model="newComment" type="text" placeholder="Adicione um comentário..."
                                           class="flex-1 px-3 py-2 text-xs lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                                  bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                                  focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition"
                                           wire:keydown.enter="addComment({{ $pv->id }})" />
                                    <button wire:click="addComment({{ $pv->id }})" type="button"
                                            class="px-3 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white transition cursor-pointer shrink-0">
                                        <x-lucide-send class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                            @error('newComment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="shrink-0 px-6 py-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-2 flex-wrap">
                    <button wire:click="openReadersReport({{ $pv->id }}); closePreview()" type="button"
                            class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm lato-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-users class="w-3.5 h-3.5" /> Ver leitores
                    </button>
                    <div class="flex items-center gap-2 flex-wrap">
                        @php $myRead = $pv->reads->where('user_id', Auth::id())->first(); @endphp
                        @if ($pv->is_mandatory_read && (! $myRead || ! $myRead->confirmed_at))
                            <button wire:click="confirmRead({{ $pv->id }})" type="button"
                                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm lato-bold bg-emerald-500 hover:bg-emerald-600 text-white transition cursor-pointer">
                                <x-lucide-check-circle class="w-4 h-4" /> Confirmar leitura
                            </button>
                        @elseif ($pv->is_mandatory_read && $myRead?->confirmed_at)
                            <span class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 lato-bold px-2">
                                <x-lucide-check-circle class="w-4 h-4" /> Leitura confirmada
                            </span>
                        @endif
                        <button wire:click="openEdit({{ $pv->id }}); closePreview()" type="button"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm lato-bold bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition cursor-pointer">
                            <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                        </button>
                        <button wire:click="closePreview" type="button"
                                class="px-4 py-2 rounded-xl text-sm lato-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition cursor-pointer">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @include('livewire.pages.publicacoes.modals._modal-leitores')
    @include('livewire.pages.publicacoes.modals._modal-tag')
    @include('livewire.pages.publicacoes.modals._modal-excluir')

</div>

</div>{{-- fim do único elemento raiz --}}
