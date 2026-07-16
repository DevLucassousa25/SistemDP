<div class="p-4 sm:p-6 lg:p-8"
     x-data
     x-effect="document.body.style.overflow = ($wire.modalCurso || $wire.modalAula || $wire.modalTeste || $wire.confirmDelete || $wire.modalObrigatorio) ? 'hidden' : ''">

    {{-- ───── Cabeçalho ─────────────────────────────────────────────────── --}}
    <div class="relative rounded-2xl overflow-hidden mb-6 bg-gradient-to-br from-blue-500 via-indigo-600 to-indigo-700 shadow-lg shadow-indigo-500/20">
        {{-- Decorative blobs --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-black/10 rounded-full translate-y-1/2 -translate-x-1/4 pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-6 py-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center shrink-0 border border-white/20">
                    <x-lucide-graduation-cap class="w-6 h-6 text-white" />
                </div>
                <div>
                    @if($cursoSelecionadoId)
                        <button wire:click="voltarLista" class="cursor-pointer flex items-center gap-1.5 text-xs text-white/60 hover:text-white mb-1 transition lato-regular">
                            <x-lucide-arrow-left class="w-3.5 h-3.5" /> Voltar à lista
                        </button>
                        <h1 class="text-xl font-bold text-white lato-black">{{ $this->cursoSelecionado?->titulo }}</h1>
                        <p class="text-sm text-white/60 mt-0.5 lato-regular">Gerencie aulas, teste e conteúdo do curso</p>
                    @else
                        <h1 class="text-xl lg:text-2xl font-bold text-white lato-black">Gestão de Treinamentos</h1>
                        <p class="text-sm text-white/60 mt-0.5 lato-regular">Crie e gerencie cursos, videoaulas, testes e dúvidas</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 shrink-0">
                @if(!$cursoSelecionadoId)
                <button wire:click="abrirModalCurso('criar')"
                    class="cursor-pointer flex items-center gap-2 bg-white text-violet-700 hover:bg-violet-50 text-sm font-medium px-4 py-2.5 rounded-xl transition lato-bold shadow-sm">
                    <x-lucide-plus class="w-4 h-4" /> Novo curso
                </button>
                @endif
                <a href="{{ route('treinamentos') }}" wire:navigate
                   class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm px-4 py-2.5 rounded-xl transition lato-regular backdrop-blur-sm">
                    <x-lucide-eye class="w-4 h-4" /> Ver como aluno
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         DETALHE DO CURSO SELECIONADO
    ══════════════════════════════════════════════════════════════════ --}}
    @if($cursoSelecionadoId && $this->cursoSelecionado)
        @php $curso = $this->cursoSelecionado; @endphp

        {{-- Abas do detalhe --}}
        <div class="flex gap-1 bg-slate-100 dark:bg-slate-800 rounded-xl p-1 w-fit mb-6">
            @foreach(['info'=>'Informações','aulas'=>'Aulas','teste'=>'Teste','alunos'=>'Alunos'] as $k => $label)
            <button wire:click="$set('abaDetalhe','{{ $k }}')"
                class="cursor-pointer px-4 py-2 text-sm rounded-lg transition lato-bold
                    {{ $abaDetalhe === $k ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                {{ $label }}
                @if($k === 'aulas') <span class="ml-1 text-xs bg-violet-100 dark:bg-violet-900/30 text-indigo-600 dark:text-indigo-400 px-1.5 rounded-full">{{ $curso->aulas->count() }}</span> @endif
                @if($k === 'alunos') <span class="ml-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-1.5 rounded-full">{{ $curso->inscricoes->count() }}</span> @endif
            </button>
            @endforeach
        </div>

        {{-- ── Aba: INFO ────────────────────────────────────────────── --}}
        @if($abaDetalhe === 'info')
        @php
            $nivelCores = ['basico' => ['emerald','Básico'], 'intermediario' => ['amber','Intermediário'], 'avancado' => ['rose','Avançado']];
            $nivelInfo  = $nivelCores[$curso->nivel] ?? ['slate', ucfirst($curso->nivel)];
            $statusCores = ['ativo' => ['emerald','bg-emerald-400'], 'rascunho' => ['amber','bg-amber-400'], 'arquivado' => ['slate','bg-slate-400']];
            $statusInfo  = $statusCores[$curso->status] ?? ['slate','bg-slate-400'];
        @endphp

        {{-- Banner do curso --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden mb-5">
            <div class="h-1.5 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
            <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-5">
                {{-- Ícone --}}
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0 shadow-lg shadow-indigo-500/20">
                    <x-lucide-graduation-cap class="w-8 h-8 text-white" />
                </div>
                {{-- Info principal --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="px-2 py-0.5 text-[10px] rounded-full lato-bold bg-{{ $nivelInfo[0] }}-100 dark:bg-{{ $nivelInfo[0] }}-900/30 text-{{ $nivelInfo[0] }}-700 dark:text-{{ $nivelInfo[0] }}-300">
                            {{ $nivelInfo[1] }}
                        </span>
                        <span class="flex items-center gap-1.5 px-2 py-0.5 text-[10px] rounded-full lato-bold
                            {{ match($curso->status) {
                                'ativo'    => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                'rascunho' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                default    => 'bg-slate-100 dark:bg-slate-700 text-slate-500',
                            } }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusInfo[1] }}"></span>
                            {{ ucfirst($curso->status) }}
                        </span>
                        <span class="text-[10px] text-slate-400 lato-regular">{{ ucfirst($curso->tipo) }}</span>
                    </div>
                    <h2 class="text-lg lato-black text-slate-800 dark:text-white truncate">{{ $curso->titulo }}</h2>
                    @if($curso->instrutor)
                    <p class="text-xs text-slate-400 lato-regular mt-0.5 flex items-center gap-1">
                        <x-lucide-user class="w-3 h-3" /> {{ $curso->instrutor }}
                    </p>
                    @endif
                </div>
                {{-- Ações --}}
                <div class="flex gap-2 shrink-0">
                    <button wire:click="abrirModalCurso('editar', {{ $curso->id }})"
                        class="cursor-pointer flex items-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                        <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                    </button>
                    <button wire:click="confirmarExclusao({{ $curso->id }}, 'curso')"
                        class="cursor-pointer flex items-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-400 hover:border-red-300 hover:text-red-500 transition disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmarExclusao" class="flex items-center gap-1.5">
                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                        </span>
                        <span wire:loading wire:target="confirmarExclusao" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-5">

            {{-- Cards de métricas --}}
            <div class="lg:col-span-3 grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center shrink-0">
                        <x-lucide-clock class="w-4 h-4 text-indigo-500" />
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 lato-regular">Carga horária</p>
                        <p class="text-sm lato-black text-slate-700 dark:text-slate-200">{{ $curso->carga_horaria_formatada }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                        <x-lucide-users class="w-4 h-4 text-blue-500" />
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 lato-regular">Inscritos</p>
                        <p class="text-sm lato-black text-slate-700 dark:text-slate-200">{{ $curso->inscricoes->count() }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center shrink-0">
                        <x-lucide-video class="w-4 h-4 text-emerald-500" />
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 lato-regular">Aulas</p>
                        <p class="text-sm lato-black text-slate-700 dark:text-slate-200">{{ $curso->aulas->count() }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center shrink-0">
                        <x-lucide-award class="w-4 h-4 text-amber-500" />
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 lato-regular">Certificado</p>
                        <p class="text-sm lato-black text-slate-700 dark:text-slate-200">{{ $curso->certificado_habilitado ? 'Habilitado' : 'Desativado' }}</p>
                    </div>
                </div>
            </div>

            {{-- Detalhes --}}
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                    <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Informações</p>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @php
                        $infos = [
                            ['icon' => 'layers', 'label' => 'Tipo', 'value' => ucfirst($curso->tipo)],
                            ['icon' => 'tag', 'label' => 'Categoria', 'value' => $curso->categoria ?: '—'],
                            ['icon' => 'user', 'label' => 'Instrutor', 'value' => $curso->instrutor ?: '—'],
                            ['icon' => 'bar-chart-2', 'label' => 'Nível', 'value' => $nivelInfo[1]],
                        ];
                    @endphp
                    @foreach($infos as $info)
                    <div class="flex items-center gap-4 px-5 py-3.5">
                        <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-700/50 flex items-center justify-center shrink-0">
                            <x-dynamic-component :component="'lucide-'.$info['icon']" class="w-3.5 h-3.5 text-slate-400" />
                        </div>
                        <span class="text-xs text-slate-400 lato-regular flex-1">{{ $info['label'] }}</span>
                        <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $info['value'] }}</span>
                    </div>
                    @endforeach
                    <div class="flex items-center gap-4 px-5 py-3.5">
                        <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-700/50 flex items-center justify-center shrink-0">
                            <x-lucide-users class="w-3.5 h-3.5 text-slate-400" />
                        </div>
                        <span class="text-xs text-slate-400 lato-regular flex-1">Inscrições abertas</span>
                        <span class="flex items-center gap-1.5 text-xs lato-bold {{ $curso->inscricao_aberta ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                            @if($curso->inscricao_aberta)
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Sim
                            @else
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></span> Não
                            @endif
                        </span>
                    </div>
                    @if($curso->data_inicio || $curso->data_fim)
                    <div class="flex items-center gap-4 px-5 py-3.5">
                        <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-700/50 flex items-center justify-center shrink-0">
                            <x-lucide-calendar class="w-3.5 h-3.5 text-slate-400" />
                        </div>
                        <span class="text-xs text-slate-400 lato-regular flex-1">Período</span>
                        <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">
                            {{ $curso->data_inicio ? \Carbon\Carbon::parse($curso->data_inicio)->format('d/m/Y') : '—' }}
                            @if($curso->data_fim) → {{ \Carbon\Carbon::parse($curso->data_fim)->format('d/m/Y') }} @endif
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Descrição --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                    <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Descrição</p>
                </div>
                <div class="p-5">
                    @if($curso->descricao)
                        <p class="text-sm text-slate-600 dark:text-slate-300 lato-regular leading-relaxed">{{ $curso->descricao }}</p>
                    @else
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <x-lucide-file-text class="w-8 h-8 text-slate-200 dark:text-slate-700 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Sem descrição cadastrada.</p>
                            <button wire:click="abrirModalCurso('editar', {{ $curso->id }})"
                                class="cursor-pointer mt-2 text-xs text-indigo-500 hover:underline lato-bold">Adicionar →</button>
                        </div>
                    @endif
                </div>
            </div>

        </div>
        @endif

        {{-- ── Aba: AULAS ───────────────────────────────────────────── --}}
        @if($abaDetalhe === 'aulas')
        <div class="flex justify-between items-center mb-4">
            <p class="text-sm text-slate-400 lato-regular">{{ $curso->aulas->count() }} aula(s) cadastrada(s)</p>
            <button wire:click="abrirModalAula('criar')"
                class="cursor-pointer flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm px-4 py-2 rounded-lg transition lato-bold">
                <x-lucide-plus class="w-4 h-4" /> Adicionar aula
            </button>
        </div>
        <div class="space-y-3">
            @forelse($curso->aulas as $aula)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center shrink-0 text-sm lato-black text-indigo-600 dark:text-indigo-400">
                    {{ $aula->ordem + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $aula->titulo }}</p>
                        @if($aula->video_url)
                            <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded-full lato-regular">
                                <x-lucide-video class="w-3 h-3 inline -mt-0.5" /> Vídeo
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ $aula->duracao_formatada }}{{ $aula->descricao ? ' · '.\Illuminate\Support\Str::limit($aula->descricao, 60) : '' }}</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button wire:click="abrirModalAula('editar', {{ $aula->id }})"
                        class="cursor-pointer p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                        <x-lucide-pencil class="w-4 h-4" />
                    </button>
                    <button wire:click="confirmarExclusao({{ $aula->id }}, 'aula')"
                        class="cursor-pointer p-2 text-slate-400 hover:text-red-500 transition disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmarExclusao" class="flex items-center gap-1.5">
                            <x-lucide-trash-2 class="w-4 h-4" />
                        </span>
                        <span wire:loading wire:target="confirmarExclusao" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </button>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                <x-lucide-video class="w-10 h-10 mb-3 opacity-40" />
                <p class="text-sm lato-bold">Nenhuma aula cadastrada</p>
                <button wire:click="abrirModalAula('criar')"
                    class="cursor-pointer mt-3 text-sm text-indigo-600 dark:text-indigo-400 hover:underline lato-bold">
                    Adicionar primeira aula →
                </button>
            </div>
            @endforelse
        </div>
        @endif

        {{-- ── Aba: TESTE ───────────────────────────────────────────── --}}
        @if($abaDetalhe === 'teste')
        @if($curso->teste)
            <div class="flex justify-between items-center mb-4">
                <p class="text-sm text-slate-400 lato-regular">{{ $curso->teste->questoes->count() }} questão(ões) cadastrada(s)</p>
                <button wire:click="abrirModalTeste"
                    class="cursor-pointer flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm px-4 py-2 rounded-lg transition lato-bold">
                    <x-lucide-pencil class="w-4 h-4" /> Editar teste
                </button>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 mb-4">
                <div class="flex flex-wrap gap-4 text-sm">
                    <span><strong class="lato-bold text-slate-700 dark:text-slate-200">Nota mínima:</strong> <span class="text-slate-400 lato-regular">{{ $curso->teste->nota_minima }}%</span></span>
                    <span><strong class="lato-bold text-slate-700 dark:text-slate-200">Tentativas:</strong> <span class="text-slate-400 lato-regular">{{ $curso->teste->tentativas_maximas }}</span></span>
                    <span><strong class="lato-bold text-slate-700 dark:text-slate-200">Embaralhar:</strong> <span class="text-slate-400 lato-regular">{{ $curso->teste->embaralhar_questoes ? 'Sim' : 'Não' }}</span></span>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($curso->teste->questoes as $qi => $q)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-3">
                        <span class="text-indigo-500">{{ $qi + 1 }}.</span> {{ $q->enunciado }}
                    </p>
                    <div class="space-y-1.5 pl-5">
                        @foreach($q->opcoes as $o)
                        <div class="flex items-center gap-2 text-sm">
                            @if($o->correta)
                                <x-lucide-check-circle class="w-4 h-4 text-emerald-500 shrink-0" />
                                <span class="text-emerald-600 dark:text-emerald-400 lato-bold">{{ $o->texto }}</span>
                            @else
                                <x-lucide-circle class="w-4 h-4 text-slate-300 dark:text-slate-600 shrink-0" />
                                <span class="text-slate-500 dark:text-slate-400 lato-regular">{{ $o->texto }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                <x-lucide-clipboard-list class="w-10 h-10 mb-3 opacity-40" />
                <p class="text-sm lato-bold">Nenhum teste criado para este curso</p>
                <button wire:click="abrirModalTeste"
                    class="cursor-pointer mt-4 flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm px-4 py-2 rounded-lg transition lato-bold">
                    <x-lucide-plus class="w-4 h-4" /> Criar teste
                </button>
            </div>
        @endif
        @endif

        {{-- ── Aba: ALUNOS ──────────────────────────────────────────── --}}
        @if($abaDetalhe === 'alunos')

        {{-- Painel: Inscrições Obrigatórias --}}
        <div class="mb-6 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-2">
                    <x-lucide-shield-check class="w-4 h-4 text-indigo-500" />
                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">Inscrições Obrigatórias</span>
                    <span class="text-xs bg-violet-100 dark:bg-violet-900/30 text-indigo-600 dark:text-indigo-400 px-1.5 rounded-full lato-bold">
                        {{ $this->obrigatorios->count() }}
                    </span>
                </div>
                <button wire:click="abrirModalObrigatorio"
                    class="cursor-pointer flex items-center gap-1.5 text-xs bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-3 py-1.5 rounded-lg transition lato-bold">
                    <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar
                </button>
            </div>
            @if($this->obrigatorios->isNotEmpty())
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($this->obrigatorios as $obrig)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                        {{ $obrig->user_id ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'bg-violet-100 dark:bg-violet-900/30 text-indigo-600 dark:text-indigo-400' }}">
                        @if($obrig->user_id)
                            <x-lucide-user class="w-4 h-4" />
                        @else
                            <x-lucide-building-2 class="w-4 h-4" />
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">
                            {{ $obrig->user_id ? $obrig->usuario->name : ($obrig->departamento->name ?? 'Departamento') }}
                        </p>
                        <p class="text-xs text-slate-400 lato-regular">
                            {{ $obrig->user_id ? 'Colaborador' : 'Departamento' }}
                            @if($obrig->prazo) · Prazo: {{ \Carbon\Carbon::parse($obrig->prazo)->format('d/m/Y') }} @endif
                        </p>
                    </div>
                    <button wire:click="removerObrigatorio({{ $obrig->id }})"
                        wire:confirm="Remover esta inscrição obrigatória?"
                        class="cursor-pointer text-slate-300 dark:text-slate-600 hover:text-red-500 transition">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-6 text-center text-slate-400 text-sm lato-regular">
                Nenhuma inscrição obrigatória configurada.
            </div>
            @endif
        </div>

        {{-- Lista de Alunos --}}
        <div class="space-y-3">
            @forelse($curso->inscricoes as $inscricao)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-700 flex items-center justify-center shrink-0 text-white text-sm lato-bold">
                    {{ substr($inscricao->usuario->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $inscricao->usuario->name }}</p>
                    <p class="text-xs text-slate-400 lato-regular">{{ $inscricao->usuario->email }}</p>
                    <div class="mt-1 flex items-center gap-3">
                        <div class="w-24 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $inscricao->progresso }}%"></div>
                        </div>
                        <span class="text-xs text-slate-400 lato-regular">{{ $inscricao->progresso }}%</span>
                    </div>
                    @if($inscricao->prazo_conclusao && $inscricao->status !== 'concluido')
                        @php $dp = now()->startOfDay()->diffInDays($inscricao->prazo_conclusao, false); @endphp
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full mt-1 lato-bold
                            {{ $dp < 0 ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : ($dp <= 3 ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400') }}">
                            <x-lucide-calendar class="w-3 h-3" />
                            {{ $dp < 0 ? 'Vencido' : 'Prazo: '.$inscricao->prazo_conclusao->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <span class="px-2 py-0.5 text-xs rounded-full lato-bold
                        {{ match($inscricao->status) {
                            'concluido'    => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                            'em_andamento' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                            'reprovado'    => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                            default        => 'bg-slate-100 dark:bg-slate-700 text-slate-500',
                        } }}">
                        {{ match($inscricao->status) {
                            'concluido'    => 'Concluído',
                            'em_andamento' => 'Em andamento',
                            'reprovado'    => 'Reprovado',
                            default        => 'Inscrito',
                        } }}
                    </span>
                    <p class="text-xs text-slate-400 lato-regular mt-1">{{ $inscricao->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                <x-lucide-users class="w-10 h-10 mb-3 opacity-40" />
                <p class="text-sm lato-bold">Nenhum aluno inscrito ainda</p>
            </div>
            @endforelse
        </div>
        @endif

    {{-- ══════════════════════════════════════════════════════════════════
         LISTA DE CURSOS
    ══════════════════════════════════════════════════════════════════ --}}
    @else

        {{-- Abas principais --}}
        <div class="flex gap-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl p-1 w-fit mb-6 border border-slate-200 dark:border-slate-700/50">
            <button wire:click="$set('aba','cursos')"
                class="cursor-pointer flex items-center gap-2 px-5 py-2.5 text-sm rounded-xl transition lato-bold
                    {{ $aba === 'cursos' ? 'bg-white dark:bg-slate-700 text-violet-700 dark:text-violet-300 shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                <x-lucide-book-open class="w-4 h-4" /> Cursos
            </button>
            <button wire:click="$set('aba','duvidas')"
                class="cursor-pointer flex items-center gap-2 px-5 py-2.5 text-sm rounded-xl transition lato-bold
                    {{ $aba === 'duvidas' ? 'bg-white dark:bg-slate-700 text-violet-700 dark:text-violet-300 shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                <x-lucide-help-circle class="w-4 h-4" /> Dúvidas dos Alunos
                @php $totalAbertas = \App\Models\TreinamentoDuvida::where('status','aberta')->count(); @endphp
                @if($totalAbertas > 0)
                    <span class="w-5 h-5 flex items-center justify-center text-[10px] lato-black rounded-full
                        {{ $aba === 'duvidas' ? 'bg-violet-100 dark:bg-violet-900/40 text-indigo-600 dark:text-indigo-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }}">
                        {{ $totalAbertas > 9 ? '9+' : $totalAbertas }}
                    </span>
                @endif
            </button>
        </div>

        {{-- ── Aba: CURSOS ──────────────────────────────────────────── --}}
        @if($aba === 'cursos')

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
                @foreach([
                    ['Total de cursos', $this->stats['total'], 'bg-slate-100 dark:bg-slate-800', 'text-slate-800 dark:text-white'],
                    ['Ativos', $this->stats['ativos'], 'bg-emerald-50 dark:bg-emerald-900/20', 'text-emerald-600 dark:text-emerald-400'],
                    ['Rascunhos', $this->stats['rascunho'], 'bg-amber-50 dark:bg-amber-900/20', 'text-amber-600 dark:text-amber-400'],
                    ['Inscritos', $this->stats['inscritos'], 'bg-blue-50 dark:bg-blue-900/20', 'text-blue-600 dark:text-blue-400'],
                    ['Concluídos', $this->stats['concluidos'], 'bg-violet-50 dark:bg-violet-900/20', 'text-indigo-600 dark:text-indigo-400'],
                ] as [$label, $val, $bg, $fg])
                <div class="{{ $bg }} rounded-2xl p-4">
                    <p class="text-xs text-slate-400 lato-regular">{{ $label }}</p>
                    <p class="text-2xl font-bold {{ $fg }} mt-1 lato-black">{{ $val }}</p>
                </div>
                @endforeach
            </div>

            {{-- Filtros --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-3 flex items-center gap-3 flex-wrap mb-5">

                {{-- Busca --}}
                <div class="relative flex-1 min-w-[160px]">
                    <x-lucide-search class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400 pointer-events-none" />
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Buscar por título ou categoria…"
                        class="w-full pl-7 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700
                               bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100
                               focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400" />
                </div>

                {{-- Separador --}}
                <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 hidden sm:block"></div>

                {{-- Filtro: Status --}}
                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                    <button @click="open = !open" type="button"
                        class="cursor-pointer flex items-center gap-2 text-xs px-3 py-2 rounded-xl transition whitespace-nowrap lato-bold
                               {{ $statusFiltro !== 'todos'
                                   ? 'border border-violet-300 dark:border-violet-700 bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300'
                                   : 'border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-800' }}">
                        @php
                            $statusDots = ['ativo' => 'bg-emerald-400', 'rascunho' => 'bg-amber-400', 'arquivado' => 'bg-slate-400'];
                            $statusLabels = ['ativo' => 'Ativo', 'rascunho' => 'Rascunho', 'arquivado' => 'Arquivado'];
                        @endphp
                        <span class="w-2 h-2 rounded-full shrink-0 {{ $statusFiltro !== 'todos' ? ($statusDots[$statusFiltro] ?? 'bg-slate-300') : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                        {{ $statusFiltro !== 'todos' ? ($statusLabels[$statusFiltro] ?? $statusFiltro) : 'Status' }}
                        @if($statusFiltro !== 'todos')
                            <button wire:click="$set('statusFiltro','todos')" @click.stop type="button"
                                class="cursor-pointer ml-0.5 text-indigo-400 hover:text-indigo-600 transition">
                                <x-lucide-x class="w-3 h-3" />
                            </button>
                        @else
                            <x-lucide-chevron-down class="w-3 h-3 text-slate-400 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
                        @endif
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute top-full right-0 mt-1.5 z-30 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg py-1 min-w-[160px]"
                         style="display:none;">
                        @foreach([
                            ['todos',     'Todos os status', 'bg-slate-300 dark:bg-slate-500'],
                            ['ativo',     'Ativo',           'bg-emerald-400'],
                            ['rascunho',  'Rascunho',        'bg-amber-400'],
                            ['arquivado', 'Arquivado',       'bg-slate-400'],
                        ] as [$val, $label, $dot])
                        <button type="button" @click="$wire.set('statusFiltro', '{{ $val }}'); open = false"
                            class="cursor-pointer w-full flex items-center gap-2.5 px-3 py-2 text-xs lato-regular text-left transition
                                   {{ $statusFiltro === $val ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span>
                            {{ $label }}
                            @if($statusFiltro === $val) <x-lucide-check class="w-3 h-3 ml-auto text-indigo-500" /> @endif
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Limpar filtros --}}
                @if($search || $statusFiltro !== 'todos')
                <button wire:click="$set('search',''); $set('statusFiltro','todos')" type="button"
                    class="cursor-pointer flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 lato-regular transition">
                    <x-lucide-x class="w-3 h-3" /> Limpar
                </button>
                @endif

            </div>

            {{-- Tabela de cursos --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400">Curso</th>
                            <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 hidden sm:table-cell">Tipo</th>
                            <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 hidden md:table-cell">Nível</th>
                            <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 hidden lg:table-cell">Inscritos</th>
                            <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($this->cursos as $curso)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                            <td class="px-4 py-3">
                                <div>
                                    <p class="lato-bold text-slate-700 dark:text-slate-200">{{ $curso->titulo }}</p>
                                    @if($curso->categoria)
                                        <p class="text-xs text-slate-400 lato-regular">{{ $curso->categoria }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <span class="text-xs lato-regular text-slate-500 dark:text-slate-400">{{ ucfirst($curso->tipo) }}</span>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                @php $cores = ['basico'=>'emerald','intermediario'=>'amber','avancado'=>'rose']; $cor = $cores[$curso->nivel] ?? 'slate'; @endphp
                                <span class="px-2 py-0.5 text-xs rounded-full lato-bold bg-{{ $cor }}-100 dark:bg-{{ $cor }}-900/30 text-{{ $cor }}-700 dark:text-{{ $cor }}-300">
                                    {{ ucfirst($curso->nivel) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <span class="text-xs text-slate-500 lato-regular">{{ $curso->inscricoes_count }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs rounded-full lato-bold
                                    {{ match($curso->status) {
                                        'ativo'    => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                        'rascunho' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                        default    => 'bg-slate-100 dark:bg-slate-700 text-slate-500',
                                    } }}">
                                    {{ ucfirst($curso->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="selecionarCurso({{ $curso->id }})"
                                    class="cursor-pointer text-xs text-indigo-600 dark:text-indigo-400 hover:underline lato-bold">
                                    Gerenciar →
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-12 text-center text-slate-400 lato-regular text-sm">Nenhum curso encontrado</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $this->cursos->links() }}
                </div>
            </div>
        @endif

        {{-- ── Aba: DÚVIDAS ─────────────────────────────────────────── --}}
        @if($aba === 'duvidas')
        @php
            $cntAbertas     = \App\Models\TreinamentoDuvida::whereHas('treinamento')->where('status','aberta')->count();
            $cntRespondidas = \App\Models\TreinamentoDuvida::whereHas('treinamento')->where('status','respondida')->count();
            $cntTotal       = $cntAbertas + $cntRespondidas;
        @endphp

        {{-- Mini stats --}}
        <div class="grid grid-cols-3 gap-2 sm:gap-3 mb-5">
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-3 sm:p-4 flex flex-col sm:flex-row items-center sm:items-start gap-2 sm:gap-3 text-center sm:text-left">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center shrink-0">
                    <x-lucide-message-circle-question class="w-4 h-4 text-indigo-500" />
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] sm:text-[10px] text-slate-400 lato-regular leading-tight">Total</p>
                    <p class="text-base sm:text-lg lato-black text-slate-700 dark:text-slate-200">{{ $cntTotal }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 border border-amber-200 dark:border-amber-800/30 rounded-2xl p-3 sm:p-4 flex flex-col sm:flex-row items-center sm:items-start gap-2 sm:gap-3 text-center sm:text-left">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center shrink-0">
                    <x-lucide-clock class="w-4 h-4 text-amber-500" />
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] sm:text-[10px] text-slate-400 lato-regular leading-tight">Em aberto</p>
                    <p class="text-base sm:text-lg lato-black text-amber-600 dark:text-amber-400">{{ $cntAbertas }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-800/30 rounded-2xl p-3 sm:p-4 flex flex-col sm:flex-row items-center sm:items-start gap-2 sm:gap-3 text-center sm:text-left">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center shrink-0">
                    <x-lucide-circle-check-big class="w-4 h-4 text-emerald-500" />
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] sm:text-[10px] text-slate-400 lato-regular leading-tight">Respondidas</p>
                    <p class="text-base sm:text-lg lato-black text-emerald-600 dark:text-emerald-400">{{ $cntRespondidas }}</p>
                </div>
            </div>
        </div>

        {{-- Barra de filtros --}}
        <div class="flex items-center gap-2 mb-5 overflow-x-auto scrollbar-hide">
            @foreach([
                'todas'       => ['Todas',       null,           $cntTotal],
                'abertas'     => ['Abertas',      'bg-amber-400', $cntAbertas],
                'respondidas' => ['Respondidas',  'bg-emerald-400',$cntRespondidas],
            ] as $k => [$label, $dot, $cnt])
            <button wire:click="$set('duvidaStatusFiltro','{{ $k }}')"
                class="cursor-pointer flex items-center gap-2 px-4 py-2 text-xs rounded-xl transition lato-bold shrink-0
                    {{ $duvidaStatusFiltro === $k
                        ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-sm shadow-indigo-500/20'
                        : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-indigo-300 dark:hover:border-indigo-600' }}">
                @if($dot) <span class="w-1.5 h-1.5 rounded-full {{ $dot }} shrink-0"></span> @endif
                {{ $label }}
                <span class="text-[10px] px-1.5 py-0.5 rounded-full lato-black
                    {{ $duvidaStatusFiltro === $k ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500' }}">
                    {{ $cnt }}
                </span>
            </button>
            @endforeach
        </div>

        <div class="grid lg:grid-cols-5 gap-4">

            {{-- ── Lista de dúvidas ── --}}
            <div class="lg:col-span-2 flex flex-col gap-2">
                @forelse($this->duvidas as $duvida)
                @php $isAtiva = $duvidaAbiertaId === $duvida->id; @endphp
                <button wire:click="abrirDuvida({{ $duvida->id }})"
                    class="cursor-pointer w-full text-left rounded-2xl border p-4 transition group relative overflow-hidden
                        {{ $isAtiva
                            ? 'bg-gradient-to-br from-blue-500 to-indigo-600 dark:from-blue-500/15 dark:to-indigo-600/10 border-indigo-400 dark:border-indigo-500 shadow-sm shadow-indigo-500/20'
                            : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-violet-300 dark:hover:border-violet-600 hover:shadow-md hover:shadow-indigo-500/20' }}">
                    @if($isAtiva)
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-l-2xl"></div>
                    @endif
                    <div class="flex items-start gap-3 {{ $isAtiva ? 'pl-1' : '' }}">
                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-white text-xs lato-bold shadow-sm
                            {{ $isAtiva ? 'bg-gradient-to-br from-blue-500 to-indigo-600 shadow-indigo-500/30' : 'bg-gradient-to-br from-slate-400 to-slate-500 group-hover:from-blue-500 group-hover:to-indigo-600 transition-all duration-200' }}">
                            {{ strtoupper(substr($duvida->usuario->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            {{-- Linha 1: nome + badge --}}
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $duvida->usuario->name }}</p>
                                <span class="flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full lato-bold shrink-0
                                    {{ $duvida->status === 'respondida'
                                        ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                                        : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $duvida->status === 'respondida' ? 'bg-emerald-500' : 'bg-amber-400' }} {{ $duvida->status !== 'respondida' ? 'animate-pulse' : '' }}"></span>
                                    {{ $duvida->status === 'respondida' ? 'Respondida' : 'Aberta' }}
                                </span>
                            </div>
                            {{-- Curso --}}
                            <p class="text-[11px] text-indigo-500 dark:text-indigo-400 lato-bold truncate mb-1.5 flex items-center gap-1">
                                <x-lucide-graduation-cap class="w-3 h-3 shrink-0" />
                                {{ Str::limit($duvida->treinamento->titulo, 38) }}
                            </p>
                            {{-- Pergunta --}}
                            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular line-clamp-2 leading-relaxed">{{ $duvida->pergunta }}</p>
                            {{-- Tempo + respostas --}}
                            <div class="flex items-center justify-between mt-2">
                                <p class="text-[10px] text-slate-300 dark:text-slate-600 lato-regular flex items-center gap-1">
                                    <x-lucide-clock class="w-3 h-3" />
                                    {{ $duvida->created_at->diffForHumans() }}
                                </p>
                                @if($duvida->respostas->count() > 0)
                                    <span class="flex items-center gap-1 text-[10px] text-emerald-500 dark:text-emerald-400 lato-bold">
                                        <x-lucide-message-circle class="w-3 h-3" /> {{ $duvida->respostas->count() }} resp.
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </button>
                @empty
                <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center mb-3">
                        <x-lucide-message-circle-question class="w-7 h-7 text-violet-300 dark:text-indigo-600" />
                    </div>
                    <p class="text-sm lato-bold text-slate-500 dark:text-slate-400">Nenhuma dúvida encontrada</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular mt-1">
                        {{ $duvidaStatusFiltro !== 'todas' ? 'Tente outro filtro' : 'Os alunos ainda não enviaram dúvidas' }}
                    </p>
                </div>
                @endforelse
                @if($this->duvidas->hasPages())
                    <div class="mt-1">{{ $this->duvidas->links() }}</div>
                @endif
            </div>

            {{-- ── Painel de resposta ── --}}
            <div class="lg:col-span-3">
                @if($this->duvidaAberta)
                @php $duvida = $this->duvidaAberta; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden sticky top-4">

                    {{-- Header do painel --}}
                    <div class="relative px-5 py-4 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-r from-blue-500 to-indigo-600/50 dark:from-blue-500/10 dark:to-indigo-600/5 overflow-hidden">
                        <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-purple-100/40 dark:from-purple-900/10 to-transparent pointer-events-none"></div>
                        <div class="relative flex items-start justify-between gap-3">
                            <div class="min-w-0 flex items-start gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0 shadow-sm shadow-indigo-500/20">
                                    <x-lucide-graduation-cap class="w-4.5 h-4.5 text-white" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm lato-black text-slate-800 dark:text-white truncate">{{ $duvida->treinamento->titulo }}</p>
                                    @if($duvida->aula)
                                        <p class="text-[11px] text-slate-400 lato-regular mt-0.5 flex items-center gap-1">
                                            <x-lucide-video class="w-3 h-3 shrink-0" />
                                            {{ $duvida->aula->titulo }}
                                        </p>
                                    @else
                                        <p class="text-[11px] text-indigo-500 dark:text-indigo-400 lato-regular mt-0.5">Dúvida geral do curso</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="flex items-center gap-1 text-[10px] px-2.5 py-1 rounded-full lato-bold
                                    {{ $duvida->status === 'respondida'
                                        ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                                        : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $duvida->status === 'respondida' ? 'bg-emerald-500' : 'bg-amber-400 animate-pulse' }}"></span>
                                    {{ $duvida->status === 'respondida' ? 'Respondida' : 'Aberta' }}
                                </span>
                                <button wire:click="fecharDuvida"
                                    class="cursor-pointer w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-white dark:hover:bg-slate-700 transition">
                                    <x-lucide-x class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">

                        {{-- Pergunta --}}
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-700 flex items-center justify-center shrink-0 text-white text-xs lato-bold shadow-sm shadow-indigo-500/20">
                                {{ strtoupper(substr($duvida->usuario->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 bg-slate-50 dark:bg-slate-900/50 rounded-2xl rounded-tl-sm px-4 py-3">
                                <p class="text-[11px] lato-bold text-slate-500 dark:text-slate-400 mb-1">{{ $duvida->usuario->name }}
                                    <span class="lato-regular text-slate-300 dark:text-slate-600 ml-1">· {{ $duvida->created_at->format('d/m/Y') }}</span>
                                </p>
                                <p class="text-sm text-slate-700 dark:text-slate-200 lato-regular leading-relaxed">{{ $duvida->pergunta }}</p>
                            </div>
                        </div>

                        {{-- Respostas --}}
                        @foreach($duvida->respostas as $resp)
                        <div class="flex items-start gap-3 pl-4">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0 text-white text-xs lato-bold">
                                {{ strtoupper(substr($resp->usuario->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 bg-violet-50 dark:bg-violet-900/10 rounded-2xl rounded-tl-sm px-4 py-3 border border-violet-100 dark:border-violet-800/30">
                                <p class="text-[11px] lato-bold text-indigo-600 dark:text-indigo-400 mb-1">{{ $resp->usuario->name }}
                                    <span class="lato-regular text-slate-400 ml-1">· {{ $resp->created_at->diffForHumans() }}</span>
                                </p>
                                <p class="text-sm text-slate-600 dark:text-slate-300 lato-regular leading-relaxed">{{ $resp->resposta }}</p>
                            </div>
                        </div>
                        @endforeach

                        {{-- Form de resposta --}}
                        @if($duvida->status !== 'respondida' || $duvida->respostas->isNotEmpty())
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-700">
                            <p class="text-[11px] lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                                {{ $duvida->respostas->isNotEmpty() ? 'Adicionar resposta' : 'Responder dúvida' }}
                            </p>
                            <textarea wire:model="respostaDuvida" rows="3"
                                placeholder="Escreva sua resposta aqui…"
                                class="w-full text-sm px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200
                                       focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                       lato-regular resize-none transition"></textarea>
                            @error('respostaDuvida')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                            <div class="mt-3 flex justify-end">
                                <button wire:click="responderDuvida"
                                    class="cursor-pointer flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600
                                           hover:from-blue-600 hover:to-indigo-700 text-white text-xs lato-bold px-4 py-2.5
                                           rounded-xl transition shadow-sm shadow-indigo-500/20">
                                    <x-lucide-send class="w-3.5 h-3.5" /> Enviar resposta
                                </button>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center gap-2 p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800/30">
                            <x-lucide-circle-check-big class="w-4 h-4 text-emerald-500 shrink-0" />
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 lato-bold">Dúvida respondida</p>
                        </div>
                        @endif

                    </div>
                </div>

                @else
                {{-- Placeholder --}}
                <div class="flex flex-col items-center justify-center h-full min-h-[400px]
                            bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 text-center p-10 relative overflow-hidden">
                    {{-- Decorative background --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/50 to-indigo-600/30 dark:from-blue-500/5 dark:to-indigo-600/5 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 dark:from-blue-500/30 dark:to-indigo-600/20 flex items-center justify-center mb-5 mx-auto border border-violet-200/50 dark:border-violet-700/30">
                            <x-lucide-message-circle class="w-10 h-10 text-indigo-400 dark:text-indigo-500" />
                        </div>
                        <p class="text-base lato-black text-slate-600 dark:text-slate-300 mb-2">Selecione uma dúvida</p>
                        <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular max-w-xs">Clique em uma dúvida à esquerda para visualizar e responder</p>
                        @if($cntAbertas > 0)
                            <div class="mt-5 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/30 text-xs text-amber-700 dark:text-amber-400 lato-bold">
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                {{ $cntAbertas }} dúvida{{ $cntAbertas > 1 ? 's' : '' }} aguardando resposta
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

        </div>
        @endif
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAIS
    ══════════════════════════════════════════════════════════════════ --}}

    {{-- Modal: Criar/Editar Curso --}}
    @if($modalCurso)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
         x-data="{ open: false, close() { this.open = false; setTimeout(() => $wire.set('modalCurso', false), 300); } }"
         x-init="requestAnimationFrame(() => open = true)"
         @click.self="close()">
        <div class="bg-white dark:bg-slate-800 shadow-2xl w-full sm:max-w-2xl sm:mx-4 flex flex-col
                    rounded-t-3xl sm:rounded-2xl
                    border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                    transition-transform duration-300 ease-out will-change-transform"
             :class="open ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
             style="max-height: 92dvh;"
             @click.stop>

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm shadow-violet-200 dark:shadow-indigo-500/20">
                        <x-lucide-graduation-cap class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100">
                            {{ $modalCursoModo === 'criar' ? 'Novo Curso' : 'Editar Curso' }}
                        </h3>
                        <p class="text-[10px] text-slate-400 lato-regular">{{ $modalCursoModo === 'criar' ? 'Preencha os dados do curso' : 'Atualize as informações' }}</p>
                    </div>
                </div>
                <button @click="close()"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1 px-5 py-5 space-y-5">

                {{-- Capa / Thumbnail --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Capa do curso</label>
                    <div x-data="{ dragging: false }"
                         @dragover.prevent="dragging = true"
                         @dragleave.prevent="dragging = false"
                         @drop.prevent="dragging = false; $wire.upload('cursoCapa', $event.dataTransfer.files[0])"
                         class="relative rounded-2xl border-2 border-dashed transition overflow-hidden"
                         :class="dragging ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/10' : 'border-slate-200 dark:border-slate-700'">

                        {{-- Preview da capa nova (ainda não salva) --}}
                        @if($cursoCapa)
                            <div class="relative group">
                                <img src="{{ $cursoCapa->temporaryUrl() }}" alt="Preview" class="w-full h-36 object-cover" />
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                    <label class="cursor-pointer flex items-center gap-1.5 bg-white/90 text-slate-700 text-xs lato-bold px-3 py-1.5 rounded-lg">
                                        <x-lucide-image class="w-3.5 h-3.5" /> Trocar
                                        <input type="file" wire:model="cursoCapa" accept="image/*" class="sr-only" />
                                    </label>
                                    <button type="button" wire:click="$set('cursoCapa', null)"
                                        class="flex items-center gap-1.5 bg-red-500/90 text-white text-xs lato-bold px-3 py-1.5 rounded-lg">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5" /> Remover
                                    </button>
                                </div>
                            </div>

                        {{-- Preview da capa existente (edição) --}}
                        @elseif($cursoCapaAtual)
                            <div class="relative group">
                                <img src="{{ asset('storage/'.$cursoCapaAtual) }}" alt="Capa atual" class="w-full h-36 object-cover" />
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                    <label class="cursor-pointer flex items-center gap-1.5 bg-white/90 text-slate-700 text-xs lato-bold px-3 py-1.5 rounded-lg">
                                        <x-lucide-image class="w-3.5 h-3.5" /> Trocar
                                        <input type="file" wire:model="cursoCapa" accept="image/*" class="sr-only" />
                                    </label>
                                    <button type="button" wire:click="removerCapa"
                                        class="flex items-center gap-1.5 bg-red-500/90 text-white text-xs lato-bold px-3 py-1.5 rounded-lg">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5" /> Remover
                                    </button>
                                </div>
                            </div>

                        {{-- Área de upload vazia --}}
                        @else
                            <label class="cursor-pointer flex flex-col items-center justify-center gap-2 py-7 px-4 text-center">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                    <x-lucide-image-plus class="w-5 h-5 text-slate-400" />
                                </div>
                                <div>
                                    <p class="text-sm lato-bold text-slate-600 dark:text-slate-300">Clique para selecionar</p>
                                    <p class="text-xs text-slate-400 lato-regular mt-0.5">ou arraste uma imagem aqui — JPG, PNG, WebP (máx. 2 MB)</p>
                                </div>
                                <input type="file" wire:model="cursoCapa" accept="image/*" class="sr-only" />
                            </label>
                        @endif

                        {{-- Loading indicator --}}
                        <div wire:loading wire:target="cursoCapa"
                             class="absolute inset-0 bg-white/70 dark:bg-slate-800/70 flex items-center justify-center gap-2 text-sm text-indigo-600 lato-bold">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            Enviando…
                        </div>
                    </div>
                    @error('cursoCapa') <p class="text-xs text-red-500 mt-1.5 lato-regular flex items-center gap-1"><x-lucide-alert-circle class="w-3 h-3" /> {{ $message }}</p> @enderror
                </div>

                {{-- Título + Descrição --}}
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Título *</label>
                        <input wire:model="cursoTitulo" type="text" placeholder="Ex: Liderança Situacional"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400" />
                        @error('cursoTitulo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Descrição</label>
                        <textarea wire:model="cursoDescricao" rows="2" placeholder="Descreva o objetivo do curso…"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400 resize-none"></textarea>
                    </div>
                </div>

                {{-- Divider com label --}}
                <div class="flex items-center gap-3">
                    <div class="flex-1 h-px bg-slate-100 dark:bg-slate-700"></div>
                    <span class="text-[10px] lato-bold text-slate-400 uppercase tracking-wider">Configurações</span>
                    <div class="flex-1 h-px bg-slate-100 dark:bg-slate-700"></div>
                </div>

                {{-- Grid de campos --}}
                <div class="grid grid-cols-2 gap-3">

                    {{-- Tipo --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Tipo *</label>
                        <button @click="open = !open" type="button"
                            class="cursor-pointer w-full flex items-center justify-between px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular transition hover:border-indigo-400/50">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full shrink-0 {{ $cursoTipo === 'interno' ? 'bg-blue-400' : 'bg-orange-400' }}"></span>
                                {{ $cursoTipo === 'interno' ? 'Interno' : 'Externo' }}
                            </span>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 transition-transform" ::class="{'rotate-180':open}" />
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute top-full left-0 mt-1 z-50 w-full bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg py-1" style="display:none">
                            @foreach([['interno','Interno','bg-blue-400'],['externo','Externo','bg-orange-400']] as [$v,$l,$d])
                            <button type="button" @click="$wire.set('cursoTipo','{{ $v }}'); open=false"
                                class="cursor-pointer w-full flex items-center gap-2.5 px-3 py-2 text-sm lato-regular transition {{ $cursoTipo===$v ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <span class="w-2 h-2 rounded-full {{ $d }} shrink-0"></span> {{ $l }}
                                @if($cursoTipo===$v) <x-lucide-check class="w-3 h-3 ml-auto text-indigo-500" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Nível --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Nível *</label>
                        @php $nivelMap = ['basico'=>['Básico','bg-emerald-400'],'intermediario'=>['Intermediário','bg-amber-400'],'avancado'=>['Avançado','bg-rose-400']]; @endphp
                        <button @click="open = !open" type="button"
                            class="cursor-pointer w-full flex items-center justify-between px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 lato-regular transition hover:border-indigo-400/50">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full shrink-0 {{ $nivelMap[$cursoNivel][1] ?? 'bg-slate-400' }}"></span>
                                {{ $nivelMap[$cursoNivel][0] ?? $cursoNivel }}
                            </span>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 transition-transform" ::class="{'rotate-180':open}" />
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute top-full left-0 mt-1 z-50 w-full bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg py-1" style="display:none">
                            @foreach([['basico','Básico','bg-emerald-400'],['intermediario','Intermediário','bg-amber-400'],['avancado','Avançado','bg-rose-400']] as [$v,$l,$d])
                            <button type="button" @click="$wire.set('cursoNivel','{{ $v }}'); open=false"
                                class="cursor-pointer w-full flex items-center gap-2.5 px-3 py-2 text-sm lato-regular transition {{ $cursoNivel===$v ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <span class="w-2 h-2 rounded-full {{ $d }} shrink-0"></span> {{ $l }}
                                @if($cursoNivel===$v) <x-lucide-check class="w-3 h-3 ml-auto text-indigo-500" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Status --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Status *</label>
                        @php $statusMap = ['rascunho'=>['Rascunho','bg-amber-400'],'ativo'=>['Ativo','bg-emerald-400'],'arquivado'=>['Arquivado','bg-slate-400']]; @endphp
                        <button @click="open = !open" type="button"
                            class="cursor-pointer w-full flex items-center justify-between px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 lato-regular transition hover:border-indigo-400/50">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full shrink-0 {{ $statusMap[$cursoStatus][1] ?? 'bg-slate-400' }}"></span>
                                {{ $statusMap[$cursoStatus][0] ?? $cursoStatus }}
                            </span>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 transition-transform" ::class="{'rotate-180':open}" />
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute top-full left-0 mt-1 z-50 w-full bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg py-1" style="display:none">
                            @foreach([['rascunho','Rascunho','bg-amber-400'],['ativo','Ativo','bg-emerald-400'],['arquivado','Arquivado','bg-slate-400']] as [$v,$l,$d])
                            <button type="button" @click="$wire.set('cursoStatus','{{ $v }}'); open=false"
                                class="cursor-pointer w-full flex items-center gap-2.5 px-3 py-2 text-sm lato-regular transition {{ $cursoStatus===$v ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <span class="w-2 h-2 rounded-full {{ $d }} shrink-0"></span> {{ $l }}
                                @if($cursoStatus===$v) <x-lucide-check class="w-3 h-3 ml-auto text-indigo-500" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Carga horária --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Carga horária (min) *</label>
                        <div class="relative">
                            <x-lucide-clock class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                            <input wire:model="cursoCargaHoraria" type="number" min="0"
                                class="w-full pl-8 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular" />
                        </div>
                    </div>

                    {{-- Categoria --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Categoria</label>
                        <div class="relative">
                            <x-lucide-tag class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                            <input wire:model="cursoCategoria" type="text" placeholder="Ex: Liderança, Técnico…"
                                class="w-full pl-8 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400" />
                        </div>
                    </div>

                    {{-- Instrutor --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Instrutor</label>
                        <div class="relative">
                            <x-lucide-user class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                            <input wire:model="cursoInstrutor" type="text" placeholder="Nome do instrutor"
                                class="w-full pl-8 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400" />
                        </div>
                    </div>

                    {{-- Datas --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Data início</label>
                        <input wire:model="cursoDataInicio" type="date"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Data fim</label>
                        <input wire:model="cursoDataFim" type="date"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular" />
                    </div>
                </div>

                {{-- Divider --}}
                <div class="flex items-center gap-3">
                    <div class="flex-1 h-px bg-slate-100 dark:bg-slate-700"></div>
                    <span class="text-[10px] lato-bold text-slate-400 uppercase tracking-wider">Opções</span>
                    <div class="flex-1 h-px bg-slate-100 dark:bg-slate-700"></div>
                </div>

                {{-- Toggles --}}
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/30 cursor-pointer hover:border-violet-300 dark:hover:border-violet-700 transition">
                        <div class="flex items-center gap-2.5">
                            <x-lucide-users class="w-3.5 h-3.5 text-indigo-500 shrink-0" />
                            <div>
                                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">Inscrições abertas</p>
                                <p class="text-[10px] text-slate-400 lato-regular">Permite novas inscrições</p>
                            </div>
                        </div>
                        <div class="relative shrink-0">
                            <input type="checkbox" wire:model="cursoInscricaoAberta" class="sr-only peer" />
                            <div class="w-9 h-5 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-indigo-600 transition-colors"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                        </div>
                    </label>
                    <label class="flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/30 cursor-pointer hover:border-violet-300 dark:hover:border-violet-700 transition">
                        <div class="flex items-center gap-2.5">
                            <x-lucide-award class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                            <div>
                                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">Emitir certificado</p>
                                <p class="text-[10px] text-slate-400 lato-regular">Ao concluir o curso</p>
                            </div>
                        </div>
                        <div class="relative shrink-0">
                            <input type="checkbox" wire:model="cursoCertificadoHabilitado" class="sr-only peer" />
                            <div class="w-9 h-5 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-indigo-600 transition-colors"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                        </div>
                    </label>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0
                        pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button @click="close()"
                    class="cursor-pointer flex-1 sm:flex-none px-4 py-2.5 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700
                           text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button wire:click="salvarCurso"
                    class="cursor-pointer flex-1 px-5 py-2.5 text-xs lato-bold rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600
                           text-white hover:from-blue-600 hover:to-indigo-700 disabled:opacity-50 transition shadow-sm"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarCurso" class="flex items-center gap-1.5">
                        {{ $modalCursoModo === 'criar' ? 'Criar curso' : 'Salvar alterações' }}
                    </span>
                    <span wire:loading wire:target="salvarCurso" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal: Criar/Editar Aula --}}
    @if($modalAula)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
         x-data="{ open: false, close() { this.open = false; setTimeout(() => $wire.set('modalAula', false), 300); } }"
         x-init="requestAnimationFrame(() => open = true)"
         @click.self="close()">
        <div class="bg-white dark:bg-slate-800 shadow-2xl w-full sm:max-w-lg sm:mx-4 flex flex-col
                    rounded-t-3xl sm:rounded-2xl
                    border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                    transition-transform duration-300 ease-out will-change-transform"
             :class="open ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
             style="max-height: 92dvh;"
             @click.stop>

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <x-lucide-video class="w-4 h-4 text-indigo-500" />
                    {{ $modalAulaModo === 'criar' ? 'Adicionar Aula' : 'Editar Aula' }}
                </h3>
                <button @click="close()"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1 px-5 py-5 space-y-4">
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Título *</label>
                    <input wire:model="aulaTitulo" type="text"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400" />
                    @error('aulaTitulo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descrição</label>
                    <textarea wire:model="aulaDescricao" rows="2"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">URL do vídeo (YouTube / Vimeo)</label>
                    <input wire:model="aulaVideoUrl" type="url" placeholder="https://www.youtube.com/watch?v=…"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400" />
                    @error('aulaVideoUrl') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Duração (minutos) *</label>
                        <input wire:model="aulaDuracao" type="number" min="1"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Ordem</label>
                        <input wire:model="aulaOrdem" type="number" min="0"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular" />
                    </div>
                </div>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" wire:model="aulaObrigatoria" class="accent-indigo-500 w-4 h-4 rounded" />
                    <span class="text-sm lato-regular text-slate-700 dark:text-slate-300">Aula obrigatória para conclusão</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" wire:model="aulaExigirVideo" class="accent-indigo-500 w-4 h-4 rounded" />
                    <span class="text-sm lato-regular text-slate-700 dark:text-slate-300">Exigir assistir vídeo completo (100%) para concluir</span>
                </label>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0
                        pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button @click="close()"
                    class="cursor-pointer flex-1 sm:flex-none px-4 py-2.5 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700
                           text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Cancelar</button>
                <button wire:click="salvarAula"
                    class="cursor-pointer flex-1 px-5 py-2.5 text-xs lato-bold rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600
                           text-white hover:from-blue-600 hover:to-indigo-700 disabled:opacity-50 transition shadow-sm"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarAula" class="flex items-center gap-1.5">
                        {{ $modalAulaModo === 'criar' ? 'Adicionar aula' : 'Salvar' }}
                    </span>
                    <span wire:loading wire:target="salvarAula" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal: Criar/Editar Teste --}}
    @if($modalTeste)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
         x-data="{ open: false, close() { this.open = false; setTimeout(() => $wire.set('modalTeste', false), 300); } }"
         x-init="requestAnimationFrame(() => open = true)"
         @click.self="close()">
        <div class="bg-white dark:bg-slate-900 shadow-2xl w-full sm:max-w-3xl sm:mx-4 flex flex-col
                    rounded-t-3xl sm:rounded-2xl
                    border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                    transition-transform duration-300 ease-out will-change-transform"
             :class="open ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
             style="max-height: 92dvh;"
             @click.stop>

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-t-3xl">
                <div class="w-10 h-1 rounded-full bg-white/40"></div>
            </div>

            {{-- Header com gradiente --}}
            <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-5 shrink-0 overflow-hidden sm:rounded-t-2xl">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>
                <div class="relative flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 flex items-center justify-center shrink-0">
                            <x-lucide-clipboard-check class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="text-sm lato-black text-white">Teste Avaliativo</h3>
                            <p class="text-[11px] text-white/60 lato-regular mt-0.5">Configure as questões e regras de aprovação</p>
                        </div>
                    </div>
                    <button @click="close()"
                        class="cursor-pointer w-8 h-8 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition shrink-0">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1">

                {{-- Seção: Configurações --}}
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-[10px] lato-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Configurações</p>

                    {{-- Título --}}
                    <div class="mb-4">
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                            Título do teste <span class="text-indigo-500">*</span>
                        </label>
                        <input wire:model="testeTitulo" type="text" placeholder="Ex: Avaliação Final"
                            class="w-full px-3.5 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400 transition" />
                        @error('testeTitulo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nota + Tentativas --}}
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                                Nota mínima (%) <span class="text-indigo-500">*</span>
                            </label>
                            <div class="relative">
                                <x-lucide-bar-chart-2 class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                <input wire:model="testeNotaMinima" type="number" min="0" max="100"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                                Tentativas máximas <span class="text-indigo-500">*</span>
                            </label>
                            <div class="relative">
                                <x-lucide-refresh-cw class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                <input wire:model="testeTentativasMaximas" type="number" min="1" max="10"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                            </div>
                        </div>
                    </div>

                    {{-- Embaralhar toggle --}}
                    <label for="testeEmbaralhar"
                        class="flex items-center justify-between gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 cursor-pointer hover:border-violet-300 dark:hover:border-violet-700 transition mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center shrink-0">
                                <x-lucide-shuffle class="w-4 h-4 text-indigo-500" />
                            </div>
                            <div>
                                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">Embaralhar questões</p>
                                <p class="text-[10px] text-slate-400 lato-regular">Ordem aleatória a cada tentativa</p>
                            </div>
                        </div>
                        <div class="relative shrink-0">
                            <input wire:model="testeEmbaralhar" type="checkbox" id="testeEmbaralhar" class="sr-only peer" />
                            <div class="w-10 h-5 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-indigo-600 transition-colors"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>

                    {{-- Descrição --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descrição / Instruções</label>
                        <textarea wire:model="testeDescricao" rows="2"
                            class="w-full px-3.5 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400 resize-none transition"
                            placeholder="Instruções visíveis ao colaborador antes de iniciar o teste…"></textarea>
                    </div>
                </div>

                {{-- Seção: Questões --}}
                <div class="px-6 py-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <p class="text-[10px] lato-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Questões</p>
                            <span class="w-5 h-5 flex items-center justify-center text-[10px] lato-black rounded-full bg-violet-100 dark:bg-violet-900/40 text-indigo-600 dark:text-indigo-400">
                                {{ count($questoes) }}
                            </span>
                        </div>
                        <button wire:click="adicionarQuestao" type="button"
                            class="cursor-pointer flex items-center gap-1.5 px-3.5 py-2 text-xs lato-bold text-violet-700 dark:text-violet-300 bg-violet-50 dark:bg-violet-900/30 hover:bg-violet-100 dark:hover:bg-violet-900/50 rounded-xl transition border border-violet-200/50 dark:border-violet-700/30">
                            <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar questão
                        </button>
                    </div>

                    @if (empty($questoes))
                    <div class="flex flex-col items-center justify-center py-10 text-center bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                        <div class="w-12 h-12 rounded-2xl bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center mb-3">
                            <x-lucide-list-checks class="w-6 h-6 text-violet-300 dark:text-indigo-600" />
                        </div>
                        <p class="text-sm lato-bold text-slate-400">Nenhuma questão ainda</p>
                        <p class="text-xs text-slate-400 lato-regular mt-1">Clique em "Adicionar questão" para começar</p>
                    </div>
                    @endif

                    <div class="space-y-4">
                    @foreach ($questoes as $qi => $questao)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-800/50 shadow-sm">
                        {{-- Questão header --}}
                        <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-slate-50 to-slate-50/50 dark:from-slate-800 dark:to-slate-800/80 border-b border-slate-100 dark:border-slate-700">
                            <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-[10px] lato-black shrink-0 shadow-sm shadow-indigo-500/20">
                                {{ $qi + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <input wire:model="questoes.{{ $qi }}.enunciado" type="text"
                                    class="w-full text-sm border-0 bg-transparent text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-0 lato-regular placeholder-slate-400"
                                    placeholder="Escreva o enunciado da questão…" />
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <select wire:model="questoes.{{ $qi }}.tipo"
                                    class="text-xs border border-slate-200 dark:border-slate-600 rounded-lg px-2.5 py-1.5 bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-400/30 lato-regular">
                                    <option value="multipla_escolha">Múltipla escolha</option>
                                    <option value="verdadeiro_falso">Verdadeiro/Falso</option>
                                </select>
                                <div class="relative" title="Pontos">
                                    <input wire:model="questoes.{{ $qi }}.pontos" type="number" min="1" max="10"
                                        class="w-14 text-xs text-center border border-slate-200 dark:border-slate-600 rounded-lg px-2 py-1.5 bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 focus:outline-none lato-bold" />
                                </div>
                                <button wire:click="removerQuestao({{ $qi }})" type="button"
                                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </div>

                        {{-- Opções --}}
                        <div class="px-4 py-3 space-y-2 bg-white dark:bg-slate-800/30">
                            @foreach ($questao['opcoes'] as $oi => $opcao)
                            <div class="flex items-center gap-2.5 group/opt">
                                <button wire:click="marcarCorreta({{ $qi }}, {{ $oi }})" type="button"
                                    title="{{ $opcao['correta'] ? 'Resposta correta' : 'Marcar como correta' }}"
                                    class="cursor-pointer shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition
                                        {{ $opcao['correta']
                                            ? 'border-emerald-500 bg-emerald-500 shadow-sm shadow-emerald-500/30'
                                            : 'border-slate-300 dark:border-slate-600 hover:border-emerald-400' }}">
                                    @if($opcao['correta'])
                                        <x-lucide-check class="w-3 h-3 text-white" />
                                    @endif
                                </button>
                                <input wire:model="questoes.{{ $qi }}.opcoes.{{ $oi }}.texto" type="text"
                                    class="flex-1 text-xs border rounded-xl px-3 py-2 focus:outline-none focus:ring-2 lato-regular placeholder-slate-400 transition
                                        {{ $opcao['correta']
                                            ? 'border-emerald-200 dark:border-emerald-700/50 bg-emerald-50/50 dark:bg-emerald-900/10 text-emerald-800 dark:text-emerald-300 focus:ring-emerald-400/20 focus:border-emerald-400'
                                            : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-indigo-400/30 focus:border-indigo-400' }}"
                                    placeholder="Opção {{ $oi + 1 }}…" />
                                @if (count($questao['opcoes']) > 2)
                                <button wire:click="removerOpcao({{ $qi }}, {{ $oi }})" type="button"
                                    class="cursor-pointer p-1 rounded-lg text-slate-300 dark:text-slate-600 hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition opacity-0 group-hover/opt:opacity-100 shrink-0">
                                    <x-lucide-x class="w-3.5 h-3.5" />
                                </button>
                                @else
                                <div class="w-6 shrink-0"></div>
                                @endif
                            </div>
                            @endforeach

                            @if (count($questao['opcoes']) < 6)
                            <button wire:click="adicionarOpcao({{ $qi }})" type="button"
                                class="cursor-pointer mt-1 flex items-center gap-1.5 text-[11px] text-indigo-500 dark:text-indigo-400 hover:text-violet-700 dark:hover:text-violet-300 lato-bold transition">
                                <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar opção
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-6 py-4 border-t border-slate-100 dark:border-slate-800 shrink-0 bg-slate-50/50 dark:bg-slate-900/50
                        pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button @click="close()" type="button"
                    class="cursor-pointer flex-1 sm:flex-none px-4 py-2.5 text-sm lato-bold text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition">
                    Cancelar
                </button>
                <button wire:click="salvarTeste" type="button"
                    class="cursor-pointer flex-1 flex items-center justify-center gap-2 px-5 py-2.5 text-sm lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 rounded-xl transition shadow-md shadow-indigo-500/25 disabled:opacity-60"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarTeste" class="flex items-center gap-1.5">
                        <x-lucide-save class="w-3.5 h-3.5" /> Salvar teste
                    </span>
                    <span wire:loading wire:target="salvarTeste" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal: Inscrição Obrigatória --}}
    @if($modalObrigatorio)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
         x-data="{ open: false, close() { this.open = false; setTimeout(() => $wire.set('modalObrigatorio', false), 300); } }"
         x-init="requestAnimationFrame(() => open = true)"
         @click.self="close()">
        <div class="bg-white dark:bg-slate-800 shadow-2xl w-full sm:max-w-md sm:mx-4 flex flex-col
                    rounded-t-3xl sm:rounded-2xl
                    border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                    transition-transform duration-300 ease-out will-change-transform"
             :class="open ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
             @click.stop>

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <x-lucide-shield-check class="w-4 h-4 text-amber-500" />
                    Inscrição Obrigatória
                </h3>
                <button @click="close()"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
            {{-- Body --}}
            <div class="px-5 py-5 space-y-4">
                {{-- Tipo --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Atribuir para</label>
                    <div class="flex gap-2">
                        <button wire:click="$set('obrigatorioTipo','usuario')" type="button"
                            class="cursor-pointer flex-1 flex items-center justify-center gap-2 py-2 text-xs lato-bold rounded-xl border transition
                                {{ $obrigatorioTipo === 'usuario' ? 'border-indigo-400 bg-violet-50 dark:bg-violet-900/30 text-indigo-600 dark:text-violet-300' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-slate-300' }}">
                            <x-lucide-user class="w-3.5 h-3.5" />
                            Colaborador
                        </button>
                        <button wire:click="$set('obrigatorioTipo','departamento')" type="button"
                            class="cursor-pointer flex-1 flex items-center justify-center gap-2 py-2 text-xs lato-bold rounded-xl border transition
                                {{ $obrigatorioTipo === 'departamento' ? 'border-indigo-400 bg-violet-50 dark:bg-violet-900/30 text-indigo-600 dark:text-violet-300' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-slate-300' }}">
                            <x-lucide-users class="w-3.5 h-3.5" />
                            Departamento
                        </button>
                    </div>
                </div>

                {{-- Seletor usuario --}}
                @if ($obrigatorioTipo === 'usuario')
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Colaborador *</label>
                    <select wire:model="obrigatorioUserId"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular">
                        <option value="">Selecione…</option>
                        @foreach ($this->usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Departamento *</label>
                    <select wire:model="obrigatorioDeptId"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular">
                        <option value="">Selecione…</option>
                        @foreach ($this->departamentos as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Prazo --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Prazo <span class="font-normal text-slate-400">(opcional)</span></label>
                    <input type="date" wire:model="obrigatorioDeadline"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                               bg-white dark:bg-slate-700/60 text-slate-700 dark:text-slate-200
                               focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular" />
                </div>

            </div>
            {{-- /body --}}

            {{-- Footer --}}
            <div class="flex gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0">
                <button wire:click="$set('modalObrigatorio', false)"
                    class="cursor-pointer px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 text-slate-500
                           rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition lato-regular">
                    Cancelar
                </button>
                <button wire:click="salvarObrigatorio"
                    class="cursor-pointer flex-1 py-2.5 text-sm lato-bold rounded-xl
                           bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                           text-white shadow-sm shadow-indigo-200/60 transition disabled:opacity-60"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarObrigatorio" class="flex items-center gap-1.5">
                        Confirmar
                    </span>
                    <span wire:loading wire:target="salvarObrigatorio" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
@endif
{{-- /modalObrigatorio --}}
