{{-- Drawer: detalhe da vaga --}}
@if ($vagaDrawer && $this->vagaDrawerData)
@php
    $dv  = $this->vagaDrawerData;
    $dvBg = portalVagaBg($dv->titulo);
    $dvIni = strtoupper(substr($dv->titulo, 0, 1));
@endphp
<div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="$set('vagaDrawer', false)"></div>
<div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] lg:w-[540px] bg-white dark:bg-slate-800 shadow-2xl flex flex-col">

    {{-- Header --}}
    <div class="shrink-0 border-b border-slate-200 dark:border-slate-700 px-6 py-4">
        <div class="flex items-start gap-3">
<div class="w-12 h-12 rounded-xl {{ $dvBg }} flex items-center justify-center text-white lato-black text-lg shrink-0">
    {{ $dvIni }}
</div>
<div class="flex-1 min-w-0">
    <h2 class="text-sm lato-black text-slate-800 dark:text-white leading-snug">{{ $dv->titulo }}</h2>
    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-0.5">{{ $dv->cargo }}</p>
    <div class="flex items-center gap-2 mt-2 flex-wrap">
        <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $vagaStatusCls[$dv->status] ?? 'bg-slate-100 text-slate-600' }}">
{{ $vagaStatusLbl[$dv->status] ?? $dv->status }}
        </span>
        <span class="px-2 py-0.5 rounded-full text-[10px] lato-regular bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
{{ $vagaModalLbl[$dv->modalidade] ?? $dv->modalidade }}
        </span>
        @if ($dv->cidade || $dv->estado)
        <span class="flex items-center gap-1 text-[10px] text-slate-500 lato-regular">
<x-lucide-map-pin class="w-3 h-3" />
{{ trim(($dv->cidade ?? '').'/'.($dv->estado ?? ''), '/') }}
        </span>
        @endif
    </div>
</div>
<button wire:click="$set('vagaDrawer', false)" type="button"
        class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
    <x-lucide-x class="w-5 h-5" />
</button>
        </div>
    </div>

    {{-- Body (scrollável) --}}
    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">

        {{-- Números rápidos --}}
        <div class="grid grid-cols-3 gap-3">
<div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3 text-center">
    <p class="text-xl lato-black text-indigo-600 dark:text-indigo-400">{{ $dv->candidaturas_count }}</p>
    <p class="text-[10px] text-slate-500 dark:text-slate-400 lato-regular mt-0.5">Candidatos</p>
</div>
<div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3 text-center">
    <p class="text-xl lato-black text-slate-800 dark:text-white">{{ $dv->vagas_disponiveis ?? '—' }}</p>
    <p class="text-[10px] text-slate-500 dark:text-slate-400 lato-regular mt-0.5">Vagas</p>
</div>
<div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3 text-center">
    <p class="text-xl lato-black text-slate-800 dark:text-white">{{ $dv->sla_dias ?? '—' }}</p>
    <p class="text-[10px] text-slate-500 dark:text-slate-400 lato-regular mt-0.5">SLA (dias)</p>
</div>
        </div>

        {{-- Remuneração --}}
        @if ($dv->salario_min || $dv->salario_max)
        <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800">
<x-lucide-dollar-sign class="w-4 h-4 text-emerald-500 shrink-0" />
<span class="text-sm lato-bold text-emerald-700 dark:text-emerald-400">
    @if ($dv->salario_min && $dv->salario_max)
        R$ {{ number_format($dv->salario_min, 0, ',', '.') }} – {{ number_format($dv->salario_max, 0, ',', '.') }}
    @elseif ($dv->salario_min)
        A partir de R$ {{ number_format($dv->salario_min, 0, ',', '.') }}
    @else
        Até R$ {{ number_format($dv->salario_max, 0, ',', '.') }}
    @endif
</span>
        </div>
        @endif

        {{-- Descrição --}}
        @if ($dv->descricao)
        <div>
<h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-2">Descrição</h4>
<p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed whitespace-pre-line">{{ $dv->descricao }}</p>
        </div>
        @endif

        {{-- Requisitos --}}
        @if ($dv->requisitos)
        <div>
<h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-2">Requisitos</h4>
<p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed whitespace-pre-line">{{ $dv->requisitos }}</p>
        </div>
        @endif

        {{-- Competências --}}
        @if ($dv->competencias)
        <div>
<h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-2">Competências desejáveis</h4>
<p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed whitespace-pre-line">{{ $dv->competencias }}</p>
        </div>
        @endif

        {{-- Benefícios --}}
        @if ($dv->beneficios)
        <div>
