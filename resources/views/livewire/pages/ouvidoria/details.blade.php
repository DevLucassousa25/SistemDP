<div class="min-h-screen bg-slate-50 dark:bg-gray-900">
    @php
        $categoriaMap = [
            'sugestao'   => ['label' => 'Sugestão',   'icon' => 'lightbulb',    'bg' => 'bg-blue-50 dark:bg-blue-900/30',    'text' => 'text-blue-600 dark:text-blue-400',    'ring' => 'ring-blue-200 dark:ring-blue-800'],
            'reclamacao' => ['label' => 'Reclamação', 'icon' => 'alert-circle', 'bg' => 'bg-orange-50 dark:bg-orange-900/30', 'text' => 'text-orange-600 dark:text-orange-400', 'ring' => 'ring-orange-200 dark:ring-orange-800'],
            'denuncia'   => ['label' => 'Denúncia',   'icon' => 'shield-alert', 'bg' => 'bg-red-50 dark:bg-red-900/30',      'text' => 'text-red-600 dark:text-red-400',      'ring' => 'ring-red-200 dark:ring-red-800'],
            'elogio'     => ['label' => 'Elogio',     'icon' => 'thumbs-up',   'bg' => 'bg-emerald-50 dark:bg-emerald-900/30','text' => 'text-emerald-600 dark:text-emerald-400','ring' => 'ring-emerald-200 dark:ring-emerald-800'],
        ];
        $statusMap = [
            'em_analise'   => ['label' => 'Em Análise',   'bg' => 'bg-amber-50 dark:bg-amber-900/30',   'text' => 'text-amber-600 dark:text-amber-400',   'dot' => 'bg-amber-400'],
            'em_andamento' => ['label' => 'Em Andamento', 'bg' => 'bg-blue-50 dark:bg-blue-900/30',    'text' => 'text-blue-600 dark:text-blue-400',    'dot' => 'bg-blue-400'],
            'respondido'   => ['label' => 'Respondido',   'bg' => 'bg-purple-50 dark:bg-purple-900/30', 'text' => 'text-purple-600 dark:text-purple-400', 'dot' => 'bg-purple-400'],
            'concluido'    => ['label' => 'Concluído',    'bg' => 'bg-emerald-50 dark:bg-emerald-900/30','text' => 'text-emerald-600 dark:text-emerald-400','dot' => 'bg-emerald-400'],
        ];
        $cat = $categoriaMap[$manifestacao->categoria] ?? ['label' => $manifestacao->categoria, 'icon' => 'file', 'bg' => 'bg-gray-50 dark:bg-gray-800', 'text' => 'text-gray-600 dark:text-gray-400', 'ring' => 'ring-gray-200 dark:ring-gray-700'];
        $st  = $statusMap[$manifestacao->status]       ?? ['label' => $manifestacao->status,    'bg' => 'bg-gray-50 dark:bg-gray-800', 'text' => 'text-gray-600 dark:text-gray-400', 'dot' => 'bg-gray-400'];

        $podeResponder = auth()->user()->isRhOuDp();

        /**
         * Retorna ícone Lucide e cor de acordo com o tipo do arquivo.
         */
        $iconePorTipo = function (string $categoria): array {
            return match ($categoria) {
                'image'       => ['icon' => 'image',     'bg' => 'bg-blue-50 dark:bg-blue-900/30',     'text' => 'text-blue-500 dark:text-blue-400'],
                'pdf'         => ['icon' => 'file-text', 'bg' => 'bg-red-50 dark:bg-red-900/30',      'text' => 'text-red-500 dark:text-red-400'],
                'document'    => ['icon' => 'file-text', 'bg' => 'bg-indigo-50 dark:bg-indigo-900/30', 'text' => 'text-indigo-500 dark:text-indigo-400'],
                'spreadsheet' => ['icon' => 'table-2',   'bg' => 'bg-emerald-50 dark:bg-emerald-900/30','text' => 'text-emerald-500 dark:text-emerald-400'],
                default       => ['icon' => 'file',      'bg' => 'bg-gray-100 dark:bg-gray-700',      'text' => 'text-gray-500 dark:text-gray-400'],
            };
        };
    @endphp

    {{-- ── HEADER ─────────────────────────────────────────────────────────── --}}
    <div class="px-4 sm:px-6 lg:px-8 py-5">
        <div class="max-w-5xl mx-auto">

            {{-- Linha 1: nav de volta + status badge --}}
            <div class="flex items-start justify-between gap-4">

                <div class="flex items-start gap-3 min-w-0">
                    {{-- Botão voltar --}}
                    <a href="{{ route('ouvidoria') }}" wire:navigate
                        class="mt-0.5 flex-shrink-0 p-1.5 rounded-lg text-gray-400 dark:text-gray-500
                               hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <x-lucide-arrow-left class="w-4 h-4" />
                    </a>

                    {{-- Título + categoria --}}
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white font-display tracking-tight leading-tight">
                                {{ $manifestacao->assunto }}
                            </h1>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold ring-1
                                {{ $cat['bg'] }} {{ $cat['text'] }} {{ $cat['ring'] }}">
                                <x-dynamic-component :component="'lucide-' . $cat['icon']" class="w-3 h-3" />
                                {{ $cat['label'] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">{{ $manifestacao->protocolo }}</p>
                    </div>
                </div>

                {{-- Status badge --}}
                <span class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold ring-1
                    {{ $st['bg'] }} {{ $st['text'] }} ring-current/20">
                    <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                    {{ $st['label'] }}
                </span>

            </div>
        </div>
    </div>

    {{-- ── BODY ────────────────────────────────────────────────────────────── --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ══ COLUNA PRINCIPAL (2/3) ══════════════════════════════════ --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Card: Descrição --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3.5">
                        <x-lucide-tag class="w-4 h-4 text-gray-400 dark:text-gray-500" />
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Descrição</h2>
                    </div>
                    <div class="px-5 py-5">
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $manifestacao->descricao }}</p>
                    </div>

                    {{-- Anexos da manifestação original (se houver) --}}
                    @if ($manifestacao->anexos->isNotEmpty())
                        <div class="px-5 pb-5 border-t border-gray-50 dark:border-gray-700/50 pt-4">
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Anexos da solicitação</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($manifestacao->anexos->whereNull('resposta_manifestacao_id') as $anexo)
                                    @php $info = $iconePorTipo($anexo->tipoCategoria()); @endphp
                                    <a href="{{ $anexo->url() }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       title="{{ $anexo->nome_arquivo }}"
                                       class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-100 dark:border-gray-700
                                              hover:border-gray-200 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50
                                              transition group max-w-xs">
                                        <div class="flex-shrink-0 w-7 h-7 rounded-md {{ $info['bg'] }} flex items-center justify-center">
                                            <x-dynamic-component :component="'lucide-' . $info['icon']"
                                                class="w-3.5 h-3.5 {{ $info['text'] }}" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate max-w-[140px]">{{ $anexo->nome_arquivo }}</p>
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $anexo->tamanhoLegivel() }}</p>
                                        </div>
                                        <x-lucide-external-link class="w-3 h-3 text-gray-300 dark:text-gray-600 group-hover:text-gray-500 dark:group-hover:text-gray-400 flex-shrink-0 transition" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Card: Respostas --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100 dark:border-gray-700">
                        <x-lucide-message-square class="w-4 h-4 text-gray-400 dark:text-gray-500" />
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Respostas</h2>
                        @if ($manifestacao->respostas->isNotEmpty())
                            <span class="ml-auto inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold">
                                {{ $manifestacao->respostas->count() }}
                            </span>
                        @endif
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">

                        {{-- Lista de respostas --}}
                        @forelse ($manifestacao->respostas as $resposta)
                            <div class="px-5 py-4 {{ $resposta->is_automatica ? 'bg-amber-50/40 dark:bg-amber-900/10' : '' }}">
                                <div class="flex items-start gap-3">

                                    {{-- Avatar: sistema ou humano --}}
                                    @if ($resposta->is_automatica)
                                        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-amber-100 dark:bg-amber-900/40
                                                    flex items-center justify-center">
                                            <x-lucide-bot class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-emerald-500 flex items-center justify-center text-white text-sm font-bold">
                                            {{ strtoupper(substr($resposta->respondente?->name ?? '?', 0, 2)) }}
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        {{-- Cabeçalho da resposta --}}
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            @if ($resposta->is_automatica)
                                                <span class="text-sm font-semibold text-amber-700 dark:text-amber-400">
                                                    Sistema
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold
                                                             bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400
                                                             ring-1 ring-amber-200 dark:ring-amber-800">
                                                    <x-lucide-bot class="w-2.5 h-2.5" />
                                                    Auto-encerramento
                                                </span>
                                            @else
                                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                    {{ $resposta->respondente?->name ?? 'Usuário' }}
                                                    @if ($resposta->respondente?->isRhOuDp())
                                                        <span class="text-gray-400 dark:text-gray-500 font-normal">– RH</span>
                                                    @endif
                                                </span>
                                                @if ($resposta->respondente?->isRhOuDp())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-800">
                                                        Oficial
                                                    </span>
                                                @endif
                                            @endif
                                        </div>

                                        {{-- Conteúdo --}}
                                        <p class="text-sm {{ $resposta->is_automatica ? 'text-amber-800 dark:text-amber-300' : 'text-gray-700 dark:text-gray-300' }} leading-relaxed">
                                            {{ $resposta->conteudo }}
                                        </p>

                                        {{-- Data --}}
                                        <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                                            {{ $resposta->created_at->format('d/m/Y \à\s H:i') }}
                                        </p>

                                        {{-- ── Anexos da resposta ─────────────────────────── --}}
                                        @if ($resposta->anexos->isNotEmpty())
                                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                                <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1">
                                                    <x-lucide-paperclip class="w-3 h-3" />
                                                    {{ $resposta->anexos->count() }} {{ $resposta->anexos->count() === 1 ? 'anexo' : 'anexos' }}
                                                </p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($resposta->anexos as $anexo)
                                                        @php $info = $iconePorTipo($anexo->tipoCategoria()); @endphp
                                                        <a href="{{ $anexo->url() }}"
                                                           target="_blank"
                                                           rel="noopener noreferrer"
                                                           download="{{ ! $anexo->podeVisualizar() ? $anexo->nome_arquivo : null }}"
                                                           title="{{ $anexo->nome_arquivo }}"
                                                           class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-100 dark:border-gray-700
                                                                  hover:border-emerald-200 dark:hover:border-emerald-800
                                                                  hover:bg-emerald-50/40 dark:hover:bg-emerald-900/20
                                                                  transition group max-w-xs">
                                                            <div class="flex-shrink-0 w-7 h-7 rounded-md {{ $info['bg'] }} flex items-center justify-center">
                                                                <x-dynamic-component :component="'lucide-' . $info['icon']"
                                                                    class="w-3.5 h-3.5 {{ $info['text'] }}" />
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="text-xs font-medium text-gray-700 dark:text-gray-200 truncate max-w-[140px]">{{ $anexo->nome_arquivo }}</p>
                                                                <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $anexo->tamanhoLegivel() }}</p>
                                                            </div>
                                                            @if ($anexo->podeVisualizar())
                                                                <x-lucide-external-link class="w-3 h-3 text-gray-300 dark:text-gray-600 group-hover:text-emerald-400 flex-shrink-0 transition" />
                                                            @else
                                                                <x-lucide-download class="w-3 h-3 text-gray-300 dark:text-gray-600 group-hover:text-emerald-400 flex-shrink-0 transition" />
                                                            @endif
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        {{-- ────────────────────────────────────────────── --}}

                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-8 text-center">
                                <x-lucide-message-circle-dashed class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" />
                                <p class="text-sm text-gray-400 dark:text-gray-500">Nenhuma resposta ainda.</p>
                            </div>
                        @endforelse

                    </div>

                    {{-- ── Formulário de resposta ────────────────────────────── --}}
                    <div class="px-5 py-4 bg-gray-50/50 dark:bg-gray-900/30">

                        @if ($podeResponder)

                            {{-- Textarea --}}
                            <textarea
                                wire:model="novaResposta"
                                rows="3"
                                placeholder="Digite uma resposta ou acompanhamento..."
                                class="w-full px-3.5 py-3 text-sm
                                    bg-white dark:bg-gray-700/60
                                    border border-gray-200 dark:border-gray-600
                                    rounded-xl
                                    placeholder-gray-400 dark:placeholder-gray-500
                                    text-gray-800 dark:text-gray-100
                                    focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500
                                    focus:border-transparent
                                    resize-none transition"
                            ></textarea>

                            @error('novaResposta')
                                <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            {{-- ── Área de upload de arquivos ──────────────────── --}}

                            {{-- Input de arquivo (oculto, ativado pelo botão) --}}
                            <input type="file"
                                   id="anexos-input"
                                   wire:model="anexosUpload"
                                   multiple
                                   accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                                   class="hidden" />

                            {{-- Preview dos arquivos selecionados --}}
                            @if (count($anexosUpload) > 0)
                                <div class="mt-3 space-y-1.5">
                                    <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1">
                                        <x-lucide-paperclip class="w-3 h-3" />
                                        {{ count($anexosUpload) }} {{ count($anexosUpload) === 1 ? 'arquivo selecionado' : 'arquivos selecionados' }}
                                    </p>

                                    @foreach ($anexosUpload as $index => $arquivo)
                                        <div class="flex items-center gap-2 px-3 py-2
                                                    bg-white dark:bg-gray-700/60
                                                    border border-gray-100 dark:border-gray-600
                                                    rounded-lg">
                                            <x-lucide-file class="w-4 h-4 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                            <span class="text-xs text-gray-700 dark:text-gray-200 truncate flex-1 min-w-0">
                                                {{ $arquivo->getClientOriginalName() }}
                                            </span>
                                            <span class="text-[10px] text-gray-400 dark:text-gray-500 flex-shrink-0">
                                                @php
                                                    $bytes = $arquivo->getSize();
                                                    echo $bytes >= 1048576
                                                        ? number_format($bytes / 1048576, 1, ',', '.') . ' MB'
                                                        : number_format($bytes / 1024, 0, ',', '.') . ' KB';
                                                @endphp
                                            </span>
                                            <button type="button"
                                                    wire:click="removerAnexo({{ $index }})"
                                                    class="flex-shrink-0 p-0.5 rounded text-gray-300 dark:text-gray-600 hover:text-red-400 dark:hover:text-red-400 transition cursor-pointer"
                                                    title="Remover arquivo">
                                                <x-lucide-x class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    @endforeach

                                    @error('anexosUpload.*')
                                        <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    @error('anexosUpload')
                                        <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            {{-- Barra de ações --}}
                            <div class="flex items-center justify-between mt-3">

                                {{-- Botão de anexar --}}
                                <div class="flex items-center gap-3">
                                    <label for="anexos-input"
                                           class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400
                                                  hover:text-emerald-600 dark:hover:text-emerald-400 transition cursor-pointer">
                                        <x-lucide-paperclip class="w-3.5 h-3.5" />
                                        Anexar arquivo
                                    </label>

                                    {{-- Indicador de carregamento do upload --}}
                                    <span wire:loading wire:target="anexosUpload"
                                          class="inline-flex items-center gap-1 text-xs text-emerald-500 dark:text-emerald-400">
                                        <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        Carregando...
                                    </span>

                                    {{-- Dica de limite --}}
                                    @if (count($anexosUpload) === 0)
                                        <span class="text-[10px] text-gray-300 dark:text-gray-600">Máx. 5 arquivos · 10 MB cada</span>
                                    @endif
                                </div>

                                {{-- Botão enviar --}}
                                <button wire:click="responder" wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium
                                        bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-blue-500/20
                                        text-white transition cursor-pointer disabled:opacity-60">
                                    <x-lucide-send class="w-3.5 h-3.5" wire:loading.remove wire:target="responder" />
                                    <svg wire:loading wire:target="responder" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Responder
                                </button>
                            </div>

                        @else
                            <p class="text-xs text-gray-400 dark:text-gray-500 text-center py-2">
                                Apenas a equipe de RH pode responder às manifestações.
                            </p>
                        @endif

                    </div>
                </div>

            </div>

            {{-- ══ COLUNA LATERAL (1/3) ════════════════════════════════════ --}}
            <div class="space-y-5">

                {{-- Card: Informações --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Informações</h2>
                    </div>
                    <div class="px-5 py-4 space-y-4">

                        {{-- Solicitante --}}
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <x-lucide-user class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Solicitante</p>
                                @if ($manifestacao->is_anonimo)
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 italic">Anônimo</p>
                                @else
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $manifestacao->user?->name ?? '—' }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Data de abertura --}}
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <x-lucide-calendar class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Data de abertura</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $manifestacao->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        {{-- Privacidade --}}
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                @if ($manifestacao->is_anonimo)
                                    <x-lucide-eye-off class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                @else
                                    <x-lucide-shield class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                @endif
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Privacidade</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                    {{ $manifestacao->is_anonimo ? 'Anônimo' : 'Identificado' }}
                                </p>
                            </div>
                        </div>

                        {{-- Categoria --}}
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <x-dynamic-component :component="'lucide-' . $cat['icon']" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Categoria</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $cat['label'] }}</p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Card: Auto-Encerramento desta manifestação (apenas RH/Admin e ouvidoria aberta) --}}
                @if ($podeResponder && ! in_array($manifestacao->status, ['respondido', 'concluido']))
                @php
                    $ultimaAtiv     = $manifestacao->ultimaAtividadeEm();
                    $prazoEfet      = $manifestacao->prazoEfetivoHoras();   // int horas
                    $temPrazoCustom = $manifestacao->prazo_personalizado_horas !== null;
                    $globalAtivo    = $configGlobal->auto_encerramento_ativo;
                    $desativadoLocal = $manifestacao->auto_encerramento_desativado;
                    $efetivamenteAtivo = $globalAtivo && ! $desativadoLocal;

                    // ── Cálculo preciso em minutos (Carbon 3 retorna float em diffIn*) ──
                    $minutosDecorridos = max(0, (int) floor($ultimaAtiv->diffInMinutes(now())));
                    $prazoMinutos      = $prazoEfet * 60;

                    // Percentual com 1 casa decimal para a barra acompanhar minutos
                    $percentualDecorrido = $prazoMinutos > 0
                        ? max(0.0, min(100.0, round(($minutosDecorridos / $prazoMinutos) * 100, 1)))
                        : 0.0;

                    // ── Label adaptável (minutos → horas → dias) ──────────────────────
                    if ($minutosDecorridos === 0) {
                        $tempoLabel = '< 1min';
                    } elseif ($minutosDecorridos < 60) {
                        $tempoLabel = $minutosDecorridos . 'min';
                    } elseif ($minutosDecorridos < 1440) {
                        $h = (int) floor($minutosDecorridos / 60);
                        $m = $minutosDecorridos % 60;
                        $tempoLabel = $m > 0 ? "{$h}h {$m}min" : "{$h}h";
                    } else {
                        $d = (int) floor($minutosDecorridos / 1440);
                        $h = (int) floor(($minutosDecorridos % 1440) / 60);
                        $tempoLabel = $h > 0 ? "{$d}d {$h}h" : "{$d}d";
                    }

                    // Label do prazo também adaptável
                    if ($prazoEfet >= 24 && $prazoEfet % 24 === 0) {
                        $prazoLabel = ($prazoEfet / 24) . 'd';
                    } else {
                        $prazoLabel = $prazoEfet . 'h';
                    }
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden">

                    {{-- Header --}}
                    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                        <x-lucide-bot class="w-4 h-4 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Auto-Encerramento</h2>

                        @if (! $editandoAutoEncerramento)
                            {{-- Badge de status efetivo --}}
                            <span class="ml-auto text-[10px] font-semibold px-2 py-0.5 rounded-full
                                {{ $efetivamenteAtivo
                                    ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500' }}">
                                {{ $efetivamenteAtivo ? 'Ativo' : 'Inativo' }}
                            </span>

                            {{-- Botão Editar --}}
                            <button wire:click="toggleEditarAutoEncerramento"
                                class="p-1.5 rounded-lg text-gray-400 dark:text-gray-500
                                       hover:text-gray-600 dark:hover:text-gray-300
                                       hover:bg-gray-100 dark:hover:bg-gray-700
                                       transition cursor-pointer"
                                title="Editar configuração">
                                <x-lucide-pencil class="w-3.5 h-3.5" />
                            </button>
                        @else
                            <span class="ml-auto text-[10px] font-semibold px-2 py-0.5 rounded-full
                                         bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                Editando
                            </span>
                        @endif
                    </div>

                    {{-- ══════════════════════════════════════════════════ --}}
                    {{-- MODO VISUALIZAÇÃO --}}
                    {{-- ══════════════════════════════════════════════════ --}}
                    @if (! $editandoAutoEncerramento)
                    <div class="px-5 py-4 space-y-3">

                        {{-- Status global --}}
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Regra global</span>
                            <span class="font-medium {{ $globalAtivo ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500' }}">
                                {{ $globalAtivo ? 'Ativada' : 'Desativada' }}
                            </span>
                        </div>

                        {{-- Status desta ouvidoria --}}
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Nesta ouvidoria</span>
                            @if ($desativadoLocal)
                                <span class="font-medium text-red-500 dark:text-red-400">Desativado</span>
                            @else
                                <span class="font-medium text-gray-700 dark:text-gray-200">Segue regra global</span>
                            @endif
                        </div>

                        {{-- Prazo --}}
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Prazo</span>
                            <span class="font-medium text-gray-700 dark:text-gray-200">
                                {{ $prazoLabel }}
                                @if ($temPrazoCustom)
                                    <span class="text-[10px] font-normal text-blue-500 dark:text-blue-400">(personalizado)</span>
                                @else
                                    <span class="text-[10px] font-normal text-gray-400 dark:text-gray-500">(global)</span>
                                @endif
                            </span>
                        </div>

                        {{-- Barra de progresso de inatividade (só se efetivamente ativo) --}}
                        @if ($efetivamenteAtivo && ! in_array($manifestacao->status, ['respondido', 'concluido']))
                        <div class="pt-1">
                            <div class="flex items-center justify-between text-[10px] text-gray-400 dark:text-gray-500 mb-1.5">
                                <span>Inatividade</span>
                                <span class="font-medium {{ $percentualDecorrido >= 80 ? 'text-red-500 dark:text-red-400' : ($percentualDecorrido >= 50 ? 'text-amber-500 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400') }}">
                                    {{ $tempoLabel }} / {{ $prazoLabel }}
                                </span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500
                                    {{ $percentualDecorrido >= 80
                                        ? 'bg-red-400 dark:bg-red-500'
                                        : ($percentualDecorrido >= 50
                                            ? 'bg-amber-400 dark:bg-amber-500'
                                            : 'bg-emerald-400 dark:bg-emerald-500') }}"
                                    style="width: {{ $percentualDecorrido }}%">
                                </div>
                            </div>
                            <p class="text-[10px] mt-1 text-right font-medium
                                {{ $percentualDecorrido >= 80
                                    ? 'text-red-500 dark:text-red-400'
                                    : ($percentualDecorrido >= 50
                                        ? 'text-amber-500 dark:text-amber-400'
                                        : 'text-gray-400 dark:text-gray-500') }}">
                                {{ $percentualDecorrido }}% do prazo decorrido
                            </p>
                        </div>
                        @endif

                        {{-- Última atividade --}}
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">
                                Última atividade
                            </p>
                            <p class="text-xs text-gray-700 dark:text-gray-300">
                                {{ $ultimaAtiv->format('d/m/Y \à\s H:i') }}
                            </p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ $minutosDecorridos === 0 ? 'Atividade recente (< 1min)' : $tempoLabel . ' sem atividade' }}
                            </p>
                        </div>

                    </div>
                    @endif

                    {{-- ══════════════════════════════════════════════════ --}}
                    {{-- MODO EDIÇÃO --}}
                    {{-- ══════════════════════════════════════════════════ --}}
                    @if ($editandoAutoEncerramento)
                    <div class="px-5 py-4 space-y-4">

                        {{-- Indicador do estado global --}}
                        <div class="flex items-center gap-2 text-xs {{ $globalAtivo ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500' }}">
                            <x-lucide-info class="w-3.5 h-3.5 flex-shrink-0" />
                            Regra global está <strong>{{ $globalAtivo ? 'ativada' : 'desativada' }}</strong>
                            (prazo: {{ $configGlobal->prazo_horas }}h).
                        </div>

                        {{-- Toggle: desativar para esta manifestação --}}
                        <div class="flex items-center justify-between gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/40">
                            <div>
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-200">Desativar para esta ouvidoria</p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Ignora as regras globais.</p>
                            </div>
                            <button
                                type="button"
                                wire:click="$toggle('autoEncerramentoDesativado')"
                                class="relative flex-shrink-0 w-9 h-5 rounded-full transition-colors cursor-pointer
                                       focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2
                                       dark:focus:ring-offset-gray-800
                                       {{ $autoEncerramentoDesativado ? 'bg-red-400' : 'bg-gray-200 dark:bg-gray-600' }}"
                                role="switch"
                                aria-checked="{{ $autoEncerramentoDesativado ? 'true' : 'false' }}"
                            >
                                <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform
                                             {{ $autoEncerramentoDesativado ? 'translate-x-4' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        {{-- Prazo personalizado --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                                Prazo personalizado
                                <span class="font-normal text-gray-400 dark:text-gray-500">(horas — vazio = usa o global)</span>
                            </label>
                            <input
                                type="number"
                                wire:model="prazoPersonalizadoInput"
                                min="1"
                                max="8760"
                                placeholder="{{ $configGlobal->prazo_horas }}h (global)"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600
                                       bg-white dark:bg-gray-700/60 text-gray-800 dark:text-gray-100
                                       placeholder-gray-300 dark:placeholder-gray-600
                                       focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500
                                       focus:border-transparent transition"
                            />
                            @error('prazoPersonalizadoInput')
                                <p class="text-xs text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ações --}}
                        <div class="flex gap-2 pt-1">
                            {{-- Cancelar --}}
                            <button wire:click="toggleEditarAutoEncerramento"
                                wire:loading.attr="disabled"
                                wire:target="salvarAutoEncerramento"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium
                                       border border-gray-200 dark:border-gray-600
                                       text-gray-600 dark:text-gray-300
                                       hover:bg-gray-50 dark:hover:bg-gray-700/50
                                       transition cursor-pointer disabled:opacity-50">
                                <x-lucide-x class="w-3.5 h-3.5" />
                                Cancelar
                            </button>

                            {{-- Salvar --}}
                            <button wire:click="salvarAutoEncerramento"
                                wire:loading.attr="disabled"
                                wire:target="salvarAutoEncerramento"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium
                                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-blue-500/20
                                       text-white transition cursor-pointer disabled:opacity-60">
                                <x-lucide-save class="w-3.5 h-3.5" wire:loading.remove wire:target="salvarAutoEncerramento" />
                                <svg wire:loading wire:target="salvarAutoEncerramento" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Salvar
                            </button>
                        </div>

                    </div>
                    @endif

                </div>
                @endif

                {{-- Card: Alterar Status (apenas RH/Admin) --}}
                @if ($podeResponder)
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                        <x-lucide-settings-2 class="w-4 h-4 text-gray-400 dark:text-gray-500" />
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Alterar Status</h2>
                    </div>
                    <div class="px-5 py-4 space-y-2">

                        @php
                            $opcoes = [
                                'em_analise'   => ['label' => 'Em Análise',    'icon' => 'search',        'ring' => 'ring-amber-200 dark:ring-amber-800',   'bg' => 'bg-amber-50 dark:bg-amber-900/30',   'text' => 'text-amber-700 dark:text-amber-300',   'dot' => 'bg-amber-400'],
                                'em_andamento' => ['label' => 'Em Andamento',  'icon' => 'clock-4',       'ring' => 'ring-blue-200 dark:ring-blue-800',    'bg' => 'bg-blue-50 dark:bg-blue-900/30',    'text' => 'text-blue-700 dark:text-blue-300',    'dot' => 'bg-blue-400'],
                                'respondido'   => ['label' => 'Respondido',    'icon' => 'message-square','ring' => 'ring-purple-200 dark:ring-purple-800', 'bg' => 'bg-purple-50 dark:bg-purple-900/30', 'text' => 'text-purple-700 dark:text-purple-300', 'dot' => 'bg-purple-400'],
                                'concluido'    => ['label' => 'Concluído',     'icon' => 'check-circle',  'ring' => 'ring-emerald-200 dark:ring-emerald-800','bg' => 'bg-emerald-50 dark:bg-emerald-900/30','text' => 'text-emerald-700 dark:text-emerald-300','dot' => 'bg-emerald-400'],
                            ];
                        @endphp

                        @foreach ($opcoes as $slug => $opcao)
                            @php
                                $ativo    = $manifestacao->status === $slug;
                                // "Respondido" e "Concluído" só ficam disponíveis após ao menos uma resposta humana
                                $requerResposta = in_array($slug, ['respondido', 'concluido']);
                                $bloqueado      = $requerResposta && ! $temResposta;
                            @endphp
                            <div class="relative group">
                                <button
                                    wire:click="atualizarStatus('{{ $slug }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="atualizarStatus('{{ $slug }}')"
                                    @class([
                                        'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition ring-1',
                                        'ring-2 ' . $opcao['ring'] . ' ' . $opcao['bg'] . ' ' . $opcao['text'] . ' cursor-default' => $ativo,
                                        'ring-gray-100 dark:ring-gray-700 text-gray-400 dark:text-gray-500 opacity-50 cursor-not-allowed' => $bloqueado && ! $ativo,
                                        'ring-gray-100 dark:ring-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer' => ! $ativo && ! $bloqueado,
                                    ])
                                    {{ ($ativo || $bloqueado) ? 'disabled' : '' }}
                                >
                                    <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $ativo ? $opcao['dot'] : ($bloqueado ? 'bg-gray-200 dark:bg-gray-700' : 'bg-gray-300 dark:bg-gray-600') }}"></span>
                                    {{ $opcao['label'] }}

                                    @if ($ativo)
                                        <x-lucide-check class="w-3.5 h-3.5 ml-auto" />
                                    @elseif ($bloqueado)
                                        <x-lucide-lock class="w-3.5 h-3.5 ml-auto opacity-50" />
                                    @endif

                                    {{-- Spinner ao carregar --}}
                                    <svg wire:loading wire:target="atualizarStatus('{{ $slug }}')"
                                        class="animate-spin w-3.5 h-3.5 ml-auto" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </button>

                                {{-- Tooltip explicativo quando bloqueado --}}
                                @if ($bloqueado && ! $ativo)
                                    <div class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-10
                                                hidden group-hover:flex
                                                items-center gap-1.5 whitespace-nowrap
                                                bg-gray-900 dark:bg-gray-700 text-white text-xs
                                                px-2.5 py-1.5 rounded-lg shadow-lg">
                                        <x-lucide-alert-circle class="w-3 h-3 flex-shrink-0 text-amber-400" />
                                        Envie uma resposta antes de alterar para "{{ $opcao['label'] }}"
                                        {{-- seta --}}
                                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></span>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                    </div>
                </div>
                @endif

                {{-- Card: Linha do Tempo --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Linha do Tempo</h2>
                    </div>
                    <div class="px-5 py-4">
                        <ol class="space-y-4">

                            {{-- Manifestação registrada --}}
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mt-0.5">
                                    <x-lucide-clock class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Manifestação registrada</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $manifestacao->created_at->format('d/m/Y \à\s H:i') }}</p>
                                </div>
                            </li>

                            {{-- Em andamento --}}
                            @if ($manifestacao->status === 'em_andamento' || $manifestacao->respondido_em || $manifestacao->concluido_em)
                                <li class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center mt-0.5">
                                        <x-lucide-clock-4 class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Em andamento pelo RH</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                                            {{ ($manifestacao->respondido_em ?? $manifestacao->updated_at)->format('d/m/Y \à\s H:i') }}
                                        </p>
                                    </div>
                                </li>
                            @endif

                            {{-- Respondida --}}
                            @if ($manifestacao->respondido_em)
                                <li class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-7 h-7 rounded-full bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center mt-0.5">
                                        <x-lucide-message-square class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Respondida pelo RH</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $manifestacao->respondido_em->format('d/m/Y \à\s H:i') }}</p>
                                    </div>
                                </li>
                            @endif

                            {{-- Concluída --}}
                            @if ($manifestacao->concluido_em)
                                <li class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mt-0.5">
                                        <x-lucide-check-circle class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Manifestação concluída</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $manifestacao->concluido_em->format('d/m/Y \à\s H:i') }}</p>
                                    </div>
                                </li>
                            @endif

                            {{-- Pendente (se ainda não concluída) --}}
                            @if (! $manifestacao->concluido_em)
                                <li class="flex items-start gap-3 opacity-40">
                                    <div class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mt-0.5">
                                        <x-lucide-circle-dashed class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Conclusão pendente</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Aguardando</p>
                                    </div>
                                </li>
                            @endif

                        </ol>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Modal de sucesso do Auto-Encerramento ───────────────────────────── --}}
    @if ($modalSucesso)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div wire:click="$set('modalSucesso', false)"
             class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm"></div>

        {{-- Card --}}
        <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">

            {{-- Topo colorido --}}
            <div class="h-1.5 w-full
                {{ $modalSucessoTipo === 'disable' ? 'bg-red-400' : 'bg-emerald-500' }}"></div>

            <div class="px-6 pt-6 pb-7 text-center">

                {{-- Ícone --}}
                <div class="mx-auto mb-4 w-14 h-14 rounded-full flex items-center justify-center
                            {{ $modalSucessoTipo === 'disable'
                                ? 'bg-red-50 dark:bg-red-900/20'
                                : 'bg-emerald-50 dark:bg-emerald-900/30' }}">
                    @if ($modalSucessoTipo === 'disable')
                        <x-lucide-bot-off class="w-7 h-7 text-red-400 dark:text-red-400" />
                    @else
                        <x-lucide-bot class="w-7 h-7 text-emerald-500 dark:text-emerald-400" />
                    @endif
                </div>

                {{-- Título --}}
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">
                    {{ $modalSucessoTipo === 'disable' ? 'Auto-Encerramento Desativado' : 'Configuração Salva!' }}
                </h3>

                {{-- Mensagem --}}
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                    {{ $modalSucessoMensagem }}
                </p>

                {{-- Botão OK --}}
                <button wire:click="$set('modalSucesso', false)"
                    class="mt-5 w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                           {{ $modalSucessoTipo === 'disable'
                               ? 'bg-gray-700 hover:bg-gray-800 dark:bg-gray-600 dark:hover:bg-gray-500'
                               : 'bg-emerald-500 hover:bg-emerald-600 dark:bg-emerald-600 dark:hover:bg-emerald-700' }}
                           text-white transition cursor-pointer">
                    <x-lucide-check class="w-4 h-4" />
                    Entendido
                </button>

            </div>
        </div>
    </div>
    @endif

</div>
