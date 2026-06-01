@php
    use Illuminate\Support\Facades\Storage;
    if (!function_exists('portalVagaBg')) {
        function portalVagaBg(string $n): string {
            $pal = ['bg-indigo-500','bg-indigo-600','bg-teal-500','bg-orange-500','bg-cyan-500','bg-rose-500'];
            return $pal[abs(crc32($n)) % count($pal)];
        }
    }
    $escLabels = ['fundamental'=>'Fundamental','medio'=>'Médio','tecnico'=>'Técnico','graduacao'=>'Graduação','pos_graduacao'=>'Pós-Graduação','mestrado'=>'Mestrado','doutorado'=>'Doutorado'];
    $statusCurrMap = [
        'ativo'          => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',  'Ativo'],
        'favorito'       => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',  'Favorito'],
        'banco_talentos' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',      'Banco Talentos'],
        'inativo'        => ['bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',     'Inativo'],
    ];
    $vagaStatusCls = ['rascunho'=>'bg-slate-100 text-slate-600','publicada'=>'bg-green-100 text-green-700','pausada'=>'bg-amber-100 text-amber-700','encerrada'=>'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400','preenchida'=>'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'];
    $vagaStatusLbl = ['rascunho'=>'Rascunho','publicada'=>'Publicada','pausada'=>'Pausada','encerrada'=>'Encerrada','preenchida'=>'Preenchida ✓'];
    $vagaModalLbl  = ['presencial'=>'Presencial','remoto'=>'Remoto','hibrido'=>'Híbrido'];
    $etapaCores    = ['bg-slate-400','bg-blue-400','bg-indigo-400','bg-violet-400','bg-purple-400','bg-green-400','bg-red-400','bg-amber-400','bg-teal-400','bg-orange-400'];
@endphp

