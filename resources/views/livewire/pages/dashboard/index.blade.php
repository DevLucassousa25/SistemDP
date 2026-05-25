@php
    $user         = auth()->user();
    $isRh         = $user->isRhOuDp();
    $isGer        = $user->isGerente();
    $isEmp        = $user->isEmployee();
    $primeiroNome = explode(' ', $user->name)[0];
    $hora         = now()->hour;
    $saudacao     = match(true) {
        $hora >= 5  && $hora < 12 => 'Bom dia',
        $hora >= 12 && $hora < 18 => 'Boa tarde',
        default                    => 'Boa noite',
    };
@endphp

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 p-4 sm:p-6 lg:p-8 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl lato-bold text-slate-800 dark:text-slate-100">
                {{ $saudacao }}, {{ $primeiroNome }}! 👋
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-0.5">
                @if($isRh)
                    Aqui está o panorama geral da empresa hoje.
                @elseif($isGer)
                    Acompanhe o desempenho e as atividades do seu time.
                @else
                    Veja suas tarefas, reuniões e atualizações do dia.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 lato-regular bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5">
            <x-lucide-calendar class="w-4 h-4 text-blue-500" />
            {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         VISÃO: FUNCIONÁRIO
    ══════════════════════════════════════════════════════ --}}
    @if($isEmp)

        @php
            $se = $this->statsEmployee;
            $empCards = [
                ['label' => 'Tarefas pendentes', 'value' => $se['tarefas_pendentes'],    'icon' => 'clock',          'color' => 'from-orange-500 to-amber-500',  'href' => route('tarefas')],
                ['label' => 'Em progresso',       'value' => $se['tarefas_em_progresso'], 'icon' => 'loader',         'color' => 'from-blue-500 to-indigo-600',   'href' => route('tarefas')],
                ['label' => 'Concluídas',         'value' => $se['tarefas_concluidas'],   'icon' => 'check-circle-2', 'color' => 'from-green-500 to-emerald-600', 'href' => route('tarefas')],
                ['label' => 'Reuniões hoje',      'value' => $se['reunioes_hoje'],        'icon' => 'video',          'color' => 'from-violet-500 to-purple-600', 'href' => route('reunioes')],
            ];
        @endphp

        {{-- Cards resumo --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($empCards as $card)
                <a href="{{ $card['href'] }}" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $card['color'] }} flex items-center justify-center shadow-sm">
                            <x-dynamic-component :component="'lucide-'.$card['icon']" class="w-4 h-4 text-white" />
                        </div>
                        <x-lucide-arrow-right class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-blue-500 transition" />
                    </div>
                    <p class="text-2xl lato-bold text-slate-800 dark:text-slate-100">{{ $card['value'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-0.5">{{ $card['label'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Minhas Tarefas --}}
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <x-lucide-check-square class="w-4 h-4 text-blue-500" /> Minhas tarefas
                    </h2>
                    <a href="{{ route('tarefas') }}" class="text-xs text-blue-500 hover:underline lato-regular">Ver todas</a>
                </div>
                @forelse($this->minhasTarefas as $tarefa)
                    @php
                        $pColor = match($tarefa->priority ?? '') {
                            'urgente' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            'alta'    => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                            'media'   => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                            default   => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',
                        };
                        $atrasada = $tarefa->due_date && $tarefa->due_date->isPast() && $tarefa->status !== 'concluida';
                    @endphp
                    <div class="flex items-center gap-3 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0">
                        <div class="w-2 h-2 rounded-full shrink-0 {{ $tarefa->status === 'em_progresso' ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 truncate">{{ $tarefa->title }}</p>
                            @if($tarefa->due_date)
                                <p class="text-xs {{ $atrasada ? 'text-red-500' : 'text-slate-400 dark:text-slate-500' }} lato-regular mt-0.5">
                                    {{ $atrasada ? '⚠ Atrasada · ' : '' }}Entrega: {{ $tarefa->due_date->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($tarefa->priority)
                                <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full {{ $pColor }}">{{ ucfirst($tarefa->priority) }}</span>
                            @endif
                            <span class="text-[10px] lato-regular px-2 py-0.5 rounded-full {{ $tarefa->status === 'em_progresso' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400' }}">
                                {{ $tarefa->status === 'em_progresso' ? 'Em progresso' : 'Pendente' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-slate-600">
                        <x-lucide-check-circle-2 class="w-10 h-10 mb-2 text-green-400" />
                        <p class="text-sm lato-regular">Nenhuma tarefa pendente. Tudo em dia!</p>
                    </div>
                @endforelse
            </div>

            {{-- Coluna direita --}}
            <div class="space-y-6">

                {{-- Próximas Reuniões --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                            <x-lucide-calendar class="w-4 h-4 text-violet-500" /> Próximas reuniões
                        </h2>
                        <a href="{{ route('reunioes') }}" class="text-xs text-blue-500 hover:underline lato-regular">Ver todas</a>
                    </div>
                    @forelse($this->minhasReunioes as $reuniao)
                        <div class="flex items-start gap-3 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0">
                            <div class="w-9 h-9 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex flex-col items-center justify-center shrink-0">
                                <span class="text-[10px] lato-bold text-violet-600 dark:text-violet-400 leading-none">{{ $reuniao->start_time->format('d') }}</span>
                                <span class="text-[8px] text-violet-500 uppercase">{{ $reuniao->start_time->format('M') }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 truncate">{{ $reuniao->title }}</p>
                                <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ $reuniao->start_time->format('H:i') }} · {{ ucfirst($reuniao->location_type) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 lato-regular text-center py-4">Nenhuma reunião agendada.</p>
                    @endforelse
                </div>

                {{-- Pesquisas Pendentes --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                            <x-lucide-clipboard-list class="w-4 h-4 text-teal-500" /> Pesquisas pendentes
                        </h2>
                        <a href="{{ route('pesquisas') }}" class="text-xs text-blue-500 hover:underline lato-regular">Ver todas</a>
                    </div>
                    @forelse($this->pesquisasPendentes as $pesquisa)
                        <a href="{{ route('pesquisas.responder', $pesquisa->id) }}" class="flex items-center gap-3 py-2.5 border-b border-slate-100 dark:border-slate-700 last:border-0 group">
                            <div class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center shrink-0">
                                <x-lucide-file-text class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" />
                            </div>
                            <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 truncate group-hover:text-blue-500 transition">{{ $pesquisa->title }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-400 lato-regular text-center py-4">Nenhuma pesquisa pendente.</p>
                    @endforelse
                </div>

            </div>
        </div>

        {{-- OKRs Pessoais --}}
        @if($this->meusOkrs->isNotEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                    <x-lucide-target class="w-4 h-4 text-rose-500" /> Meus OKRs
                </h2>
                <a href="{{ route('okrs') }}" class="text-xs text-blue-500 hover:underline lato-regular">Ver todos</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach($this->meusOkrs as $okr)
                    @php $progress = (int)($okr->progress ?? 0); @endphp
                    <div class="p-4 border border-slate-100 dark:border-slate-700 rounded-xl">
                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-3 line-clamp-2">{{ $okr->title }}</p>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-rose-400 to-pink-500 transition-all" style="width: {{ $progress }}%"></div>
                            </div>
                            <span class="text-xs lato-bold text-slate-600 dark:text-slate-300 shrink-0">{{ $progress }}%</span>
                        </div>
                        <p class="text-[10px] text-slate-400 lato-regular mt-2">{{ $okr->keyResults->count() }} key result(s)</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    @endif {{-- /isEmp --}}


    {{-- ══════════════════════════════════════════════════════
         VISÃO: GERENTE
    ══════════════════════════════════════════════════════ --}}
    @if($isGer)

        @php
            $sg = $this->statsGerente;
            $gerCards = [
                ['label' => 'Funcionários no time',  'value' => $sg['funcionarios'],           'icon' => 'users',          'color' => 'from-blue-500 to-indigo-600',   'href' => route('time')],
                ['label' => 'Tarefas do time',       'value' => $sg['tarefas_time_total'],      'icon' => 'list-checks',    'color' => 'from-violet-500 to-purple-600', 'href' => route('tarefas')],
                ['label' => 'Tarefas atrasadas',     'value' => $sg['tarefas_time_atrasadas'],  'icon' => 'alert-triangle', 'color' => 'from-orange-500 to-red-500',    'href' => route('tarefas')],
                ['label' => 'Feedbacks em aberto',   'value' => $sg['feedbacks_abertos'],       'icon' => 'message-circle', 'color' => 'from-teal-500 to-cyan-600',    'href' => route('feedback')],
                ['label' => 'Reuniões esta semana',  'value' => $sg['reunioes_semana'],          'icon' => 'calendar',       'color' => 'from-pink-500 to-rose-600',    'href' => route('reunioes')],
            ];
        @endphp

        {{-- Cards resumo --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($gerCards as $card)
                <a href="{{ $card['href'] }}" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $card['color'] }} flex items-center justify-center shadow-sm">
                            <x-dynamic-component :component="'lucide-'.$card['icon']" class="w-4 h-4 text-white" />
                        </div>
                        <x-lucide-arrow-right class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-blue-500 transition" />
                    </div>
                    <p class="text-2xl lato-bold text-slate-800 dark:text-slate-100">{{ $card['value'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-0.5">{{ $card['label'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Tarefas do Time --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <x-lucide-list-checks class="w-4 h-4 text-blue-500" /> Tarefas do time
                    </h2>
                    <a href="{{ route('tarefas') }}" class="text-xs text-blue-500 hover:underline lato-regular">Ver todas</a>
                </div>
                @forelse($this->tarefasTime as $tarefa)
                    @php $atrasada = $tarefa->due_date && $tarefa->due_date->isPast() && $tarefa->status !== 'concluida'; @endphp
                    <div class="flex items-center gap-3 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0">
                        @if($tarefa->assignedTo?->avatar)
                            <img src="{{ $tarefa->assignedTo->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover shrink-0" />
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $tarefa->assignedTo?->avatarColor() ?? 'from-slate-400 to-slate-600' }} flex items-center justify-center shrink-0">
                                <span class="text-[11px] lato-bold text-white select-none">{{ $tarefa->assignedTo?->initials() ?? '?' }}</span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 truncate">{{ $tarefa->title }}</p>
                            <p class="text-xs text-slate-400 lato-regular mt-0.5">
                                {{ $tarefa->assignedTo?->name }}
                                @if($tarefa->due_date)
                                    · <span class="{{ $atrasada ? 'text-red-500' : '' }}">{{ $atrasada ? '⚠ ' : '' }}{{ $tarefa->due_date->format('d/m') }}</span>
                                @endif
                            </p>
                        </div>
                        <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full shrink-0 {{ $tarefa->status === 'em_progresso' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400' }}">
                            {{ $tarefa->status === 'em_progresso' ? 'Em progresso' : 'Pendente' }}
                        </span>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-slate-600">
                        <x-lucide-check-circle-2 class="w-10 h-10 mb-2 text-green-400" />
                        <p class="text-sm lato-regular">Nenhuma tarefa pendente no time.</p>
                    </div>
                @endforelse
            </div>

            {{-- OKRs do Departamento --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <x-lucide-target class="w-4 h-4 text-rose-500" /> OKRs do departamento
                    </h2>
                    <a href="{{ route('okrs') }}" class="text-xs text-blue-500 hover:underline lato-regular">Ver todos</a>
                </div>
                @forelse($this->okrsDepartamento as $okr)
                    @php $progress = (int)($okr->progress ?? 0); @endphp
                    <div class="py-3 border-b border-slate-100 dark:border-slate-700 last:border-0">
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 truncate flex-1 mr-2">{{ $okr->title }}</p>
                            <span class="text-xs lato-bold text-slate-600 dark:text-slate-300 shrink-0">{{ $progress }}%</span>
                        </div>
                        <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all {{ $progress >= 70 ? 'bg-gradient-to-r from-green-400 to-emerald-500' : ($progress >= 40 ? 'bg-gradient-to-r from-yellow-400 to-amber-500' : 'bg-gradient-to-r from-red-400 to-rose-500') }}"
                                 style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 lato-regular text-center py-6">Nenhum OKR ativo no departamento.</p>
                @endforelse
            </div>

        </div>

        {{-- Feedbacks pendentes --}}
        @if($this->feedbacksPendentesGerente->isNotEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                    <x-lucide-message-circle class="w-4 h-4 text-teal-500" /> Feedbacks em aberto
                </h2>
                <a href="{{ route('feedback') }}" class="text-xs text-blue-500 hover:underline lato-regular">Ver todos</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($this->feedbacksPendentesGerente as $fb)
                    <div class="p-3 border border-slate-100 dark:border-slate-700 rounded-xl flex items-center gap-3">
                        @if($fb->employee?->avatar)
                            <img src="{{ $fb->employee->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover shrink-0" />
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $fb->employee?->avatarColor() ?? 'from-slate-400 to-slate-600' }} flex items-center justify-center shrink-0">
                                <span class="text-[11px] lato-bold text-white select-none">{{ $fb->employee?->initials() ?? '?' }}</span>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $fb->employee?->name }}</p>
                            <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ $fb->created_at?->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    @endif {{-- /isGer --}}


    {{-- ══════════════════════════════════════════════════════
         VISÃO: DP / RH
    ══════════════════════════════════════════════════════ --}}
    @if($isRh)

        @php
            $sr = $this->statsRh;
            $rhCards = [
                ['label' => 'Funcionários ativos',    'value' => $sr['total_funcionarios'],    'icon' => 'users',          'color' => 'from-blue-500 to-indigo-600',    'href' => route('users')],
                ['label' => 'Gerentes ativos',        'value' => $sr['total_gerentes'],         'icon' => 'shield-check',   'color' => 'from-violet-500 to-purple-600',  'href' => route('users')],
                ['label' => 'Vagas abertas',          'value' => $sr['vagas_abertas'],          'icon' => 'briefcase',      'color' => 'from-teal-500 to-cyan-600',      'href' => route('rh.curriculos', ['aba' => 'vagas'])],
                ['label' => 'Pesquisas ativas',       'value' => $sr['pesquisas_ativas'],       'icon' => 'clipboard-list', 'color' => 'from-orange-500 to-amber-500',   'href' => route('pesquisas')],
                ['label' => 'Manifestações abertas',  'value' => $sr['manifestacoes_abertas'],  'icon' => 'alert-circle',   'color' => 'from-rose-500 to-pink-600',      'href' => route('ouvidoria')],
                ['label' => 'Reuniões hoje',          'value' => $sr['reunioes_hoje'],          'icon' => 'video',          'color' => 'from-green-500 to-emerald-600',  'href' => route('reunioes')],
            ];
        @endphp

        {{-- Cards resumo --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4">
            @foreach($rhCards as $card)
                <a href="{{ $card['href'] }}" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $card['color'] }} flex items-center justify-center shadow-sm">
                            <x-dynamic-component :component="'lucide-'.$card['icon']" class="w-4 h-4 text-white" />
                        </div>
                        <x-lucide-arrow-right class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-blue-500 transition" />
                    </div>
                    <p class="text-2xl lato-bold text-slate-800 dark:text-slate-100">{{ $card['value'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-0.5">{{ $card['label'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Vagas abertas --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <x-lucide-briefcase class="w-4 h-4 text-teal-500" /> Vagas em aberto
                    </h2>
                    <a href="{{ route('rh.curriculos', ['aba' => 'vagas']) }}" class="text-xs text-blue-500 hover:underline lato-regular">Ver todas</a>
                </div>
                @forelse($this->vagasAbertasRh as $vaga)
                    <div class="flex items-center gap-3 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0">
                        <div class="w-9 h-9 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center shrink-0">
                            <x-lucide-briefcase class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $vaga->titulo ?? $vaga->title ?? 'Vaga' }}</p>
                            <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ $vaga->modalidade ?? '' }}{{ ($vaga->modalidade && $vaga->cidade) ? ' · ' : '' }}{{ $vaga->cidade ?? '' }}</p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <x-lucide-users class="w-3.5 h-3.5 text-slate-400" />
                            <span class="text-xs lato-bold text-slate-600 dark:text-slate-300">{{ $vaga->candidaturas_count }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 lato-regular text-center py-6">Nenhuma vaga aberta no momento.</p>
                @endforelse
            </div>

            {{-- Pesquisas ativas --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <x-lucide-clipboard-list class="w-4 h-4 text-orange-500" /> Pesquisas ativas
                    </h2>
                    <a href="{{ route('pesquisas') }}" class="text-xs text-blue-500 hover:underline lato-regular">Ver todas</a>
                </div>
                @forelse($this->pesquisasAtivasRh as $pesquisa)
                    <a href="{{ route('pesquisas.resultados', $pesquisa->id) }}" class="flex items-center gap-3 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0 group">
                        <div class="w-9 h-9 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center shrink-0">
                            <x-lucide-file-bar-chart class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 truncate group-hover:text-blue-500 transition">{{ $pesquisa->title }}</p>
                            <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ $pesquisa->responses_count }} resposta(s)</p>
                        </div>
                        <x-lucide-arrow-right class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-blue-500 transition shrink-0" />
                    </a>
                @empty
                    <p class="text-sm text-slate-400 lato-regular text-center py-6">Nenhuma pesquisa ativa.</p>
                @endforelse
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Manifestações pendentes --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <x-lucide-alert-circle class="w-4 h-4 text-rose-500" /> Manifestações abertas
                    </h2>
                    <a href="{{ route('ouvidoria') }}" class="text-xs text-blue-500 hover:underline lato-regular">Ver todas</a>
                </div>
                @forelse($this->manifestacoesPendentes as $manifestacao)
                    @php
                        $statusLabel = match($manifestacao->status) {
                            'em_analise'   => ['label' => 'Em análise',   'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'],
                            'em_andamento' => ['label' => 'Em andamento', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'],
                            default        => ['label' => ucfirst($manifestacao->status), 'class' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400'],
                        };
                    @endphp
                    <a href="{{ route('ouvidoria.details', $manifestacao->id) }}" class="flex items-center gap-3 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0 group">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center shrink-0">
                            <x-lucide-alert-circle class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 truncate group-hover:text-blue-500 transition">
                                {{ $manifestacao->assunto ?? 'Manifestação #'.$manifestacao->id }}
                            </p>
                            <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ $manifestacao->created_at?->format('d/m/Y') }}</p>
                        </div>
                        <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full shrink-0 {{ $statusLabel['class'] }}">{{ $statusLabel['label'] }}</span>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-8 text-slate-400 dark:text-slate-600">
                        <x-lucide-check-circle-2 class="w-8 h-8 mb-2 text-green-400" />
                        <p class="text-sm lato-regular">Nenhuma manifestação em aberto.</p>
                    </div>
                @endforelse
            </div>

            {{-- Últimos cadastros --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                        <x-lucide-user-plus class="w-4 h-4 text-indigo-500" /> Últimos cadastros
                    </h2>
                    <a href="{{ route('users') }}" class="text-xs text-blue-500 hover:underline lato-regular">Ver todos</a>
                </div>
                @foreach($this->ultimosUsuarios as $usuario)
                    <div class="flex items-center gap-3 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0">
                        @if($usuario->avatar)
                            <img src="{{ $usuario->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover shrink-0" />
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $usuario->avatarColor() }} flex items-center justify-center shrink-0">
                                <span class="text-[11px] lato-bold text-white select-none">{{ $usuario->initials() }}</span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $usuario->name }}</p>
                            <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ $usuario->department?->name ?? 'Sem departamento' }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1 shrink-0">
                            <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                                {{ $usuario->accessProfile?->name ?? '–' }}
                            </span>
                            <span class="text-[10px] text-slate-400 lato-regular">{{ $usuario->created_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

    @endif {{-- /isRh --}}

</div>