<h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-2">Benefícios</h4>
<p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed whitespace-pre-line">{{ $dv->beneficios }}</p>
        </div>
        @endif

        {{-- Etapas do processo --}}
        @if ($dv->etapas && $dv->etapas->count())
        <div>
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest">Etapas do processo</h4>
                <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                    {{ $dv->etapas->count() }} etapas
                </span>
            </div>
            @php $etapasOrdenadas = $dv->etapas->sortBy('ordem')->values(); @endphp
            <div class="space-y-2">
                @foreach ($etapasOrdenadas as $idx => $etapa)
                @php
                    $etapaColors = [
                        'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
                        'bg-blue-50 text-blue-500 dark:bg-blue-900/30 dark:text-blue-400',
                        'bg-indigo-50 text-indigo-500 dark:bg-indigo-900/30 dark:text-indigo-400',
                        'bg-violet-50 text-indigo-500 dark:bg-violet-900/30 dark:text-indigo-400',
                        'bg-purple-50 text-purple-500 dark:bg-purple-900/30 dark:text-purple-400',
                        'bg-amber-50 text-amber-500 dark:bg-amber-900/30 dark:text-amber-400',
                        'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400',
                        'bg-teal-50 text-teal-500 dark:bg-teal-900/30 dark:text-teal-400',
                    ];
                    $etapaDot = [
                        'bg-slate-400','bg-blue-500','bg-indigo-500','bg-indigo-600',
                        'bg-indigo-600','bg-amber-500','bg-green-500','bg-teal-500',
                    ];
                    $ci = $idx % count($etapaColors);
                @endphp
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-700">
                    <span class="w-6 h-6 rounded-lg {{ $etapaColors[$ci] }} flex items-center justify-center text-[10px] lato-black shrink-0">
                        {{ $idx + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs lato-bold text-slate-800 dark:text-slate-100 truncate">{{ $etapa->nome }}</p>
                        @if ($etapa->descricao)
                        <p class="text-[10px] text-slate-400 lato-regular truncate mt-0.5">{{ $etapa->descricao }}</p>
                        @endif
                    </div>
                    @if (!$loop->last)
                    <x-lucide-chevron-right class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 shrink-0" />
                    @else
                    <x-lucide-flag class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 shrink-0" />
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Candidatos recentes --}}
        @if ($dv->candidaturas && $dv->candidaturas->count())
        <div>
<h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3">
    Candidatos recentes
    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 normal-case text-[9px]">{{ $dv->candidaturas_count }}</span>
</h4>
<div class="space-y-2">
    @foreach ($dv->candidaturas->take(6) as $cand)
    @php $cCurr = $cand->curriculo; @endphp
    @if ($cCurr)
    @php
        $cIni = collect(explode(' ', $cCurr->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
        $candColors = ['bg-indigo-500','bg-indigo-600','bg-blue-500','bg-teal-500','bg-pink-500','bg-emerald-500'];
        $cBg = $candColors[abs(crc32($cCurr->nome)) % count($candColors)];
    @endphp
    <div class="flex items-center gap-3 py-2 px-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <div class="w-8 h-8 rounded-full {{ $cBg }} flex items-center justify-center text-white text-xs lato-bold shrink-0">{{ $cIni }}</div>
        <div class="flex-1 min-w-0">
<p class="text-xs lato-bold text-slate-800 dark:text-white truncate">{{ $cCurr->nome }}</p>
<p class="text-[10px] text-slate-500 lato-regular truncate">{{ $cand->etapa->nome ?? 'Sem etapa' }}</p>
        </div>
        @php
$stColors = ['em_analise'=>'bg-blue-100 text-blue-700','aprovado'=>'bg-green-100 text-green-700','reprovado'=>'bg-red-100 text-red-600','em_espera'=>'bg-amber-100 text-amber-700'];
$stLabels = ['em_analise'=>'Em análise','aprovado'=>'Aprovado','reprovado'=>'Reprovado','em_espera'=>'Em espera'];
        @endphp
        <span class="shrink-0 px-1.5 py-0.5 rounded-full text-[9px] lato-bold {{ $stColors[$cand->status] ?? 'bg-slate-100 text-slate-600' }}">
{{ $stLabels[$cand->status] ?? $cand->status }}
        </span>
    </div>
    @endif
    @endforeach
</div>
        </div>
        @endif

        {{-- Datas --}}
        <div class="grid grid-cols-2 gap-3 text-xs text-slate-500 dark:text-slate-400 lato-regular">
@if ($dv->data_encerramento)
<div class="flex items-center gap-1.5">
    <x-lucide-calendar class="w-3.5 h-3.5 text-slate-400" />
    Encerra: {{ \Carbon\Carbon::parse($dv->data_encerramento)->format('d/m/Y') }}
</div>
@endif
@if ($dv->nota_minima)
<div class="flex items-center gap-1.5">
    <x-lucide-check-circle class="w-3.5 h-3.5 text-slate-400" />
    Nota mínima: {{ $dv->nota_minima }}%
</div>
@endif
@if ($dv->creator)
<div class="flex items-center gap-1.5 col-span-2">
    <x-lucide-user class="w-3.5 h-3.5 text-slate-400" />
    Criado por {{ $dv->creator->name }}
</div>
@endif
        </div>

    </div>

    {{-- Footer --}}
    <div class="shrink-0 border-t border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center gap-3">
        <button wire:click="openVagaEdit({{ $dv->id }})" @click="$wire.set('vagaDrawer', false)" type="button"
    class="cursor-pointer flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs lato-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
<x-lucide-pencil class="w-4 h-4" /> Editar vaga
        </button>
        <button wire:click="setAba('pipeline')" @click="$wire.set('vagaDrawer', false)" type="button"
    class="cursor-pointer flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs lato-bold bg-indigo-600 hover:bg-indigo-700 text-white transition">
<x-lucide-git-branch class="w-4 h-4" /> Ver Pipeline
        </button>
    </div>

</div>
@endif