{{-- ═══════════════════════════════════════════════════════════════════════
     PORTAL RH  –  Layout: sidebar esquerda + área de conteúdo
═══════════════════════════════════════════════════════════════════════ --}}
<div class="flex overflow-hidden bg-slate-50 dark:bg-slate-900"
     x-data="{ nav: false }"
     x-init="
         const setH = () => { $el.style.height = $el.parentElement.clientHeight + 'px'; };
         setH();
         window.addEventListener('resize', setH);
     ">

    {{-- ── OVERLAY MOBILE ──────────────────────────────────────────── --}}
    <div x-show="nav" @click="nav=false" style="cursor:pointer"
         class="fixed inset-0 bg-black/40 z-30 lg:hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display:none"></div>

    {{-- ── SIDEBAR DE NAVEGAÇÃO DO PORTAL ──────────────────────────── --}}
    <aside :class="nav ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed lg:relative inset-y-0 left-0 z-40 w-60 shrink-0
                  bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700
                  flex flex-col transition-transform duration-200 ease-in-out">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                    <x-lucide-users class="w-4 h-4 text-white" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-black text-slate-800 dark:text-white leading-tight">Portal RH</p>
                    <p class="text-[11px] text-slate-400 lato-regular">Recrutamento & Seleção</p>
                </div>
                {{-- Fechar no mobile --}}
                <button @click="nav=false" type="button"
                        class="cursor-pointer lg:hidden p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Navegação --}}
        <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
            @php
                $navItems = [
                    ['id'=>'dashboard',      'label'=>'Dashboard',      'icon'=>'layout-dashboard', 'dot'=>'bg-blue-500'],
                    ['id'=>'curriculos',     'label'=>'Currículos',     'icon'=>'file-text',        'dot'=>'bg-indigo-500'],
                    ['id'=>'vagas',          'label'=>'Vagas',          'icon'=>'briefcase',        'dot'=>'bg-indigo-600'],
                    ['id'=>'pipeline',       'label'=>'Pipeline',       'icon'=>'git-branch',       'dot'=>'bg-teal-500'],
                    ['id'=>'testes',         'label'=>'Testes Online',  'icon'=>'clipboard-list',   'dot'=>'bg-orange-500'],
                    ['id'=>'desligamentos',       'label'=>'Desligamentos',  'icon'=>'user-minus',       'dot'=>'bg-rose-500'],
                    ['id'=>'onboarding',          'label'=>'Onboarding',     'icon'=>'user-check',       'dot'=>'bg-emerald-500'],
                    ['id'=>'relatorio-turnover',  'label'=>'Turnover',         'icon'=>'bar-chart-2',      'dot'=>'bg-pink-500'],
                    ['id'=>'clima-unificado',     'label'=>'Clima Unificado',  'icon'=>'sun-medium',       'dot'=>'bg-indigo-600'],
                    ['id'=>'solicitacoes',         'label'=>'Solicitações RH',  'icon'=>'inbox',            'dot'=>'bg-amber-500'],
                    ['id'=>'mapa-competencias',    'label'=>'Mapa Competências', 'icon'=>'layout-grid',      'dot'=>'bg-cyan-500'],
                    ['id'=>'people-analytics',     'label'=>'People Analytics',  'icon'=>'users-round',      'dot'=>'bg-indigo-600'],
                    ['id'=>'humor-equipes',        'label'=>'Humor das Equipes', 'icon'=>'heart-pulse',      'dot'=>'bg-rose-500'],
                    ['id'=>'sucessao',             'label'=>'Plano de Sucessão', 'icon'=>'git-branch',       'dot'=>'bg-violet-500'],
                ];
            @endphp
            @foreach ($navItems as $item)
            <button wire:click="setAba('{{ $item['id'] }}')" @click="nav=false" type="button"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition lato-regular cursor-pointer
                           {{ $aba === $item['id']
                              ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 lato-bold'
                              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                <x-dynamic-component :component="'lucide-'.$item['icon']"
                    class="w-4 h-4 shrink-0 {{ $aba === $item['id'] ? 'text-blue-500' : 'text-slate-400' }}" />
                {{ $item['label'] }}
                @if ($aba === $item['id'])
                <div class="ml-auto w-1.5 h-1.5 rounded-full {{ $item['dot'] }}"></div>
                @endif
            </button>
            @endforeach

            <div class="pt-3 mt-3 border-t border-slate-100 dark:border-slate-700">
                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Links rápidos</p>
                <a href="{{ route('dashboard') }}"
                   class="cursor-pointer flex items-center gap-3 px-3 py-2 rounded-xl text-xs text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition lato-regular">
                    <x-lucide-arrow-left class="w-3.5 h-3.5" /> Voltar
                </a>
            </div>
        </nav>

        {{-- Stats rápidos --}}
        @php $st = $this->stats; @endphp
        <div class="p-3 border-t border-slate-200 dark:border-slate-700 space-y-1">
            <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900">
                <span class="text-xs text-slate-500 lato-regular">Candidatos</span>
                <span class="text-xs lato-black text-slate-800 dark:text-white">{{ $st['total_candidatos'] }}</span>
            </div>
            <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900">
                <span class="text-xs text-slate-500 lato-regular">Vagas abertas</span>
                <span class="text-xs lato-black text-green-600">{{ $st['vagas_abertas'] }}</span>
            </div>
        </div>
    </aside>

    {{-- ── ÁREA DE CONTEÚDO ─────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">

        {{-- Barra mobile (hambúrguer + aba atual) --}}
        @php
            $abaLabels = ['dashboard'=>'Dashboard','curriculos'=>'Currículos','vagas'=>'Vagas','pipeline'=>'Pipeline','testes'=>'Testes Online','desligamentos'=>'Desligamentos','onboarding'=>'Onboarding','relatorio-turnover'=>'Turnover','clima-unificado'=>'Clima Unificado','solicitacoes'=>'Solicitações RH','mapa-competencias'=>'Mapa de Competências','people-analytics'=>'People Analytics','humor-equipes'=>'Humor das Equipes','sucessao'=>'Plano de Sucessão'];
            $abaIcons  = ['dashboard'=>'layout-dashboard','curriculos'=>'file-text','vagas'=>'briefcase','pipeline'=>'git-branch','testes'=>'clipboard-list','desligamentos'=>'user-minus','onboarding'=>'user-check','relatorio-turnover'=>'bar-chart-2','clima-unificado'=>'sun-medium','solicitacoes'=>'inbox','mapa-competencias'=>'layout-grid','people-analytics'=>'users-round','humor-equipes'=>'heart-pulse','sucessao'=>'git-branch'];
        @endphp
        <header class="lg:hidden sticky top-0 z-20 flex items-center gap-3 px-4 py-3
                        bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shrink-0">
            <button @click="nav=true" type="button"
                    class="cursor-pointer p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-menu class="w-5 h-5" />
            </button>
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <x-dynamic-component :component="'lucide-'.$abaIcons[$aba]" class="w-4 h-4 text-blue-500 shrink-0" />
                <span class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $abaLabels[$aba] }}</span>
            </div>
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                <x-lucide-users class="w-4 h-4 text-white" />
            </div>
        </header>

        <div class="flex-1 overflow-y-auto min-h-0">

        @include('livewire.pages.rh.portal.tabs.dashboard')
        @include('livewire.pages.rh.portal.tabs.curriculos')
        @include('livewire.pages.rh.portal.tabs.vagas')
        @include('livewire.pages.rh.portal.tabs.pipeline')
        @include('livewire.pages.rh.portal.tabs.testes')
        @include('livewire.pages.rh.portal.tabs.desligamentos')
        @include('livewire.pages.rh.portal.tabs.onboarding')
        @include('livewire.pages.rh.portal.tabs.relatorio-turnover')
        @include('livewire.pages.rh.portal.tabs.clima-unificado')
        @include('livewire.pages.rh.portal.tabs.solicitacoes')
        @include('livewire.pages.rh.portal.tabs.mapa-competencias')
        @include('livewire.pages.rh.portal.tabs.people-analytics')
        @include('livewire.pages.rh.portal.tabs.humor-equipes')
        @include('livewire.pages.rh.portal.tabs.sucessao')
    </div>{{-- fim área de conteúdo --}}
</div>{{-- fim portal --}}

{{-- ═══════════════════════════════════════════════════════════════════════
     MODAIS GLOBAIS — fixed position, sempre no DOM
═══════════════════════════════════════════════════════════════════════ --}}

@include('livewire.pages.rh.portal.modals._drawer-vaga')
@include('livewire.pages.rh.portal.modals._modal-upload-curriculo')
@include('livewire.pages.rh.portal.modals._drawer-curriculo')
@include('livewire.pages.rh.portal.modals._modal-editar-curriculo')
@include('livewire.pages.rh.portal.modals._modal-excluir-curriculo')
@include('livewire.pages.rh.portal.modals._modal-vaga')
@include('livewire.pages.rh.portal.modals._modal-excluir-vaga')
@include('livewire.pages.rh.portal.modals._modal-teste-enviado')
@include('livewire.pages.rh.portal.modals._modal-desligamento')
@include('livewire.pages.rh.portal.modals._drawer-desligamento')
@include('livewire.pages.rh.portal.modals._modal-verbas')
@include('livewire.pages.rh.portal.modals._modal-solicitacao')
