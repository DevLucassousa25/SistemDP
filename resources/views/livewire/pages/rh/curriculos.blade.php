<div class="p-4 sm:p-6 lg:p-8 space-y-6">

    {{-- ── Cabeçalho ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lato-black tracking-tight text-slate-800 dark:text-white">
                Currículos
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular mt-1">
                Gerencie o banco de currículos e candidatos
            </p>
        </div>
        <button wire:click="$set('uploadModal', true)" type="button"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm shrink-0">
            <x-lucide-upload class="w-4 h-4" />
            Novo Currículo
        </button>
    </div>

    {{-- ── Cards de resumo ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $statsCards = [
                ['label' => 'Total',           'value' => $this->curriculos->total(), 'icon' => 'file-user',    'color' => 'text-indigo-500'],
                ['label' => 'Ativos',          'value' => $this->curriculos->where('status','ativo')->count(),   'icon' => 'circle-check-big','color' => 'text-green-500'],
                ['label' => 'Favoritos',       'value' => $this->curriculos->where('status','favorito')->count(),'icon' => 'star',            'color' => 'text-amber-500'],
                ['label' => 'Banco Talentos',  'value' => $this->curriculos->where('status','banco_talentos')->count(),'icon' => 'database','color' => 'text-blue-500'],
            ];
        @endphp
        @foreach ($statsCards as $card)
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                <x-dynamic-component :component="'lucide-'.$card['icon']" class="w-5 h-5 {{ $card['color'] }}" />
            </div>
            <div>
                <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $card['value'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $card['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Filtros ─────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Search --}}
            <div class="relative sm:col-span-2 lg:col-span-1">
                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <input type="text" wire:model.live.debounce.400ms="search" placeholder="Buscar por nome, email..."
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
            </div>

            {{-- Status --}}
            <select wire:model.live="filterStatus"
                    class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular">
                <option value="">Todos os status</option>
                <option value="ativo">Ativo</option>
                <option value="favorito">Favorito</option>
                <option value="banco_talentos">Banco de Talentos</option>
                <option value="inativo">Inativo</option>
            </select>

            {{-- Escolaridade --}}
            <select wire:model.live="filterEscolaridade"
                    class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular">
                <option value="">Escolaridade</option>
                <option value="fundamental">Fundamental</option>
                <option value="medio">Médio</option>
                <option value="tecnico">Técnico</option>
                <option value="graduacao">Graduação</option>
                <option value="pos_graduacao">Pós-Graduação</option>
                <option value="mestrado">Mestrado</option>
                <option value="doutorado">Doutorado</option>
            </select>

            {{-- Área --}}
            <div class="relative">
                <x-lucide-briefcase class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <input type="text" wire:model.live.debounce.400ms="filterArea" placeholder="Filtrar por área..."
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
            </div>

            {{-- Estado --}}
            <div class="relative">
                <x-lucide-map-pin class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <input type="text" wire:model.live.debounce.400ms="filterEstado" placeholder="Estado (ex: SP, RJ)..."
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
            </div>

            {{-- Faixa salarial --}}
            <div class="flex items-center gap-2">
                <input type="number" wire:model.live.debounce.400ms="filterSalMin" placeholder="Sal. mín"
                       class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                <span class="text-slate-400 text-xs shrink-0">até</span>
                <input type="number" wire:model.live.debounce.400ms="filterSalMax" placeholder="Sal. máx"
                       class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
            </div>
        </div>
    </div>

    {{-- ── Grid de Currículos ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse ($this->curriculos as $curr)
        @php
            $initials  = collect(explode(' ', $curr->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
            $colors    = ['bg-indigo-500','bg-indigo-600','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500','bg-rose-500','bg-amber-500'];
            $avatarBg  = $colors[crc32($curr->nome) % count($colors)];
            $escLabels = ['fundamental'=>'Fundamental','medio'=>'Médio','tecnico'=>'Técnico','graduacao'=>'Graduação','pos_graduacao'=>'Pós-Graduação','mestrado'=>'Mestrado','doutorado'=>'Doutorado'];
            $statusMap = [
                'ativo'          => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'Ativo'],
                'favorito'       => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', 'Favorito'],
                'banco_talentos' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'Banco Talentos'],
                'inativo'        => ['bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400', 'Inativo'],
            ];
            [$statusClass, $statusLabel] = $statusMap[$curr->status] ?? $statusMap['inativo'];
        @endphp
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 hover:shadow-md transition flex flex-col gap-3"
             x-data="{ menu: false }">

            {{-- Topo: avatar + info + status --}}
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl {{ $avatarBg }} flex items-center justify-center text-white lato-black text-sm shrink-0">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $curr->nome }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">{{ $curr->area_interesse ?? '—' }}</p>
                    @if ($curr->cidade || $curr->estado)
                    <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular mt-0.5">
                        <x-lucide-map-pin class="w-3 h-3 inline -mt-0.5" />
                        {{ trim(($curr->cidade ?? '') . ', ' . ($curr->estado ?? ''), ', ') }}
                    </p>
                    @endif
                </div>
                <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] lato-bold {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>

            {{-- Tags --}}
            @if ($curr->tags && $curr->tags->count())
            <div class="flex flex-wrap gap-1.5">
                @foreach ($curr->tags->take(3) as $tag)
                <span class="px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 text-[11px] lato-bold">
                    {{ $tag->nome }}
                </span>
                @endforeach
                @if ($curr->tags->count() > 3)
                <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[11px] lato-regular">
                    +{{ $curr->tags->count() - 3 }}
                </span>
                @endif
            </div>
            @endif

            {{-- Escolaridade + Pretensão --}}
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 lato-regular">
                <span class="flex items-center gap-1">
                    <x-lucide-graduation-cap class="w-3.5 h-3.5" />
                    {{ $escLabels[$curr->escolaridade] ?? ucfirst($curr->escolaridade ?? '—') }}
                </span>
                @if ($curr->pretensao_salarial)
                <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 lato-bold">
                    <x-lucide-banknote class="w-3.5 h-3.5" />
                    R$ {{ number_format($curr->pretensao_salarial, 0, ',', '.') }}
                </span>
                @endif
            </div>

            {{-- Experiência mais recente --}}
            @if ($curr->experiencias && $curr->experiencias->count())
            @php $exp = $curr->experiencias->sortByDesc('data_inicio')->first(); @endphp
            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">
                <x-lucide-building-2 class="w-3 h-3 inline -mt-0.5 mr-0.5" />
                {{ $exp->cargo ?? '' }}
                @if ($exp->empresa) · {{ $exp->empresa }} @endif
            </p>
            @endif

            {{-- Botões --}}
            <div class="flex items-center gap-2 pt-1 border-t border-slate-100 dark:border-slate-700 mt-auto">
                <button wire:click="openDrawer({{ $curr->id }})" type="button"
                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                    <x-lucide-eye class="w-3.5 h-3.5" /> Ver
                </button>
                <button wire:click="openEdit({{ $curr->id }})" type="button"
                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                    <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                </button>
                <button wire:click="toggleFavorito({{ $curr->id }})" type="button"
                        class="p-2 rounded-xl {{ $curr->status === 'favorito' ? 'bg-amber-100 text-amber-500 dark:bg-amber-900/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500' }} hover:bg-amber-100 dark:hover:bg-amber-900/30 hover:text-amber-500 transition">
                    <x-lucide-star class="w-3.5 h-3.5" />
                </button>
                <div class="relative" @click.outside="menu = false">
                    <button @click="menu = !menu" type="button"
                            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                        <x-lucide-ellipsis-vertical class="w-3.5 h-3.5" />
                    </button>
                    <div x-show="menu" x-transition
                         class="absolute right-0 bottom-full mb-2 w-44 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg z-20 py-1">
                        <button wire:click="moverBancoTalentos({{ $curr->id }})" @click="menu=false" type="button"
                                class="w-full flex items-center gap-2 px-3 py-2 text-xs lato-regular text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                            <x-lucide-database class="w-3.5 h-3.5 text-blue-500" /> Banco de Talentos
                        </button>
                        <button wire:click="confirmDelete({{ $curr->id }})" @click="menu=false" type="button"
                                class="w-full flex items-center gap-2 px-3 py-2 text-xs lato-regular text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition disabled:opacity-60"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="confirmDelete" class="flex items-center gap-1.5">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" /> Excluir
                            </span>
                            <span wire:loading wire:target="confirmDelete" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                <x-lucide-file-search class="w-8 h-8 text-slate-400" />
            </div>
            <p class="text-slate-500 dark:text-slate-400 lato-bold">Nenhum currículo encontrado</p>
            <p class="text-slate-400 dark:text-slate-500 text-sm lato-regular mt-1">Tente ajustar os filtros ou faça upload de um novo currículo.</p>
        </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    @if ($this->curriculos->hasPages())
    <div class="mt-4">
        {{ $this->curriculos->links() }}
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- DRAWER LATERAL: detalhe do currículo                              --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if ($drawerOpen && $this->drawerCurriculo)
    @php
        $dc = $this->drawerCurriculo;
        $escLabels = ['fundamental'=>'Fundamental','medio'=>'Médio','tecnico'=>'Técnico','graduacao'=>'Graduação','pos_graduacao'=>'Pós-Graduação','mestrado'=>'Mestrado','doutorado'=>'Doutorado'];
        $statusMap = [
            'ativo'          => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'Ativo'],
            'favorito'       => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', 'Favorito'],
            'banco_talentos' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'Banco Talentos'],
            'inativo'        => ['bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400', 'Inativo'],
        ];
        [$dcStatusClass, $dcStatusLabel] = $statusMap[$dc->status] ?? $statusMap['inativo'];
        $initials = collect(explode(' ', $dc->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
        $colors   = ['bg-indigo-500','bg-indigo-600','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500','bg-rose-500','bg-amber-500'];
        $avatarBg = $colors[crc32($dc->nome) % count($colors)];
    @endphp
    <div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="closeDrawer"></div>
    <div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] lg:w-[560px] bg-white dark:bg-slate-800 shadow-2xl overflow-y-auto"
         x-data="{ tab: 'resumo' }">

        {{-- Header drawer --}}
        <div class="sticky top-0 z-10 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl {{ $avatarBg }} flex items-center justify-center text-white lato-black text-base shrink-0">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base lato-black text-slate-800 dark:text-white">{{ $dc->nome }}</h2>
                        <span class="px-2 py-0.5 rounded-full text-[11px] lato-bold {{ $dcStatusClass }}">{{ $dcStatusLabel }}</span>
                    </div>
                    <div class="flex flex-wrap gap-3 mt-1 text-xs text-slate-500 dark:text-slate-400 lato-regular">
                        @if ($dc->email) <span><x-lucide-mail class="w-3 h-3 inline" /> {{ $dc->email }}</span> @endif
                        @if ($dc->telefone) <span><x-lucide-phone class="w-3 h-3 inline" /> {{ $dc->telefone }}</span> @endif
                        @if ($dc->cidade || $dc->estado) <span><x-lucide-map-pin class="w-3 h-3 inline" /> {{ trim(($dc->cidade ?? '') . ', ' . ($dc->estado ?? ''), ', ') }}</span> @endif
                    </div>
                </div>
                <button wire:click="closeDrawer" type="button"
                        class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            {{-- Ações rápidas --}}
            <div class="flex items-center gap-2 mt-3">
                <button wire:click="openEdit({{ $dc->id }})" type="button"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                    <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                </button>
                <button wire:click="toggleFavorito({{ $dc->id }})" type="button"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl {{ $dc->status === 'favorito' ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }} text-xs lato-bold transition">
                    <x-lucide-star class="w-3.5 h-3.5" />
                    {{ $dc->status === 'favorito' ? 'Favoritado' : 'Favoritar' }}
                </button>
                @if ($dc->arquivo_path)
                <a href="{{ Storage::url($dc->arquivo_path) }}" target="_blank"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs lato-bold hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                    <x-lucide-download class="w-3.5 h-3.5" /> Baixar arquivo
                </a>
                @endif
            </div>
        </div>

        {{-- Conteúdo --}}
        <div class="px-6 py-4 space-y-6">

            {{-- Resumo profissional --}}
            @if ($dc->resumo)
            <div>
                <h3 class="text-xs lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Resumo Profissional</h3>
                <p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed">{{ $dc->resumo }}</p>
            </div>
            @endif

            {{-- Experiências --}}
            @if ($dc->experiencias && $dc->experiencias->count())
            <div>
                <h3 class="text-xs lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Experiências</h3>
                <div class="relative pl-5 border-l-2 border-slate-200 dark:border-slate-700 space-y-4">
                    @foreach ($dc->experiencias->sortByDesc('data_inicio') as $exp)
                    <div class="relative">
                        <div class="absolute -left-[22px] top-1 w-3.5 h-3.5 rounded-full bg-blue-500 border-2 border-white dark:border-slate-800"></div>
                        <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $exp->cargo }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $exp->empresa }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular mt-0.5">
                            {{ \Carbon\Carbon::parse($exp->data_inicio)->format('M/Y') }}
                            — {{ $exp->data_fim ? \Carbon\Carbon::parse($exp->data_fim)->format('M/Y') : 'Atual' }}
                        </p>
                        @if ($exp->descricao)
                        <p class="text-xs text-slate-600 dark:text-slate-400 lato-regular mt-1 leading-relaxed">{{ $exp->descricao }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Formações --}}
            @if ($dc->formacoes && $dc->formacoes->count())
            <div>
                <h3 class="text-xs lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Formação Acadêmica</h3>
                <div class="space-y-3">
                    @foreach ($dc->formacoes->sortByDesc('data_conclusao') as $form)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                            <x-lucide-graduation-cap class="w-4 h-4 text-indigo-500" />
                        </div>
                        <div>
                            <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $form->curso }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $form->instituicao }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular">
                                {{ $escLabels[$form->nivel] ?? ucfirst($form->nivel ?? '') }}
                                @if ($form->data_conclusao) · {{ \Carbon\Carbon::parse($form->data_conclusao)->format('Y') }} @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Habilidades --}}
            @if ($dc->habilidades && $dc->habilidades->count())
            <div>
                <h3 class="text-xs lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Habilidades</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($dc->habilidades as $hab)
                    @php
                        $nivelColor = match($hab->nivel ?? '') {
                            'basico'      => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                            'intermediario'=> 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'avancado'    => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
                            'especialista'=> 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
                            default       => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs lato-bold {{ $nivelColor }}">
                        {{ $hab->nome }}
                        @if ($hab->nivel) <span class="opacity-60 font-normal">({{ ucfirst($hab->nivel) }})</span> @endif
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Tags --}}
            <div>
                <h3 class="text-xs lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Tags</h3>
                <div class="flex flex-wrap gap-2 mb-3">
                    @forelse ($dc->tags ?? [] as $tag)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 text-xs lato-bold">
                        {{ $tag->nome }}
                        <button wire:click="removeTag({{ $tag->id }})" type="button"
                                class="ml-0.5 text-indigo-400 hover:text-red-500 transition">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                    @empty
                    <p class="text-xs text-slate-400 lato-regular">Nenhuma tag adicionada.</p>
                    @endforelse
                </div>
                <div class="flex gap-2">
                    <input type="text" wire:model="newTag" placeholder="Nova tag..."
                           wire:keydown.enter="addTag({{ $dc->id }})"
                           class="flex-1 px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                    <button wire:click="addTag({{ $dc->id }})" type="button"
                            class="px-3 py-2 rounded-xl bg-indigo-500 text-white text-sm lato-bold hover:bg-indigo-600 transition">
                        <x-lucide-plus class="w-4 h-4" />
                    </button>
                </div>
            </div>

            {{-- Candidaturas --}}
            @if ($dc->candidaturas && $dc->candidaturas->count())
            <div>
                <h3 class="text-xs lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Candidaturas</h3>
                <div class="space-y-2">
                    @foreach ($dc->candidaturas as $cand)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                        <div>
                            <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $cand->vaga->titulo ?? '—' }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $cand->etapa->nome ?? '—' }}</p>
                        </div>
                        <span class="text-xs text-slate-400 lato-regular">{{ $cand->created_at?->format('d/m/Y') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Testes realizados --}}
            @if ($dc->tentativasTestes && $dc->tentativasTestes->count())
            <div>
                <h3 class="text-xs lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Testes Realizados</h3>
                <div class="space-y-2">
                    @foreach ($dc->tentativasTestes as $tent)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                        <div>
                            <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $tent->teste->titulo ?? '—' }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $tent->created_at?->format('d/m/Y') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($tent->nota_final !== null)
                            <span class="text-sm lato-black {{ $tent->aprovado ? 'text-green-500' : 'text-red-500' }}">
                                {{ number_format($tent->nota_final, 1) }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[11px] lato-bold {{ $tent->aprovado ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ $tent->aprovado ? 'Aprovado' : 'Reprovado' }}
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[11px] lato-bold">Aguardando</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Notas internas --}}
            <div>
                <h3 class="text-xs lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Notas Internas</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 lato-regular leading-relaxed whitespace-pre-line">
                    {{ $dc->notas ?? 'Nenhuma nota registrada.' }}
                </p>
                <button wire:click="openEdit({{ $dc->id }})" type="button"
                        class="mt-2 flex items-center gap-1.5 text-xs text-blue-500 hover:text-blue-600 lato-bold transition">
                    <x-lucide-pencil class="w-3 h-3" /> Editar notas
                </button>
            </div>

        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Upload de Currículo                                         --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if ($uploadModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         wire:click.self="$set('uploadModal', false)">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-md p-6"
             x-data="{ dragging: false }">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base lato-black text-slate-800 dark:text-white">Enviar Currículo</h2>
                <button wire:click="$set('uploadModal', false)" type="button"
                        class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            {{-- Dropzone --}}
            <label for="currUpload"
                   class="block w-full cursor-pointer"
                   @dragover.prevent="dragging = true"
                   @dragleave.prevent="dragging = false"
                   @drop.prevent="dragging = false">
                <div :class="dragging ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900'"
                     class="border-2 border-dashed rounded-2xl p-10 flex flex-col items-center gap-3 transition">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <x-lucide-file-up class="w-7 h-7 text-blue-500" />
                    </div>
                    <div class="text-center">
                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">Arraste o arquivo ou clique para selecionar</p>
                        <p class="text-xs text-slate-400 lato-regular mt-1">PDF, DOC ou DOCX — máx. 10 MB</p>
                    </div>
                    <input id="currUpload" type="file" wire:model="arquivo" accept=".pdf,.doc,.docx" class="hidden" />
                </div>
            </label>

            @if ($arquivo)
            <div class="mt-3 flex items-center gap-2 p-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                <x-lucide-file-check class="w-4 h-4 text-green-500 shrink-0" />
                <span class="text-xs text-green-700 dark:text-green-300 lato-regular truncate">
                    {{ is_object($arquivo) ? $arquivo->getClientOriginalName() : 'Arquivo selecionado' }}
                </span>
            </div>
            @endif

            @error('arquivo')
            <p class="mt-2 text-xs text-red-500 lato-regular">{{ $message }}</p>
            @enderror

            <div class="flex justify-end gap-3 mt-5">
                <button wire:click="$set('uploadModal', false)" type="button"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button wire:click="uploadCurriculo" type="button"
                        wire:loading.attr="disabled"
                        wire:target="uploadCurriculo"
                        class="relative inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60">
                    <span wire:loading.remove wire:target="uploadCurriculo">
                        <x-lucide-sparkles class="w-4 h-4 inline" /> Enviar e Analisar
                    </span>
                    <span wire:loading wire:target="uploadCurriculo" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Analisando...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Edição completa                                             --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if ($editModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         wire:click.self="$set('editModal', false)">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between z-10">
                <h2 class="text-base lato-black text-slate-800 dark:text-white">
                    {{ $editId ? 'Editar Currículo' : 'Novo Currículo' }}
                </h2>
                <button wire:click="$set('editModal', false)" type="button"
                        class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Nome --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Nome completo</label>
                        <input type="text" wire:model="editNome" placeholder="Nome do candidato"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        @error('editNome') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Email --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Email</label>
                        <input type="email" wire:model="editEmail" placeholder="email@exemplo.com"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        @error('editEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Telefone --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Telefone</label>
                        <input type="text" wire:model="editTelefone" placeholder="(00) 90000-0000"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        @error('editTelefone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Cidade --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Cidade</label>
                        <input type="text" wire:model="editCidade" placeholder="São Paulo"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        @error('editCidade') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Estado --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Estado</label>
                        <input type="text" wire:model="editEstado" placeholder="SP" maxlength="2"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        @error('editEstado') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Área --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Área de interesse</label>
                        <input type="text" wire:model="editArea" placeholder="Tecnologia, Marketing..."
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        @error('editArea') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Escolaridade --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Escolaridade</label>
                        <select wire:model="editEscolaridade"
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular">
                            <option value="">Selecione</option>
                            <option value="fundamental">Fundamental</option>
                            <option value="medio">Médio</option>
                            <option value="tecnico">Técnico</option>
                            <option value="graduacao">Graduação</option>
                            <option value="pos_graduacao">Pós-Graduação</option>
                            <option value="mestrado">Mestrado</option>
                            <option value="doutorado">Doutorado</option>
                        </select>
                        @error('editEscolaridade') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Pretensão --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Pretensão salarial (R$)</label>
                        <input type="number" wire:model="editPretensao" placeholder="5000"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        @error('editPretensao') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Status --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                        <select wire:model="editStatus"
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular">
                            <option value="ativo">Ativo</option>
                            <option value="favorito">Favorito</option>
                            <option value="banco_talentos">Banco de Talentos</option>
                            <option value="inativo">Inativo</option>
                        </select>
                        @error('editStatus') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Resumo --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Resumo profissional</label>
                        <textarea wire:model="editResumo" rows="3" placeholder="Descreva brevemente o perfil profissional..."
                                  class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                        @error('editResumo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    {{-- Notas internas --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Notas internas</label>
                        <textarea wire:model="editNotas" rows="3" placeholder="Observações internas sobre o candidato..."
                                  class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                        @error('editNotas') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="sticky bottom-0 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 px-6 py-4 flex justify-end gap-3">
                <button wire:click="$set('editModal', false)" type="button"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button wire:click="saveEdit" type="button"
                        wire:loading.attr="disabled" wire:target="saveEdit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveEdit"><x-lucide-save class="w-4 h-4 inline" /> Salvar</span>
                    <span wire:loading wire:target="saveEdit">Salvando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Exclusão                                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if ($deleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         wire:click.self="$set('deleteModal', false)">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-sm p-6">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <x-lucide-trash-2 class="w-7 h-7 text-red-500" />
                </div>
                <div>
                    <h3 class="text-base lato-black text-slate-800 dark:text-white">Excluir currículo?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-1">Esta ação não pode ser desfeita. Todos os dados do candidato serão removidos.</p>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button wire:click="$set('deleteModal', false)" type="button"
                        class="flex-1 px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button wire:click="deleteCurriculo" type="button"
                        wire:loading.attr="disabled" wire:target="deleteCurriculo"
                        class="flex-1 px-5 py-2.5 rounded-xl bg-red-500 text-white text-sm lato-bold hover:bg-red-600 transition disabled:opacity-60">
                    <span wire:loading.remove wire:target="deleteCurriculo">Confirmar exclusão</span>
                    <span wire:loading wire:target="deleteCurriculo">Excluindo...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
