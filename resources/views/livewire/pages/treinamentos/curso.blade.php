<div class="flex h-[calc(100vh-4rem)] overflow-hidden"
     x-data="{ confirmEnviar: false }">

    {{-- ══════════════════════════════════════════════════════════════════
         SIDEBAR
    ══════════════════════════════════════════════════════════════════ --}}
    <aside class="w-72 shrink-0 bg-white dark:bg-slate-800 border-r border-slate-100 dark:border-slate-700 flex flex-col overflow-hidden hidden lg:flex">

        {{-- Banner do curso --}}
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-4">
            <a href="{{ route('treinamentos') }}" wire:navigate
               class="inline-flex items-center gap-1.5 text-[11px] text-white/70 hover:text-white mb-3 lato-regular transition">
                <x-lucide-arrow-left class="w-3 h-3" /> Voltar ao catálogo
            </a>
            <h2 class="text-sm font-bold text-white lato-bold leading-snug line-clamp-2 mb-3">
                {{ $this->treinamento->titulo }}
            </h2>
            <div>
                <div class="flex justify-between text-[11px] text-white/70 mb-1.5">
                    <span class="lato-regular">Progresso</span>
                    <span class="lato-bold text-white">{{ $this->inscricao->progresso }}%</span>
                </div>
                <div class="w-full h-1.5 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white rounded-full transition-all duration-500"
                         style="width: {{ $this->inscricao->progresso }}%"></div>
                </div>
            </div>
        </div>

        {{-- Abas --}}
        <div class="flex border-b border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
            <button wire:click="$set('aba','aulas')"
                class="cursor-pointer flex-1 py-2.5 text-xs lato-bold transition
                    {{ $aba === 'aulas' ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-500' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}">
                Aulas
            </button>
            @if($this->treinamento->teste)
            <button wire:click="$set('aba','teste')"
                class="cursor-pointer flex-1 py-2.5 text-xs lato-bold transition
                    {{ $aba === 'teste' ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-500' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}">
                Teste
            </button>
            @endif
            <button wire:click="$set('aba','duvidas')"
                class="cursor-pointer flex-1 py-2.5 text-xs lato-bold transition
                    {{ $aba === 'duvidas' ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-500' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}">
                Dúvidas
            </button>
        </div>

        {{-- Lista de aulas --}}
        @if($aba === 'aulas')
        <nav class="overflow-y-auto flex-1 p-2 space-y-0.5">
            @foreach($this->treinamento->aulas as $i => $aula)
                @php
                    $concluida = isset($this->progressoMap[$aula->id]) && $this->progressoMap[$aula->id]['concluida'];
                    $ativa = $aulaAtualId === $aula->id;
                    $prevAula = $i > 0 ? $this->treinamento->aulas[$i - 1] : null;
                    $bloqueada = $i > 0 && !($prevAula && isset($this->progressoMap[$prevAula->id]) && $this->progressoMap[$prevAula->id]['concluida']);
                @endphp
                <button
                    @if(!$bloqueada) wire:click="selecionarAula({{ $aula->id }})" @endif
                    @if($bloqueada) disabled title="Conclua a aula anterior para desbloquear" @endif
                    class="{{ $bloqueada ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }} w-full text-left flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                        {{ !$bloqueada && $ativa
                            ? 'bg-violet-50 dark:bg-indigo-600/10'
                            : (!$bloqueada ? 'hover:bg-slate-50 dark:hover:bg-slate-700/40' : '') }}">
                    {{-- Ícone de status --}}
                    <div class="w-7 h-7 rounded-full shrink-0 flex items-center justify-center text-xs lato-bold transition
                        {{ $bloqueada
                            ? 'bg-slate-100 dark:bg-slate-700 text-slate-400'
                            : ($concluida
                                ? 'bg-emerald-500 text-white'
                                : ($ativa
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-slate-100 dark:bg-slate-700 text-slate-400')) }}">
                        @if($bloqueada)
                            <x-lucide-lock class="w-3 h-3" />
                        @elseif($concluida)
                            <x-lucide-check class="w-3.5 h-3.5" />
                        @elseif($ativa)
                            <x-lucide-play class="w-3 h-3 ml-0.5" />
                        @else
                            <span>{{ $i + 1 }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs lato-bold leading-snug truncate
                            {{ $ativa && !$bloqueada ? 'text-violet-700 dark:text-violet-300' : 'text-slate-700 dark:text-slate-200' }}">
                            {{ $aula->titulo }}
                        </p>
                        <p class="text-[10px] text-slate-400 lato-regular mt-0.5 flex items-center gap-1">
                            @if($bloqueada)
                                <x-lucide-lock class="w-2.5 h-2.5" /> Bloqueada
                            @else
                                <x-lucide-clock class="w-2.5 h-2.5" /> {{ $aula->duracao_formatada }}
                            @endif
                        </p>
                    </div>
                    @if($ativa && !$bloqueada)
                        <div class="w-1.5 h-1.5 rounded-full bg-indigo-600 shrink-0"></div>
                    @endif
                </button>
            @endforeach
        </nav>
        @endif

        {{-- Prazo --}}
        @if($this->inscricao->prazo_conclusao && $this->inscricao->status !== 'concluido')
        @php
            $diasRestantes = now()->startOfDay()->diffInDays($this->inscricao->prazo_conclusao, false);
            $prazoCor = $diasRestantes < 0 ? 'red' : ($diasRestantes <= 3 ? 'amber' : 'blue');
        @endphp
        <div class="p-3 border-t border-slate-100 dark:border-slate-700">
            <div class="bg-{{ $prazoCor }}-50 dark:bg-{{ $prazoCor }}-900/20 rounded-xl p-3 flex items-center gap-2.5">
                <x-lucide-calendar-clock class="w-4 h-4 text-{{ $prazoCor }}-500 shrink-0" />
                <div>
                    <p class="text-xs lato-bold text-{{ $prazoCor }}-700 dark:text-{{ $prazoCor }}-300">
                        @if($diasRestantes < 0) Prazo vencido @elseif($diasRestantes === 0) Prazo hoje! @else {{ $diasRestantes }}d restantes @endif
                    </p>
                    <p class="text-[11px] text-{{ $prazoCor }}-500 lato-regular">{{ $this->inscricao->prazo_conclusao->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Certificado --}}
        @if($this->inscricao->certificado)
        <div class="p-3 border-t border-slate-100 dark:border-slate-700">
            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/10 rounded-xl p-3 border border-amber-100 dark:border-amber-800/30">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-400 flex items-center justify-center shrink-0">
                        <x-lucide-award class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <p class="text-xs lato-bold text-amber-700 dark:text-amber-300">Certificado emitido!</p>
                        <p class="text-[10px] text-amber-500 font-mono">{{ $this->inscricao->certificado->codigo }}</p>
                    </div>
                </div>
                <a href="{{ route('treinamentos.certificado', $this->inscricao->id) }}" target="_blank"
                   class="flex items-center justify-center gap-1.5 w-full text-xs text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/30 hover:bg-amber-200 dark:hover:bg-amber-800/30 rounded-lg py-1.5 transition lato-bold">
                    <x-lucide-download class="w-3 h-3" /> Baixar PDF
                </a>
            </div>
            @if($this->inscricao->status === 'concluido' && !$this->inscricao->avaliacaoReacao)
            <button wire:click="abrirModalAvaliacao"
                class="cursor-pointer mt-2 w-full flex items-center justify-center gap-1.5 border border-amber-200 dark:border-amber-700 text-amber-600 dark:text-amber-400 text-xs px-3 py-1.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition lato-bold">
                <x-lucide-star class="w-3.5 h-3.5" /> Avaliar curso
            </button>
            @elseif($this->inscricao->avaliacaoReacao)
            <div class="mt-2 flex items-center gap-1 justify-center">
                @for($s=1;$s<=5;$s++)
                    <x-lucide-star class="w-3.5 h-3.5 {{ $s <= $this->inscricao->avaliacaoReacao->nota ? 'text-amber-400 fill-amber-400' : 'text-slate-300' }}" />
                @endfor
                <span class="text-[11px] text-slate-400 lato-regular ml-1">Avaliado</span>
            </div>
            @endif
        </div>
        @endif
    </aside>

    {{-- ══════════════════════════════════════════════════════════════════
         CONTEÚDO PRINCIPAL
    ══════════════════════════════════════════════════════════════════ --}}
    <main class="flex-1 overflow-y-auto bg-slate-50 dark:bg-slate-900">

        {{-- ──── ABA: AULAS ───────────────────────────────────────────── --}}
        @if($aba === 'aulas' && $this->aulaAtual)
            @php $aula = $this->aulaAtual; $concluida = isset($this->progressoMap[$aula->id]) && $this->progressoMap[$aula->id]['concluida']; @endphp

            {{-- ── Player com rastreamento de progresso ───────────────────── --}}
            @php
                $temVideo = $aula->video_url || $aula->video_arquivo;
                $videoTipo = $aula->videoTipo;
                $ytId = $aula->videoYoutubeId;
                $vimeoId = $aula->videoVimeoId;
                $duracaoSegundos = $aula->duracaoSegundos;
                $progressoExistente = $this->progressoMap[$aula->id]['segundos_assistidos'] ?? 0;
                $exigirVideo  = $temVideo && $aula->exigir_video;
                $threshold    = $exigirVideo ? 1.0 : 0.0; // 100% ou sem exigência
                $jaAssistiu50 = !$exigirVideo || ($duracaoSegundos > 0 && $progressoExistente >= ($duracaoSegundos * $threshold));
            @endphp

            @if($temVideo)
            <div
                wire:key="video-aula-{{ $aula->id }}"
                x-data="videoTracker({
                    aulaId: {{ $aula->id }},
                    tipo: '{{ $videoTipo }}',
                    ytId: '{{ $ytId }}',
                    vimeoId: '{{ $vimeoId }}',
                    duracaoEstimada: {{ $duracaoSegundos }},
                    jaAssistiu50: {{ $jaAssistiu50 ? 'true' : 'false' }},
                    progressoSalvo: {{ $progressoExistente }},
                    exigirVideo: {{ $exigirVideo ? 'true' : 'false' }}
                })"
                class="relative bg-black w-full"
                style="aspect-ratio: 16/9; max-height: 56vh;"
                wire:ignore>

                {{-- YouTube --}}
                @if($videoTipo === 'youtube' && $ytId)
                    <div :id="'yt-player-' + aulaId" class="w-full h-full"></div>
                {{-- Vimeo --}}
                @elseif($videoTipo === 'vimeo' && $vimeoId)
                    <iframe
                        :id="'vimeo-player-' + aulaId"
                        id="vimeo-player-{{ $aula->id }}"
                        src="https://player.vimeo.com/video/{{ $vimeoId }}?api=1&player_id=vimeo-player-{{ $aula->id }}"
                        class="w-full h-full" frameborder="0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen
                        x-init="initVimeo($el)">
                    </iframe>
                {{-- HTML5 --}}
                @elseif($videoTipo === 'html5')
                    <video class="w-full h-full" controls
                        x-init="initHtml5($el)"
                        @timeupdate="onHtml5TimeUpdate($event)"
                        @ratechange="onSpeedChange($event.target.playbackRate)">
                        <source src="{{ asset('storage/'.$aula->video_arquivo) }}" />
                    </video>
                {{-- Outro embed --}}
                @else
                    <iframe src="{{ $aula->video_embed_url }}" class="w-full h-full" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                @endif

                {{-- Indicador de progresso de watching --}}
                <div class="absolute bottom-0 left-0 right-0 bg-black/60 px-3 py-1.5 flex items-center gap-3"
                     x-show="duration > 0 && !suficiente">
                    <div class="flex-1 h-1 bg-white/20 rounded-full overflow-hidden">
                        <div class="h-full bg-violet-400 rounded-full transition-all duration-300"
                             :style="'width: ' + Math.min(watchedPercent, 100) + '%'"></div>
                    </div>
                    <span class="text-[10px] text-white/70 lato-regular shrink-0">
                        Assistido: <span x-text="Math.round(watchedPercent)"></span>% / <span x-text="exigirVideo ? '100%' : '—'"></span>
                    </span>
                </div>
                <div class="absolute bottom-0 left-0 right-0 bg-emerald-600/80 px-3 py-1.5 flex items-center justify-center gap-2"
                     x-show="suficiente && duration > 0" style="display:none">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-[11px] text-white lato-bold" x-text="exigirVideo ? 'Vídeo completo — você pode concluir esta aula' : 'Você pode concluir esta aula'"></span>
                </div>
            </div>
            @else
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-full flex items-center justify-center" style="aspect-ratio: 16/9; max-height: 40vh;">
                    <div class="text-center">
                        <x-lucide-play-circle class="w-16 h-16 text-white/30 mx-auto mb-2" />
                        <p class="text-sm text-white/40 lato-regular">Sem vídeo disponível</p>
                    </div>
                </div>
            @endif

            {{-- Barra de info da aula --}}
            <div class="bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 px-5 lg:px-8 py-4"
                 @if($temVideo) x-data @endif>
                <div class="max-w-4xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg lg:text-xl font-bold text-slate-800 dark:text-white lato-black leading-snug truncate">
                            {{ $aula->titulo }}
                        </h1>
                        <div class="flex items-center gap-3 mt-1 text-xs text-slate-400 lato-regular">
                            <span class="flex items-center gap-1">
                                <x-lucide-clock class="w-3.5 h-3.5" /> {{ $aula->duracao_formatada }}
                            </span>
                            @if($temVideo)
                                <span class="flex items-center gap-1 text-indigo-500 lato-bold">
                                    <x-lucide-shield class="w-3.5 h-3.5" />
                                    Assista o vídeo para concluir
                                </span>
                            @endif
                            @if($aula->material_pdf)
                                <a href="{{ asset('storage/'.$aula->material_pdf) }}" target="_blank"
                                   class="flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:underline lato-bold">
                                    <x-lucide-file-text class="w-3.5 h-3.5" /> Material PDF
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if(!$concluida)
                            <button
                                @if($temVideo)
                                    @click="$wire.marcarAulaConcluida({{ $aula->id }}, Math.round(window._vtWatchedSeconds_{{ $aula->id }} || 0))"
                                    :disabled="!($store.videoProgress?.suficiente ?? {{ $jaAssistiu50 ? 'true' : 'false' }})"
                                    :class="!($store.videoProgress?.suficiente ?? {{ $jaAssistiu50 ? 'true' : 'false' }}) ? 'opacity-50 cursor-not-allowed' : ''"
                                    :title="!($store.videoProgress?.suficiente ?? {{ $jaAssistiu50 ? 'true' : 'false' }}) ? (exigirVideo ? 'Assista o vídeo completo para concluir' : 'Assista o vídeo para concluir') : ''"
                                @else
                                    wire:click="marcarAulaConcluida({{ $aula->id }})"
                                @endif
                                class="cursor-pointer flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-sm px-4 py-2 rounded-xl shadow-sm shadow-emerald-500/20 transition lato-bold">
                                <x-lucide-check-circle class="w-4 h-4" />
                                Marcar concluída
                            </button>
                        @else
                            <span class="inline-flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-sm px-4 py-2 rounded-xl border border-emerald-200 dark:border-emerald-800/40 lato-bold">
                                <x-lucide-check-circle class="w-4 h-4" /> Concluída
                            </span>
                        @endif
                        <button wire:click="proximaAula"
                            @if($temVideo && !$jaAssistiu50)
                                :disabled="!$store.videoProgress.suficiente"
                                :class="!$store.videoProgress.suficiente ? 'opacity-50 cursor-not-allowed' : ''"
                                :title="!$store.videoProgress.suficiente ? (exigirVideo ? 'Assista o vídeo completo para pular' : 'Assista o vídeo para pular') : ''"
                            @endif
                            class="cursor-pointer flex items-center gap-1.5 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm px-4 py-2 rounded-xl transition lato-regular">
                            Próxima <x-lucide-chevron-right class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            {{-- Script de rastreamento (carregado uma vez) --}}
            @once
            <script>
            // ── Carrega YouTube IFrame API uma única vez ──────────────────────
            if (!window._ytApiLoaded) {
                window._ytApiLoaded = true;
                window._ytReadyCallbacks = [];
                window.onYouTubeIframeAPIReady = function() {
                    window._ytReady = true;
                    (window._ytReadyCallbacks || []).forEach(fn => fn());
                    window._ytReadyCallbacks = [];
                };
                const tag = document.createElement('script');
                tag.src = 'https://www.youtube.com/iframe_api';
                document.head.appendChild(tag);
            }

            // ── Carrega Vimeo Player SDK uma única vez ────────────────────────
            if (!window._vimeoLoaded) {
                window._vimeoLoaded = true;
                const tag = document.createElement('script');
                tag.src = 'https://player.vimeo.com/api/player.js';
                document.head.appendChild(tag);
            }

            // ── Componente Alpine videoTracker ────────────────────────────────
            // Registra o componente e o store — funciona na carga inicial E em
            // navegações Livewire SPA (onde alpine:init nunca dispara novamente).
            function _registerVideoTracker() {
                if (!window.Alpine) return;

                // Garante que o store existe
                if (!Alpine.store('videoProgress')) {
                    Alpine.store('videoProgress', { suficiente: false });
                }

                // Alpine.data só pode ser chamado uma vez por nome
                if (window._videoTrackerRegistered) return;
                window._videoTrackerRegistered = true;

                Alpine.data('videoTracker', (cfg) => ({
                    aulaId:          cfg.aulaId,
                    tipo:            cfg.tipo,
                    ytId:            cfg.ytId,
                    vimeoId:         cfg.vimeoId,
                    duracaoEstimada: cfg.duracaoEstimada || 0,
                    suficiente:      cfg.jaAssistiu50 || false,
                    exigirVideo:     cfg.exigirVideo || false,

                    watched:         [],      // bitmask por segundo
                    watchedSeconds:  cfg.progressoSalvo || 0,
                    duration:        cfg.duracaoEstimada || 0,
                    lastTime:        -1,
                    lastSpeed:       1,
                    watchedPercent:  0,
                    _pollInterval:   null,
                    _ytPlayer:       null,
                    _vimeoPlayer:    null,

                    // ── Inicialização ──────────────────────────────────────────
                    init() {
                        // Sincroniza store com estado inicial
                        Alpine.store('videoProgress').suficiente = this.suficiente;

                        // Pre-preenche bitmask com progresso já salvo
                        for (let i = 0; i < this.watchedSeconds; i++) this.watched[i] = true;
                        this._calcPercent();

                        if (this.tipo === 'youtube' && this.ytId) this._initYouTube();
                        // Vimeo e HTML5 são inicializados via x-init no elemento
                    },

                    destroy() {
                        if (this._pollInterval) clearInterval(this._pollInterval);
                        try { if (this._ytPlayer) this._ytPlayer.destroy(); } catch(e) {}
                        this._ytPlayer = null;
                    },

                    // ── HTML5 ──────────────────────────────────────────────────
                    initHtml5(el) {
                        // Captura duração real quando disponível
                        const setDur = () => { if (el.duration && isFinite(el.duration)) this.duration = el.duration; };
                        el.addEventListener('loadedmetadata', setDur);
                        el.addEventListener('durationchange', setDur);
                        if (el.duration && isFinite(el.duration)) this.duration = el.duration;
                    },

                    onHtml5TimeUpdate(e) {
                        const el = e.target;
                        this._trackTick(el.currentTime, el.playbackRate);
                    },

                    onSpeedChange(rate) {
                        this.lastSpeed = rate;
                    },

                    // ── Vimeo ──────────────────────────────────────────────────
                    initVimeo(iframeEl) {
                        const tryInit = () => {
                            if (typeof Vimeo === 'undefined') {
                                setTimeout(tryInit, 300);
                                return;
                            }
                            const player = new Vimeo.Player(iframeEl);
                            this._vimeoPlayer = player;

                            player.getDuration().then(d => { if (d) this.duration = d; });
                            player.on('playbackratechange', (data) => { this.lastSpeed = data.playbackRate; });

                            this._pollInterval = setInterval(async () => {
                                try {
                                    const [t, rate] = await Promise.all([player.getCurrentTime(), player.getPlaybackRate()]);
                                    this._trackTick(t, rate);
                                } catch {}
                            }, 1000);
                        };
                        tryInit();
                    },

                    // ── YouTube ────────────────────────────────────────────────
                    _initYouTube() {
                        const self = this;
                        const containerId = 'yt-player-' + this.aulaId;

                        const createPlayer = () => {
                            self._ytPlayer = new YT.Player(containerId, {
                                videoId: self.ytId,
                                playerVars: { rel: 0, modestbranding: 1, origin: window.location.origin },
                                width: '100%',
                                height: '100%',
                                events: {
                                    onReady(e) {
                                        // Tenta obter duração real; retenta se ainda não disponível
                                        const trySetDuration = () => {
                                            const dur = e.target.getDuration();
                                            if (dur > 0) {
                                                self.duration = dur;
                                                self._calcPercent(); // recalcula com duração correta
                                            } else {
                                                setTimeout(trySetDuration, 500);
                                            }
                                        };
                                        trySetDuration();

                                        self._pollInterval = setInterval(() => {
                                            const state = e.target.getPlayerState();
                                            if (state === YT.PlayerState.PLAYING) {
                                                const t = e.target.getCurrentTime();
                                                const r = e.target.getPlaybackRate();
                                                self._trackTick(t, r);
                                            }
                                        }, 1000);
                                    },
                                    onStateChange(e) {
                                        if (e.data === YT.PlayerState.ENDED) {
                                            const dur = e.target.getDuration();
                                            // Se chegou ao fim sem skip significativo, marcar resto
                                            if (self.watchedPercent >= 45) {
                                                self._trackTick(dur, 1);
                                                self._calcPercent();
                                            }
                                        }
                                    }
                                }
                            });
                        };

                        // Garante que o DOM está pronto (bindings Alpine aplicados) antes de criar o player
                        const safeCreate = () => this.$nextTick(() => createPlayer());

                        if (window._ytReady) {
                            safeCreate();
                        } else {
                            window._ytReadyCallbacks = window._ytReadyCallbacks || [];
                            window._ytReadyCallbacks.push(() => this.$nextTick(() => createPlayer()));
                        }
                    },

                    // ── Lógica central de rastreamento ─────────────────────────
                    _trackTick(currentTime, speed) {
                        // Velocidade acima de 2x → não contar
                        if (speed > 2.05) {
                            this.lastTime = currentTime;
                            return;
                        }

                        if (this.lastTime < 0) {
                            this.lastTime = currentTime;
                            return;
                        }

                        const gap = currentTime - this.lastTime;

                        // Gap negativo = rebobinou, apenas atualiza referência
                        if (gap < 0) {
                            this.lastTime = currentTime;
                            return;
                        }

                        // Skip para frente detectado (> 8 segundos de salto).
                        // Limite maior (8s) para tolerar atrasos do setInterval em
                        // abas em background (browsers throttlam a ~4-5s).
                        if (gap > 8) {
                            this.lastTime = currentTime;
                            return;
                        }

                        // Progresso normal: conta todos os segundos únicos do intervalo
                        if (gap > 0) {
                            const from = Math.floor(this.lastTime);
                            const to   = Math.floor(currentTime);
                            for (let s = from; s <= to; s++) {
                                if (!this.watched[s]) {
                                    this.watched[s] = true;
                                    this.watchedSeconds++;
                                }
                            }
                        }

                        this.lastTime = currentTime;
                        this._calcPercent();
                    },

                    _calcPercent() {
                        const dur = this.duration > 0 ? this.duration : this.duracaoEstimada;
                        if (dur <= 0) return;

                        this.watchedPercent = Math.min((this.watchedSeconds / dur) * 100, 100);

                        // Mantém variável global atualizada para o botão "Marcar concluída"
                        window['_vtWatchedSeconds_' + this.aulaId] = Math.round(this.watchedSeconds);

                        const threshold = this.exigirVideo ? 100 : 0;
                        if (!this.suficiente && (threshold === 0 || this.watchedPercent >= threshold)) {
                            this.suficiente = true;
                            Alpine.store('videoProgress').suficiente = true;
                            $wire.registrarProgressoVideo(this.aulaId, Math.round(this.watchedSeconds));
                        }
                    }
                }));
            } // fim _registerVideoTracker

            // Executa imediatamente se Alpine já estiver pronto (navegação SPA),
            // caso contrário aguarda o evento alpine:init (carga inicial).
            if (window.Alpine) {
                _registerVideoTracker();
            } else {
                document.addEventListener('alpine:init', _registerVideoTracker);
            }
            </script>
            @endonce

            {{-- Corpo da aula --}}
            <div class="p-5 lg:p-8 max-w-4xl mx-auto space-y-6">

                {{-- Descrição --}}
                @if($aula->descricao)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
                    <h3 class="text-xs lato-bold font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Sobre esta aula</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 lato-regular leading-relaxed">
                        {{ $aula->descricao }}
                    </p>
                </div>
                @endif

                {{-- Dúvidas --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                            <x-lucide-message-circle class="w-3.5 h-3.5 text-indigo-500" />
                        </div>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold">
                            Dúvidas sobre esta aula?
                        </h3>
                    </div>
                    <div class="flex gap-2">
                        <input wire:model="novaDuvida" type="text"
                            placeholder="Escreva sua dúvida aqui…"
                            class="flex-1 text-sm px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                        <button wire:click="enviarDuvida"
                            class="cursor-pointer flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm px-4 py-2.5 rounded-xl shadow-sm shadow-indigo-500/20 transition lato-bold shrink-0">
                            <x-lucide-send class="w-3.5 h-3.5" />
                            Enviar
                        </button>
                    </div>
                    @error('novaDuvida')
                        <p class="text-xs text-red-500 mt-1.5 lato-regular flex items-center gap-1">
                            <x-lucide-alert-circle class="w-3 h-3" /> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

        {{-- ──── ABA: TESTE ───────────────────────────────────────────── --}}
        @elseif($aba === 'teste')
            @php $teste = $this->treinamento->teste; @endphp
            <div class="p-6 lg:p-10 max-w-3xl mx-auto">

                {{-- Resultado --}}
                @if($testeEnviado && $resultadoTeste)
                    <div class="text-center mb-8">
                        @if($resultadoTeste['aprovado'])
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/30">
                                <x-lucide-check-circle class="w-10 h-10 text-white" />
                            </div>
                            <h2 class="text-2xl font-bold text-emerald-600 lato-black">Parabéns! Você foi aprovado!</h2>
                        @else
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-red-400 to-rose-500 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-red-500/30">
                                <x-lucide-x-circle class="w-10 h-10 text-white" />
                            </div>
                            <h2 class="text-2xl font-bold text-red-600 lato-black">Não foi dessa vez…</h2>
                        @endif
                        <p class="text-slate-500 dark:text-slate-400 mt-2 lato-regular">
                            Você acertou <strong>{{ $resultadoTeste['acertos'] }}</strong> de <strong>{{ $resultadoTeste['total'] }}</strong> questões
                        </p>
                        <div class="mt-5 inline-flex items-center gap-4 bg-white dark:bg-slate-800 rounded-2xl px-7 py-5 shadow-sm border border-slate-200 dark:border-slate-700">
                            <span class="text-4xl font-black lato-black
                                {{ $resultadoTeste['aprovado'] ? 'text-emerald-500' : 'text-red-500' }}">
                                {{ number_format($resultadoTeste['nota'], 1) }}%
                            </span>
                            <div class="text-left border-l border-slate-100 dark:border-slate-700 pl-4">
                                <p class="text-xs text-slate-400 lato-regular">Nota mínima: {{ $resultadoTeste['nota_minima'] }}%</p>
                                <p class="text-sm lato-bold {{ $resultadoTeste['aprovado'] ? 'text-emerald-500' : 'text-red-500' }}">
                                    {{ $resultadoTeste['aprovado'] ? '✓ Aprovado' : '✕ Reprovado' }}
                                </p>
                            </div>
                        </div>
                        @if($this->inscricao->certificado)
                        <div class="mt-6 bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/10 rounded-2xl p-5 border border-amber-200 dark:border-amber-700/40">
                            <x-lucide-award class="w-8 h-8 text-amber-500 mx-auto mb-2" />
                            <p class="text-sm font-semibold text-amber-700 dark:text-amber-300 lato-bold">Certificado emitido!</p>
                            <p class="text-xs text-amber-500 font-mono mt-1">{{ $this->inscricao->certificado->codigo }}</p>
                            <a href="{{ route('treinamentos') }}?aba=certificados" wire:navigate
                               class="mt-3 inline-block text-xs text-amber-600 hover:underline lato-bold">
                                Ver meus certificados →
                            </a>
                        </div>
                        @endif
                        @php
                            $tentativasUsadas = $this->inscricao->tentativas()->where('teste_id', $teste->id)->count();
                            $tentativasRestantes = $teste->tentativas_maximas - $tentativasUsadas;
                        @endphp
                        @if(!$resultadoTeste['aprovado'] && $tentativasRestantes > 0)
                        <button wire:click="iniciarTeste"
                            class="cursor-pointer mt-6 inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-indigo-500/20 text-white text-sm px-6 py-2.5 rounded-xl transition lato-bold">
                            <x-lucide-refresh-cw class="w-4 h-4" />
                            Tentar novamente ({{ $tentativasRestantes }} restante{{ $tentativasRestantes > 1 ? 's' : '' }})
                        </button>
                        @endif
                    </div>

                {{-- Teste em andamento --}}
                @elseif($testeAtivo)
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white lato-black">{{ $teste->titulo }}</h2>
                            @if($teste->descricao)
                                <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-0.5">{{ $teste->descricao }}</p>
                            @endif
                        </div>
                        <div class="text-xs text-slate-400 lato-regular bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2 rounded-xl">
                            {{ count($respostas) }}/{{ $teste->questoes->count() }}
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach($teste->questoes as $qi => $questao)
                        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold mb-4 flex gap-2">
                                <span class="text-indigo-500 shrink-0">{{ $qi + 1 }}.</span>
                                <span>{{ $questao->enunciado }}</span>
                            </p>
                            <div class="space-y-2">
                                @foreach($questao->opcoes as $opcao)
                                @php $marcada = isset($respostas[$questao->id]) && $respostas[$questao->id] == $opcao->id; @endphp
                                <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition border
                                    {{ $marcada
                                        ? 'border-indigo-400 bg-violet-50 dark:bg-indigo-600/10'
                                        : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition
                                        {{ $marcada ? 'border-indigo-500 bg-indigo-600' : 'border-slate-300 dark:border-slate-600' }}">
                                        @if($marcada)
                                            <div class="w-2 h-2 rounded-full bg-white"></div>
                                        @endif
                                    </div>
                                    <input type="radio" name="questao_{{ $questao->id }}" value="{{ $opcao->id }}"
                                        wire:click="responder({{ $questao->id }}, {{ $opcao->id }})"
                                        {{ $marcada ? 'checked' : '' }} class="sr-only" />
                                    <span class="text-sm text-slate-700 dark:text-slate-200 lato-regular">{{ $opcao->texto }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-8 flex items-center justify-between">
                        <p class="text-sm text-slate-400 lato-regular">
                            <span class="font-semibold text-slate-600 dark:text-slate-300">{{ count($respostas) }}</span> de {{ $teste->questoes->count() }} respondidas
                        </p>
                        <button @click="confirmEnviar = true"
                            @if(count($respostas) < $teste->questoes->count()) disabled @endif
                            class="cursor-pointer flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm px-6 py-2.5 rounded-xl shadow-md shadow-indigo-500/20 transition lato-bold">
                            <x-lucide-send class="w-4 h-4" />
                            Enviar teste
                        </button>
                    </div>

                {{-- Tela inicial do teste --}}
                @else
                    @php
                        $tentativasUsadas = $this->inscricao->tentativas()->where('teste_id', $teste->id)->count();
                        $tentativasRestantes = $teste->tentativas_maximas - $tentativasUsadas;
                        $melhorNota = $this->inscricao->tentativas()->where('teste_id', $teste->id)->max('nota');
                    @endphp
                    <div class="text-center">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-700 flex items-center justify-center mx-auto mb-5 shadow-lg shadow-indigo-500/30">
                            <x-lucide-clipboard-check class="w-10 h-10 text-white" />
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white lato-black">{{ $teste->titulo }}</h2>
                        @if($teste->descricao)
                            <p class="text-slate-500 dark:text-slate-400 mt-2 lato-regular">{{ $teste->descricao }}</p>
                        @endif

                        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                            @foreach([
                                ['label' => 'Questões', 'value' => $teste->questoes->count(), 'icon' => 'list', 'color' => 'violet'],
                                ['label' => 'Nota mínima', 'value' => $teste->nota_minima.'%', 'icon' => 'target', 'color' => 'blue'],
                                ['label' => 'Tentativas restantes', 'value' => $tentativasRestantes, 'icon' => 'refresh-cw', 'color' => 'amber'],
                                ['label' => 'Melhor nota', 'value' => $melhorNota ? number_format($melhorNota,1).'%' : '—', 'icon' => 'trophy', 'color' => 'emerald'],
                            ] as $stat)
                            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-100 dark:border-slate-700">
                                <p class="text-2xl font-black lato-black text-slate-800 dark:text-white">{{ $stat['value'] }}</p>
                                <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ $stat['label'] }}</p>
                            </div>
                            @endforeach
                        </div>

                        @if($tentativasRestantes > 0)
                        <button wire:click="iniciarTeste"
                            class="cursor-pointer mt-8 inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-lg shadow-indigo-500/20 text-white text-sm px-8 py-3 rounded-xl transition lato-bold">
                            <x-lucide-play class="w-4 h-4" />
                            Iniciar teste
                        </button>
                        @else
                        <p class="mt-8 text-sm text-red-500 lato-regular">Você esgotou todas as tentativas para este teste.</p>
                        @endif
                    </div>
                @endif
            </div>

        {{-- ──── ABA: DÚVIDAS ─────────────────────────────────────────── --}}
        @elseif($aba === 'duvidas')
            <div class="p-6 lg:p-10 max-w-3xl mx-auto">

                <div class="mb-6">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white lato-black">Minhas Dúvidas</h2>
                    <p class="text-sm text-slate-400 lato-regular mt-0.5">Envie suas dúvidas para o instrutor responder</p>
                </div>

                {{-- Form nova dúvida --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-100 dark:border-slate-700 mb-6">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 lato-bold mb-3 flex items-center gap-2">
                        <x-lucide-message-circle class="w-4 h-4 text-indigo-500" /> Nova dúvida
                    </h3>
                    <textarea wire:model="novaDuvida" rows="3"
                        placeholder="Descreva sua dúvida com detalhes…"
                        class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular resize-none transition"></textarea>
                    @error('novaDuvida')
                        <p class="text-xs text-red-500 mt-1 lato-regular">{{ $message }}</p>
                    @enderror
                    <div class="mt-3 flex justify-end">
                        <button wire:click="enviarDuvida"
                            class="cursor-pointer flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm px-4 py-2 rounded-xl shadow-sm shadow-indigo-500/20 transition lato-bold">
                            <x-lucide-send class="w-4 h-4" /> Enviar dúvida
                        </button>
                    </div>
                </div>

                {{-- Lista de dúvidas --}}
                <div class="space-y-3">
                    @forelse($this->duvidas as $duvida)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                        <div class="p-4 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center shrink-0">
                                <x-lucide-help-circle class="w-4 h-4 text-indigo-500" />
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    @if($duvida->aula)
                                        <span class="text-[11px] bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 px-2 py-0.5 rounded-full lato-regular">
                                            {{ $duvida->aula->titulo }}
                                        </span>
                                    @endif
                                    <span class="text-[11px] px-2 py-0.5 rounded-full lato-bold
                                        {{ $duvida->status === 'respondida'
                                            ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                                            : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }}">
                                        {{ $duvida->status === 'respondida' ? '✓ Respondida' : 'Aguardando resposta' }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 lato-regular ml-auto">{{ $duvida->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-slate-700 dark:text-slate-200 lato-regular">{{ $duvida->pergunta }}</p>
                            </div>
                        </div>
                        @if($duvida->respostas->isNotEmpty())
                        <div class="border-t border-slate-100 dark:border-slate-700 bg-violet-50/50 dark:bg-violet-900/10 p-4 space-y-3">
                            @foreach($duvida->respostas as $resp)
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                                    <x-lucide-user class="w-3.5 h-3.5 text-white" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <p class="text-xs lato-bold text-indigo-600 dark:text-indigo-400">{{ $resp->usuario->name }}</p>
                                        <span class="text-[10px] bg-violet-100 dark:bg-violet-900/30 text-indigo-600 dark:text-indigo-400 px-1.5 py-0.5 rounded-full lato-regular">Instrutor</span>
                                        <span class="text-[11px] text-slate-400 lato-regular ml-auto">{{ $resp->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-300 lato-regular">{{ $resp->resposta }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                            <x-lucide-help-circle class="w-7 h-7 opacity-40" />
                        </div>
                        <p class="text-sm lato-bold text-slate-500">Nenhuma dúvida enviada ainda</p>
                        <p class="text-xs text-slate-400 lato-regular mt-0.5">Suas dúvidas aparecerão aqui</p>
                    </div>
                    @endforelse
                </div>
            </div>
        @endif

    </main>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: AVALIAÇÃO DE REAÇÃO
    ══════════════════════════════════════════════════════════════════ --}}
    @if($modalAvaliacao)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('modalAvaliacao',false)"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 to-yellow-500 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-amber-400/30">
                <x-lucide-star class="w-8 h-8 text-white" />
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white lato-black mb-1">Como foi o curso?</h3>
            <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular mb-5">
                Sua avaliação ajuda a melhorar os treinamentos
            </p>
            <div class="flex justify-center gap-2 mb-4">
                @for($s = 1; $s <= 5; $s++)
                <button wire:click="setNota({{ $s }})"
                    class="cursor-pointer w-10 h-10 rounded-xl flex items-center justify-center transition text-2xl
                        {{ $avaliacaoNota >= $s ? 'text-amber-400 scale-110 bg-amber-50 dark:bg-amber-900/20' : 'text-slate-200 dark:text-slate-700 hover:text-amber-300' }}">
                    ★
                </button>
                @endfor
            </div>
            @if($avaliacaoNota > 0)
            <p class="text-xs text-amber-600 dark:text-amber-400 lato-bold mb-4">
                {{ ['','Muito ruim 😞','Ruim 😕','Regular 😐','Bom 😊','Excelente! 🤩'][$avaliacaoNota] }}
            </p>
            @endif
            <textarea wire:model="avaliacaoComentario" rows="3"
                placeholder="Deixe um comentário opcional…"
                class="w-full text-sm px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 focus:border-amber-400 lato-regular resize-none mb-4 transition"></textarea>
            <div class="flex gap-3">
                <button wire:click="$set('modalAvaliacao',false)"
                    class="cursor-pointer flex-1 px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl transition lato-regular">
                    Agora não
                </button>
                <button wire:click="salvarAvaliacao"
                    @if($avaliacaoNota === 0) disabled @endif
                    class="cursor-pointer flex-1 px-4 py-2.5 text-sm bg-gradient-to-r from-amber-400 to-yellow-500 hover:from-amber-500 hover:to-yellow-600 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl transition lato-bold">
                    Enviar avaliação
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Dialog: Confirmar envio do teste ────────────────────────────── --}}
    <div x-show="confirmEnviar"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         @keydown.escape.window="confirmEnviar = false"
         style="display:none;">

        <div x-show="confirmEnviar"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             @click.stop
             class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

            {{-- Header gradiente --}}
            <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700 px-6 pt-6 pb-8 text-center overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-black/10 rounded-full translate-y-1/2 -translate-x-1/4 pointer-events-none"></div>
                <div class="relative">
                    <div class="w-16 h-16 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <x-lucide-send class="w-8 h-8 text-white" />
                    </div>
                    <h3 class="text-lg lato-black text-white">Enviar teste?</h3>
                    <p class="text-sm text-white/70 lato-regular mt-1">Esta ação não pode ser desfeita</p>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5">
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/30 rounded-xl p-4 flex items-start gap-3">
                    <x-lucide-triangle-alert class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                    <div>
                        <p class="text-xs lato-bold text-amber-800 dark:text-amber-300">Atenção antes de confirmar</p>
                        <p class="text-xs text-amber-700 dark:text-amber-400 lato-regular mt-0.5 leading-relaxed">
                            Após enviar, suas respostas serão registradas e a tentativa ficará encerrada. Verifique se respondeu todas as questões.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-6 pb-6">
                <button @click="confirmEnviar = false" type="button"
                    class="cursor-pointer flex-1 px-4 py-2.5 text-sm lato-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Cancelar
                </button>
                <button @click="confirmEnviar = false; $wire.enviarTeste()" type="button"
                    class="cursor-pointer flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm lato-bold rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-md shadow-indigo-500/25 transition">
                    <x-lucide-send class="w-4 h-4" />
                    Sim, enviar
                </button>
            </div>
        </div>
    </div>

</div>
