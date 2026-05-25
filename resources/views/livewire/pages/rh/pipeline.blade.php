<div class="p-4 sm:p-6 lg:p-8 space-y-6" x-data="{ drawerTab: 'perfil' }">

    {{-- ── Cabeçalho + seletor de vaga ───────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lato-black tracking-tight text-slate-800 dark:text-white">
                Pipeline de Seleção
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular mt-1">
                Gerencie candidatos por etapa do processo seletivo
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0 flex-wrap">
            {{-- Select de vaga --}}
            <div class="relative">
                <x-lucide-briefcase class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <select wire:model.live="vagaId"
                        class="appearance-none pl-9 pr-8 py-2.5 text-sm lato-bold rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400/40 cursor-pointer min-w-[200px]">
                    <option value="">Selecionar vaga...</option>
                    @foreach ($this->vagas as $vaga)
                    <option value="{{ $vaga->id }}">{{ $vaga->titulo }}</option>
                    @endforeach
                </select>
                <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            </div>
            {{-- Busca de candidato --}}
            <div class="relative">
                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar candidato..."
                       class="pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular w-48" />
            </div>
        </div>
    </div>

    @if ($vagaId && $this->vaga)
    @php $vaga = $this->vaga; @endphp

    {{-- ── Info bar da vaga ───────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-wrap items-center gap-4 justify-between">
        <div class="flex items-center gap-4 flex-wrap">
            <div>
                <p class="text-xs text-slate-400 lato-regular">Vaga selecionada</p>
                <p class="text-sm lato-black text-slate-800 dark:text-white">{{ $vaga->titulo }}</p>
            </div>
            @if ($vaga->departamento)
            <div class="hidden sm:block w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
            <div>
                <p class="text-xs text-slate-400 lato-regular">Departamento</p>
                <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $vaga->departamento }}</p>
            </div>
            @endif
            <div class="hidden sm:block w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
            <div>
                <p class="text-xs text-slate-400 lato-regular">Candidatos</p>
                <p class="text-sm lato-black text-slate-800 dark:text-white">
                    {{ collect($this->kanban)->sum(fn($e) => count($e['candidaturas'])) }}
                </p>
            </div>
        </div>
        <button wire:click="openVincular" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm shrink-0">
            <x-lucide-user-plus class="w-4 h-4" /> Vincular Candidato
        </button>
    </div>

    {{-- ── Kanban board ────────────────────────────────────────────────── --}}
    @if (count($this->kanban) > 0)
    <div class="overflow-x-auto pb-4">
        <div class="flex gap-4 min-w-max">
            @foreach ($this->kanban as $coluna)
            @php
                $colCount = count($coluna['candidaturas']);
                $colColor = $coluna['etapa']['cor'] ?? '#6366f1';
            @endphp
            <div class="w-72 shrink-0 flex flex-col gap-3">
                {{-- Header da coluna --}}
                <div class="flex items-center justify-between px-3 py-2.5 rounded-xl"
                     style="background-color: {{ $colColor }}18; border: 1px solid {{ $colColor }}30;">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $colColor }};"></div>
                        <span class="text-sm lato-black text-slate-800 dark:text-white">{{ $coluna['etapa']['nome'] }}</span>
                    </div>
                    <span class="min-w-[22px] h-[22px] px-1.5 rounded-full text-[11px] lato-black text-white flex items-center justify-center"
                          style="background-color: {{ $colColor }};">
                        {{ $colCount }}
                    </span>
                </div>

                {{-- Cards de candidatos --}}
                <div class="space-y-2.5 min-h-[120px]">
                    @forelse ($coluna['candidaturas'] as $cand)
                    @php
                        $curr    = $cand['curriculo'] ?? null;
                        $initials = $curr ? collect(explode(' ', $curr['nome']))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('') : '??';
                        $colors   = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500'];
                        $avatarBg = $colors[crc32($curr['nome'] ?? 'x') % count($colors)];
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-3 hover:shadow-md transition cursor-pointer group"
                         x-data="{ moveMenu: false }"
                         wire:click="openDrawer({{ $cand['id'] }})">
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-lg {{ $avatarBg }} flex items-center justify-center text-white lato-black text-xs shrink-0">
                                {{ $initials }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $curr['nome'] ?? '—' }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">
                                    {{ $curr['area_interesse'] ?? '' }}
                                    @if ($curr['cidade'] ?? null) · {{ $curr['cidade'] }} @endif
                                </p>
                            </div>
                            {{-- Nota final --}}
                            @if (isset($cand['nota_final']) && $cand['nota_final'] !== null)
                            <span class="shrink-0 text-xs lato-black {{ $cand['aprovado'] ? 'text-green-500' : 'text-red-500' }}">
                                {{ number_format($cand['nota_final'], 1) }}
                            </span>
                            @endif
                        </div>

                        {{-- Tags --}}
                        @if (!empty($curr['tags']))
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach (array_slice($curr['tags'], 0, 2) as $tag)
                            <span class="px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 text-[10px] lato-bold">{{ $tag['nome'] }}</span>
                            @endforeach
                            @if (count($curr['tags']) > 2)
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 text-[10px]">+{{ count($curr['tags']) - 2 }}</span>
                            @endif
                        </div>
                        @endif

                        {{-- Mover para etapa --}}
                        <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-700" @click.stop>
                            <div class="relative" @click.outside="moveMenu = false">
                                <button @click="moveMenu = !moveMenu" type="button"
                                        class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs text-slate-500 dark:text-slate-400 lato-regular hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                    <span class="flex items-center gap-1"><x-lucide-move-right class="w-3 h-3" /> Mover para...</span>
                                    <x-lucide-chevron-down class="w-3 h-3" />
                                </button>
                                <div x-show="moveMenu" x-transition
                                     class="absolute bottom-full mb-1 left-0 right-0 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg z-20 py-1 max-h-48 overflow-y-auto">
                                    @foreach ($this->kanban as $etapaOpcao)
                                    @if ($etapaOpcao['etapa']['id'] !== $coluna['etapa']['id'])
                                    <button wire:click="moverCandidato({{ $cand['id'] }}, {{ $etapaOpcao['etapa']['id'] }})"
                                            @click="moveMenu = false"
                                            type="button"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs lato-regular text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                        <div class="w-2 h-2 rounded-full" style="background-color: {{ $etapaOpcao['etapa']['cor'] ?? '#94a3b8' }};"></div>
                                        {{ $etapaOpcao['etapa']['nome'] }}
                                    </button>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-8 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
                        <x-lucide-users class="w-6 h-6 text-slate-300 dark:text-slate-600 mb-1" />
                        <p class="text-xs text-slate-400 lato-regular">Sem candidatos</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-20 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
            <x-lucide-layout-dashboard class="w-8 h-8 text-slate-400" />
        </div>
        <p class="text-slate-500 dark:text-slate-400 lato-bold">Nenhuma etapa configurada</p>
        <p class="text-slate-400 dark:text-slate-500 text-sm lato-regular mt-1">Configure as etapas do processo seletivo para esta vaga.</p>
    </div>
    @endif

    @else
    {{-- Estado vazio: nenhuma vaga selecionada --}}
    <div class="flex flex-col items-center justify-center py-24 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
        <div class="w-20 h-20 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mb-5">
            <x-lucide-kanban class="w-10 h-10 text-indigo-400" />
        </div>
        <p class="text-slate-700 dark:text-slate-200 lato-black text-lg">Selecione uma vaga</p>
        <p class="text-slate-400 dark:text-slate-500 text-sm lato-regular mt-2 max-w-xs">
            Escolha uma vaga no seletor acima para visualizar o pipeline de candidatos.
        </p>
    </div>
    @endif

    {{-- ── MODAL CONFIRMAR CONTRATAÇÃO ─────────────────────────────── --}}
    @if ($contratarModal)
    <div class="fixed inset-0 z-[70] flex items-end sm:items-center justify-center sm:p-4"
         x-data x-init="document.body.style.overflow='hidden'" x-destroy="document.body.style.overflow=''">
        <div wire:click="$set('contratarModal', false)"
             class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
        <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-lg rounded-t-2xl sm:rounded-2xl shadow-2xl flex flex-col z-10 max-h-[95vh] sm:max-h-[90vh]">
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
            </div>
            <div class="flex items-start justify-between px-5 pt-4 pb-4 sm:pt-6 border-b border-slate-200 dark:border-slate-700 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-md shadow-green-500/20 shrink-0">
                        <x-lucide-user-check class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800 dark:text-white lato-bold">Confirmar Contratação</h2>
                        <p class="text-xs text-slate-400 lato-regular hidden sm:block">Revise os dados e confirme a criação do usuário</p>
                    </div>
                </div>
                <button wire:click="$set('contratarModal', false)"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer shrink-0 mt-0.5">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
            <div class="overflow-y-auto flex-1 px-5 py-5 space-y-4">
                <div class="flex items-start gap-2.5 p-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                    <x-lucide-info class="w-4 h-4 text-green-600 dark:text-green-400 shrink-0 mt-0.5" />
                    <p class="text-xs text-green-700 dark:text-green-300 lato-regular leading-relaxed">
                        Usuário criado com senha padrão
                        <span class="font-mono font-bold bg-green-100 dark:bg-green-900/40 px-1.5 py-0.5 rounded">lu753951</span>.
                        Onboarding com 8 tarefas será iniciado automaticamente.
                    </p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5 lato-bold">Nome <span class="text-red-400">*</span></label>
                        <input wire:model="onbNome" type="text"
                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent placeholder-slate-300 lato-regular @error('onbNome') border-red-400 @enderror" />
                        @error('onbNome') <p class="text-xs text-red-500 mt-1 lato-regular">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5 lato-bold">E-mail <span class="text-red-400">*</span></label>
                        <input wire:model="onbEmail" type="email"
                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent placeholder-slate-300 lato-regular @error('onbEmail') border-red-400 @enderror" />
                        @error('onbEmail') <p class="text-xs text-red-500 mt-1 lato-regular">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5 lato-bold">Cargo <span class="text-red-400">*</span></label>
                    <input wire:model="onbCargo" type="text"
                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent placeholder-slate-300 lato-regular @error('onbCargo') border-red-400 @enderror" />
                    @error('onbCargo') <p class="text-xs text-red-500 mt-1 lato-regular">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5 lato-bold">Departamento</label>
                        <select wire:model="onbDepartamento"
                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent lato-regular">
                            <option value="">— Selecionar —</option>
                            @foreach ($this->departamentosOnb as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5 lato-bold">Perfil de Acesso</label>
                        <select wire:model="onbPerfil"
                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent lato-regular">
                            <option value="">— Selecionar —</option>
                            @foreach ($this->perfisOnb as $perfil)
                                <option value="{{ $perfil->id }}">{{ $perfil->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5 lato-bold">Data de Início</label>
                        <input wire:model="onbDataInicio" type="date"
                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent lato-regular" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5 lato-bold">Observações</label>
                        <input wire:model="onbObservacoes" type="text" placeholder="Opcional..."
                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent placeholder-slate-300 lato-regular" />
                    </div>
                </div>
            </div>
            <div class="shrink-0 px-5 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-transparent rounded-b-2xl">
                <div class="flex gap-3 sm:justify-end">
                    <button wire:click="$set('contratarModal', false)"
                        class="flex-1 sm:flex-none px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:border-slate-300 rounded-xl transition bg-white dark:bg-slate-700 cursor-pointer lato-bold">
                        Cancelar
                    </button>
                    <button wire:click="confirmarContratacao" wire:loading.attr="disabled"
                        class="flex-1 sm:flex-none px-5 py-2.5 text-sm font-medium text-white cursor-pointer bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 shadow-md shadow-green-500/20 rounded-xl transition flex items-center justify-center gap-2 disabled:opacity-60 lato-bold">
                        <span wire:loading.remove wire:target="confirmarContratacao" class="flex items-center gap-1.5">
                            <x-lucide-user-check class="w-4 h-4" />
                            Confirmar e Criar Usuário
                        </span>
                        <span wire:loading wire:target="confirmarContratacao" class="flex items-center gap-2">
                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                            Criando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
