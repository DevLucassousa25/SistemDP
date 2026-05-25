<div class="p-4 sm:p-6 lg:p-8" id="organograma-page">

    {{-- ───── Cabeçalho ───────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white font-display tracking-tight lato-black">
                Organograma
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1 lato-regular">
                Visualize a hierarquia da empresa por departamento
            </p>
        </div>

        {{-- Botões de exportação --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <button onclick="exportarPNG()"
                class="flex items-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer shadow-sm">
                <x-lucide-image class="w-3.5 h-3.5" />
                Exportar PNG
            </button>
            <button onclick="exportarPDF()"
                class="flex items-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition cursor-pointer shadow-sm shadow-emerald-200/50">
                <x-lucide-file-text class="w-3.5 h-3.5" />
                Exportar PDF
            </button>
        </div>
    </div>

    {{-- ───── Cards de estatísticas ─────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-[#F1F5F9] dark:bg-slate-800 rounded-2xl p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-600 dark:text-slate-400 lato-regular">Colaboradores</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1 lato-black">{{ $this->stats['total'] }}</p>
            </div>
            <x-lucide-users class="w-8 h-8 text-slate-400" />
        </div>
        <div class="bg-[#DBEAFE] dark:bg-blue-900/20 rounded-2xl p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-600 dark:text-blue-200 lato-regular">Departamentos</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1 lato-black">{{ $this->stats['departamentos'] }}</p>
            </div>
            <x-lucide-building-2 class="w-8 h-8 text-blue-400" />
        </div>
        <div class="bg-[#D1FAE5] dark:bg-emerald-900/20 rounded-2xl p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-600 dark:text-emerald-200 lato-regular">Gerentes</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1 lato-black">{{ $this->stats['gerentes'] }}</p>
            </div>
            <x-lucide-briefcase class="w-8 h-8 text-emerald-500" />
        </div>
    </div>

    {{-- ───── Área do Organograma ──────────────────────────────────── --}}
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">

        {{-- Barra de controles --}}
        <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
            <button onclick="fitOrganograma()" title="Ajustar à tela"
                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600 transition cursor-pointer">
                <x-lucide-maximize-2 class="w-4 h-4" />
            </button>
            <span class="text-xs text-slate-400 lato-regular ml-1">Scroll para zoom • Arraste para navegar • Clique no colaborador para ver o perfil</span>
        </div>

        {{-- ApexTree container --}}
        <div id="org-tree" wire:ignore style="height: 620px;"></div>
    </div>

    {{-- ───── Drawer de Perfil ─────────────────────────────────────── --}}
    {{-- Backdrop --}}
    <div
        x-data
        x-show="$wire.drawerAberto"
        x-transition.opacity.duration.200ms
        @keydown.escape.window="$wire.drawerAberto && $wire.call('fecharDrawer')"
        class="fixed inset-0 z-[60] bg-slate-900/50 backdrop-blur-sm"
        wire:click="fecharDrawer"
        style="display: none;">
    </div>

    {{-- Painel lateral --}}
    <aside class="fixed top-0 right-0 z-[70] h-screen w-full sm:max-w-md bg-white dark:bg-slate-800 shadow-2xl flex flex-col
                   transform transition-transform duration-300 ease-out
                   {{ $drawerAberto ? 'translate-x-0' : 'translate-x-full' }}">

        @php $colaborador = $this->colaboradorSelecionado; @endphp

        @if ($colaborador)
            @php
                $slug      = $colaborador->accessProfile?->slug;
                $ehCeo     = $slug === 'ceo';
                $ehAdmin   = $slug === 'administrator';
                $ehGeRh    = $slug === 'hr_manager';
                $ehRh      = $slug === 'hr';
                $ehGerente = $slug === 'manager';
                $iniciais  = $colaborador->initials();
            @endphp

            {{-- Header colorido --}}
            <div class="relative overflow-hidden flex-shrink-0
                {{ $ehCeo                                             ? 'bg-gradient-to-br from-yellow-500 to-amber-600'   : '' }}
                {{ $ehAdmin   && !$ehCeo                              ? 'bg-gradient-to-br from-red-400 to-rose-500'        : '' }}
                {{ $ehGeRh    && !$ehCeo && !$ehAdmin                 ? 'bg-gradient-to-br from-purple-500 to-violet-600'   : '' }}
                {{ $ehRh      && !$ehCeo && !$ehAdmin && !$ehGeRh     ? 'bg-gradient-to-br from-blue-400 to-indigo-500'     : '' }}
                {{ $ehGerente && !$ehCeo && !$ehAdmin && !$ehGeRh && !$ehRh ? 'bg-gradient-to-br from-emerald-500 to-teal-500' : '' }}
                {{ !$ehCeo && !$ehAdmin && !$ehGeRh && !$ehRh && !$ehGerente ? 'bg-gradient-to-br from-slate-500 to-slate-600' : '' }}">

                <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/10"></div>
                <div class="absolute -left-10 -bottom-16 w-52 h-52 rounded-full bg-white/5"></div>

                <button type="button" wire:click="fecharDrawer"
                    class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-lg bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white transition cursor-pointer z-10">
                    <x-lucide-x class="w-4 h-4" />
                </button>

                <div class="relative px-6 pt-8 pb-6 flex flex-col items-center text-center">
                    @if ($colaborador->avatarUrl())
                        <img src="{{ $colaborador->avatarUrl() }}" alt="{{ $colaborador->name }}"
                             class="w-24 h-24 rounded-2xl object-cover border-2 border-white/40 shadow-lg mb-3" />
                    @else
                        <div class="w-24 h-24 rounded-2xl bg-white/20 backdrop-blur-sm border-2 border-white/40 flex items-center justify-center text-white text-3xl font-bold lato-black shadow-lg mb-3">
                            {{ $iniciais ?: '??' }}
                        </div>
                    @endif

                    @if ($ehCeo)
                        <span class="inline-flex items-center gap-1 bg-white/20 text-white text-[10px] font-bold lato-bold px-2 py-0.5 rounded-full mb-2 border border-white/30">
                            <x-lucide-star class="w-3 h-3" />CEO
                        </span>
                    @elseif ($ehAdmin)
                        <span class="inline-flex items-center gap-1 bg-white/20 text-white text-[10px] font-bold lato-bold px-2 py-0.5 rounded-full mb-2 border border-white/30">
                            <x-lucide-shield-check class="w-3 h-3" />Administrador
                        </span>
                    @elseif ($ehGeRh)
                        <span class="inline-flex items-center gap-1 bg-white/20 text-white text-[10px] font-bold lato-bold px-2 py-0.5 rounded-full mb-2 border border-white/30">
                            <x-lucide-users class="w-3 h-3" />Gerente de RH
                        </span>
                    @elseif ($ehRh)
                        <span class="inline-flex items-center gap-1 bg-white/20 text-white text-[10px] font-bold lato-bold px-2 py-0.5 rounded-full mb-2 border border-white/30">
                            <x-lucide-users class="w-3 h-3" />RH
                        </span>
                    @elseif ($ehGerente)
                        <span class="inline-flex items-center gap-1 bg-amber-400/90 text-amber-900 text-[10px] font-bold lato-bold px-2 py-0.5 rounded-full mb-2">
                            <x-lucide-crown class="w-3 h-3" />Gerente
                        </span>
                    @endif

                    <h2 class="text-xl font-bold text-white lato-black leading-tight">{{ $colaborador->name }}</h2>
                    <p class="text-sm text-white/90 lato-regular mt-1">{{ $colaborador->position ?? 'Sem cargo definido' }}</p>
                </div>
            </div>

            {{-- Conteúdo --}}
            <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                {{-- Contato --}}
                <section class="bg-white dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <x-lucide-mail class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <h3 class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">Contato</h3>
                    </div>
                    <a href="mailto:{{ $colaborador->email }}"
                       class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border border-slate-100 dark:border-slate-700 transition group">
                        <div class="w-9 h-9 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center shrink-0">
                            <x-lucide-at-sign class="w-4 h-4 text-slate-500" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">E-mail</p>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 lato-bold truncate">{{ $colaborador->email }}</p>
                        </div>
                        <x-lucide-external-link class="w-4 h-4 text-slate-300 group-hover:text-emerald-500 shrink-0" />
                    </a>
                </section>

                {{-- Cargo e Departamento --}}
                <section class="bg-white dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <x-lucide-briefcase class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <h3 class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">Cargo e Departamento</h3>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                            <div class="w-9 h-9 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center shrink-0">
                                <x-lucide-id-card class="w-4 h-4 text-slate-500" />
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Cargo</p>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 lato-bold">{{ $colaborador->position ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                            <div class="w-9 h-9 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center shrink-0">
                                <x-lucide-building-2 class="w-4 h-4 text-slate-500" />
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Departamento</p>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 lato-bold">{{ $colaborador->department?->name ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Perfil de acesso --}}
                <section class="bg-white dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <x-lucide-shield class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <h3 class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">Perfil de Acesso</h3>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                        <div @class([
                            'w-9 h-9 rounded-lg flex items-center justify-center shrink-0',
                            'bg-amber-50 text-amber-500'     => $ehCeo,
                            'bg-red-50 text-red-500'         => $ehAdmin   && !$ehCeo,
                            'bg-purple-50 text-purple-500'   => $ehGeRh    && !$ehCeo && !$ehAdmin,
                            'bg-blue-50 text-blue-500'       => $ehRh      && !$ehCeo && !$ehAdmin && !$ehGeRh,
                            'bg-emerald-50 text-emerald-500' => $ehGerente && !$ehCeo && !$ehAdmin && !$ehGeRh && !$ehRh,
                            'bg-slate-100 text-slate-500'    => !$ehCeo && !$ehAdmin && !$ehGeRh && !$ehRh && !$ehGerente,
                        ])>
                            @if ($ehCeo)     <x-lucide-star class="w-4 h-4" />
                            @elseif ($ehAdmin)  <x-lucide-shield-check class="w-4 h-4" />
                            @elseif ($ehGeRh)   <x-lucide-users class="w-4 h-4" />
                            @elseif ($ehRh)     <x-lucide-users class="w-4 h-4" />
                            @elseif ($ehGerente) <x-lucide-briefcase class="w-4 h-4" />
                            @else <x-lucide-user class="w-4 h-4" /> @endif
                        </div>
                        <div>
                            <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Função no sistema</p>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 lato-bold">
                                {{ $colaborador->accessProfile?->name ?? 'Sem perfil' }}
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Footer --}}
            <div class="flex-shrink-0 px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex gap-3">
                <a href="mailto:{{ $colaborador->email }}"
                   class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm lato-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-sm shadow-emerald-200/50">
                    <x-lucide-mail class="w-4 h-4" />Enviar e-mail
                </a>
                @if(auth()->user()?->isRhOuDp())
                    <a href="{{ route('users.details', $colaborador->id) }}"
                       class="px-4 py-2.5 text-sm lato-bold rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600 transition flex items-center gap-1.5">
                        <x-lucide-external-link class="w-4 h-4" />Perfil
                    </a>
                @endif
                <button type="button" wire:click="fecharDrawer"
                    class="px-4 py-2.5 text-sm lato-bold rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600 transition cursor-pointer">
                    Fechar
                </button>
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                    <x-lucide-user class="w-7 h-7 text-slate-400" />
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular">Selecione um colaborador para ver o perfil.</p>
            </div>
        @endif
    </aside>

</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apextree@1/apextree.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
    // ── Dados da hierarquia (PHP → JS) ────────────────────────────────
    const HIERARQUIA = @json($this->hierarquia);

    // ── Mapa de dados dos nós para exportação (nodeId → data) ────────
    const NODE_DATA_MAP = {};

    // ── Paletas de cor ────────────────────────────────────────────────
    const PROFILE_COLORS = {
        ceo          : { bg: '#FEF3C7', border: '#FCD34D', badge: '#F59E0B', text: '#92400E' },
        administrator: { bg: '#FEE2E2', border: '#FCA5A5', badge: '#EF4444', text: '#B91C1C' },
        hr_manager   : { bg: '#F3E8FF', border: '#C4B5FD', badge: '#8B5CF6', text: '#5B21B6' },
        hr           : { bg: '#DBEAFE', border: '#93C5FD', badge: '#3B82F6', text: '#1D4ED8' },
        manager      : { bg: '#D1FAE5', border: '#6EE7B7', badge: '#10B981', text: '#065F46' },
        employee     : { bg: '#F1F5F9', border: '#CBD5E1', badge: '#64748B', text: '#334155' },
    };

    const DEPT_PALETTE = [
        '#6366F1','#8B5CF6','#EC4899','#F59E0B','#10B981','#3B82F6','#14B8A6','#F97316'
    ];

    // ── Instância do grafo ────────────────────────────────────────────
    let orgGraph = null;

    // ── Template de nó customizado ────────────────────────────────────
    function orgNodeTemplate(content) {
        if (!content || typeof content !== 'object') {
            return `<div style="display:flex;align-items:center;justify-content:center;height:100%;padding:8px;font-family:Lato,sans-serif;">${content}</div>`;
        }

        // Nó raiz da empresa
        if (content.tipo === 'root') {
            return `
                <div data-node-id="${content.nodeId || 'root'}" style="display:flex;align-items:center;justify-content:center;gap:8px;height:100%;padding:0 16px;box-sizing:border-box;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span style="font-size:13px;font-weight:800;color:#fff;font-family:Lato,sans-serif;white-space:nowrap;">${content.nome}</span>
                </div>`;
        }

        // Nó de departamento
        if (content.tipo === 'departamento') {
            return `
                <div data-node-id="${content.nodeId}" style="display:flex;align-items:center;gap:10px;height:100%;padding:0 14px;width:100%;box-sizing:border-box;overflow:hidden;">
                    <div style="width:10px;height:10px;border-radius:50%;background:${content.cor};flex-shrink:0;"></div>
                    <span style="font-size:12px;font-weight:700;color:#1E293B;font-family:Lato,sans-serif;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;">${content.nome}</span>
                    <span style="font-size:10px;color:#94A3B8;font-family:Lato,sans-serif;flex-shrink:0;margin-left:4px;">${content.total} pessoas</span>
                </div>`;
        }

        // Nó de colaborador
        const pc = PROFILE_COLORS[content.perfil] || PROFILE_COLORS.employee;
        const avatarHtml = content.avatarUrl
            ? `<img src="${content.avatarUrl}" style="width:38px;height:38px;border-radius:8px;object-fit:cover;border:1.5px solid ${pc.border};flex-shrink:0;" />`
            : `<div style="width:38px;height:38px;border-radius:8px;background:${pc.badge};display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0;font-family:Lato,sans-serif;">${content.iniciais}</div>`;

        return `
            <div data-node-id="${content.nodeId}" style="display:flex;align-items:center;gap:10px;padding:10px 12px;height:100%;box-sizing:border-box;width:100%;overflow:hidden;">
                ${avatarHtml}
                <div style="min-width:0;flex:1;overflow:hidden;">
                    <div style="font-size:12px;font-weight:700;color:#1E293B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-family:Lato,sans-serif;">${content.nome}</div>
                    <div style="font-size:10px;color:${pc.text};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-family:Lato,sans-serif;margin-top:2px;">${content.cargo}</div>
                </div>
            </div>`;
    }

    // ── Converte dado de usuário para nó ApexTree ─────────────────────
    function buildUserNode(user) {
        const pc = PROFILE_COLORS[user.perfil] || PROFILE_COLORS.employee;
        const id   = 'u_' + user.id;
        const data = { tipo: 'usuario', nodeId: id, ...user };
        NODE_DATA_MAP[id] = data;
        return {
            id,
            name    : user.nome,
            data,
            options : {
                nodeBGColor      : pc.bg,
                nodeBGColorHover : pc.bg,
                borderColor      : pc.border,
                borderColorHover : pc.badge,
            },
            children: [],
        };
    }

    // ── Monta a árvore de dados ───────────────────────────────────────
    function buildTreeData() {
        const lideranca = HIERARQUIA.lideranca    || [];
        const depts     = HIERARQUIA.departamentos || [];
        const semDept   = HIERARQUIA.semDept       || [];

        // Monta os nós de departamentos
        const deptNodes = [];
        depts.forEach((dept, i) => {
            const cor      = DEPT_PALETTE[i % DEPT_PALETTE.length];
            const children = [
                ...dept.gerentes.map(u => buildUserNode(u)),
                ...dept.membros.map(u  => buildUserNode(u)),
            ];
            if (children.length === 0) return;

            const deptId   = 'd_' + dept.id;
            const deptData = { tipo: 'departamento', nodeId: deptId, nome: dept.nome, total: dept.total, cor };
            NODE_DATA_MAP[deptId] = deptData;

            deptNodes.push({
                id      : deptId,
                name    : dept.nome,
                data    : deptData,
                options : {
                    nodeBGColor      : '#F8FAFC',
                    nodeBGColorHover : '#F1F5F9',
                    borderColor      : cor,
                    borderColorHover : cor,
                },
                children,
            });
        });

        // Usuários sem departamento
        if (semDept.length > 0) {
            const sdData = { tipo: 'departamento', nodeId: 'sem-dept', nome: 'Sem Departamento', total: semDept.length, cor: '#94A3B8' };
            NODE_DATA_MAP['sem-dept'] = sdData;
            deptNodes.push({
                id      : 'sem-dept',
                name    : 'Sem Departamento',
                data    : sdData,
                options : {
                    nodeBGColor      : '#F8FAFC',
                    borderColor      : '#CBD5E1',
                    borderColorHover : '#94A3B8',
                },
                children: semDept.map(u => buildUserNode(u)),
            });
        }

        const appName   = @json(config('app.name'));
        const rootData  = { tipo: 'root', nodeId: 'root', nome: appName };
        NODE_DATA_MAP['root'] = rootData;

        if (lideranca.length > 0) {
            // O CEO é o nó raiz da árvore — sem nó intermediário da empresa.
            const rootNode = buildUserNode(lideranca[0]);
            rootNode.children = deptNodes;

            if (lideranca.length > 1) {
                const extras = lideranca.slice(1).map(u => buildUserNode(u));
                return {
                    id      : 'root',
                    name    : appName,
                    data    : rootData,
                    options : {
                        nodeBGColor      : '#1E293B',
                        nodeBGColorHover : '#334155',
                        borderColor      : '#0F172A',
                        borderColorHover : '#475569',
                    },
                    children: [rootNode, ...extras],
                };
            }

            return rootNode;
        }

        // Sem CEO: nó empresa como raiz
        return {
            id      : 'root',
            name    : appName,
            data    : rootData,
            options : {
                nodeBGColor      : '#1E293B',
                nodeBGColorHover : '#334155',
                borderColor      : '#0F172A',
                borderColorHover : '#475569',
            },
            children: deptNodes,
        };
    }

    // ── Inicializa / reinicializa o ApexTree ──────────────────────────
    function initOrganograma() {
        const container = document.getElementById('org-tree');
        if (!container) return;

        container.innerHTML = '';
        orgGraph = null;

        const treeData = buildTreeData();

        const tree = new ApexTree(container, {
            width          : '100%',
            height         : 620,
            nodeWidth      : 190,
            nodeHeight     : 72,
            childrenSpacing: 70,
            siblingSpacing : 20,
            direction      : 'top',
            edgeStyle      : 'curved',
            edgeColor      : '#CBD5E1',
            edgeColorHover : '#94A3B8',
            edgeWidth      : 1.5,
            borderRadius   : '12px',
            enableToolbar  : true,
            enableExpandCollapse: true,
            enableAnimation: true,
            contentKey     : 'data',
            nodeTemplate   : orgNodeTemplate,
            onNodeClick    : (node) => {
                if (node.data && node.data.tipo === 'usuario' && node.data.id) {
                    @this.abrirPerfil(node.data.id);
                }
            },
        });

        orgGraph = tree.render(treeData);
    }

    // ── Ajustar à tela ────────────────────────────────────────────────
    function fitOrganograma() {
        if (orgGraph && typeof orgGraph.fitScreen === 'function') {
            orgGraph.fitScreen();
        }
    }

    // ── Exportação — Canvas 2D direto (sem bibliotecas de screenshot) ──

    /** Desenha um retângulo arredondado (polyfill para browsers antigos). */
    function drawRoundRect(ctx, x, y, w, h, r) {
        if (typeof ctx.roundRect === 'function') {
            ctx.roundRect(x, y, w, h, r);
        } else {
            ctx.moveTo(x + r, y);
            ctx.lineTo(x + w - r, y);
            ctx.quadraticCurveTo(x + w, y,     x + w, y + r);
            ctx.lineTo(x + w, y + h - r);
            ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
            ctx.lineTo(x + r, y + h);
            ctx.quadraticCurveTo(x,     y + h, x,         y + h - r);
            ctx.lineTo(x, y + r);
            ctx.quadraticCurveTo(x, y,         x + r, y);
            ctx.closePath();
        }
    }

    /** Desenha avatar de iniciais (fallback quando não há foto). */
    function drawInitialsAvatar(ctx, initials, bgColor, x, y, size) {
        ctx.save();
        ctx.beginPath();
        drawRoundRect(ctx, x, y, size, size, 8);
        ctx.fillStyle = bgColor;
        ctx.fill();
        ctx.restore();

        ctx.save();
        ctx.fillStyle = '#ffffff';
        ctx.font = `bold ${Math.round(size * 0.34)}px Lato, sans-serif`;
        ctx.textAlign    = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(initials || '?', x + size / 2, y + size / 2);
        ctx.restore();
    }

    /** Trunca texto para caber em maxWidth pixels. */
    function truncate(ctx, text, maxWidth) {
        if (ctx.measureText(text).width <= maxWidth) return text;
        let t = text;
        while (t.length > 0 && ctx.measureText(t + '…').width > maxWidth) t = t.slice(0, -1);
        return t + '…';
    }

    /**
     * Desenha um cartão de nó diretamente no canvas.
     * Recebe coordenadas em CSS pixels (o ctx já está scaled).
     */
    async function drawNodeCard(ctx, data, x, y, w, h) {
        const r = 12;

        // ── Nó root (empresa) ──────────────────────────────────────────
        if (data.tipo === 'root') {
            ctx.save();
            ctx.beginPath();
            drawRoundRect(ctx, x, y, w, h, r);
            ctx.fillStyle = '#1E293B';
            ctx.fill();
            ctx.restore();

            ctx.save();
            ctx.fillStyle    = '#ffffff';
            ctx.font         = 'bold 13px Lato, sans-serif';
            ctx.textAlign    = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(truncate(ctx, data.nome, w - 32), x + w / 2, y + h / 2);
            ctx.restore();
            return;
        }

        // ── Nó departamento ────────────────────────────────────────────
        if (data.tipo === 'departamento') {
            const cor = data.cor || '#94A3B8';

            ctx.save();
            ctx.beginPath();
            drawRoundRect(ctx, x, y, w, h, r);
            ctx.fillStyle   = '#F8FAFC';
            ctx.fill();
            ctx.strokeStyle = cor;
            ctx.lineWidth   = 1.5;
            ctx.stroke();
            ctx.restore();

            // Ponto colorido
            ctx.save();
            ctx.beginPath();
            ctx.arc(x + 20, y + h / 2, 5, 0, Math.PI * 2);
            ctx.fillStyle = cor;
            ctx.fill();
            ctx.restore();

            // Nome do departamento
            ctx.save();
            ctx.fillStyle    = '#1E293B';
            ctx.font         = 'bold 12px Lato, sans-serif';
            ctx.textBaseline = 'middle';
            const countText  = data.total + ' pessoas';
            const countW     = ctx.measureText(countText).width;
            const nameMaxW   = w - 40 - countW - 16;
            ctx.fillText(truncate(ctx, data.nome, nameMaxW), x + 32, y + h / 2);
            ctx.restore();

            // Contador
            ctx.save();
            ctx.fillStyle    = '#94A3B8';
            ctx.font         = '10px Lato, sans-serif';
            ctx.textBaseline = 'middle';
            ctx.fillText(countText, x + w - countW - 10, y + h / 2);
            ctx.restore();
            return;
        }

        // ── Nó colaborador ─────────────────────────────────────────────
        if (data.tipo === 'usuario') {
            const pc = PROFILE_COLORS[data.perfil] || PROFILE_COLORS.employee;

            // Fundo + borda
            ctx.save();
            ctx.beginPath();
            drawRoundRect(ctx, x, y, w, h, r);
            ctx.fillStyle   = pc.bg;
            ctx.fill();
            ctx.strokeStyle = pc.border;
            ctx.lineWidth   = 1.5;
            ctx.stroke();
            ctx.restore();

            // Avatar (38×38, 12px da borda esquerda, centrado verticalmente)
            const avSize = 38;
            const avX    = x + 12;
            const avY    = y + (h - avSize) / 2;

            if (data.avatarUrl) {
                await new Promise(resolve => {
                    const img    = new Image();
                    img.crossOrigin = 'anonymous';
                    const done   = () => resolve();
                    img.onload  = () => {
                        ctx.save();
                        ctx.beginPath();
                        drawRoundRect(ctx, avX, avY, avSize, avSize, 8);
                        ctx.clip();
                        ctx.drawImage(img, avX, avY, avSize, avSize);
                        ctx.restore();
                        // Borda fina sobre a foto
                        ctx.save();
                        ctx.beginPath();
                        drawRoundRect(ctx, avX, avY, avSize, avSize, 8);
                        ctx.strokeStyle = pc.border;
                        ctx.lineWidth   = 1.5;
                        ctx.stroke();
                        ctx.restore();
                        done();
                    };
                    img.onerror = () => {
                        drawInitialsAvatar(ctx, data.iniciais, pc.badge, avX, avY, avSize);
                        done();
                    };
                    img.src = data.avatarUrl;
                });
            } else {
                drawInitialsAvatar(ctx, data.iniciais, pc.badge, avX, avY, avSize);
            }

            // Texto
            const textX    = avX + avSize + 10;
            const textMaxW = x + w - 10 - textX;
            const midY     = y + h / 2;

            // Nome
            ctx.save();
            ctx.fillStyle    = '#1E293B';
            ctx.font         = 'bold 12px Lato, sans-serif';
            ctx.textBaseline = 'bottom';
            ctx.fillText(truncate(ctx, data.nome  || '', textMaxW), textX, midY - 1);
            ctx.restore();

            // Cargo
            ctx.save();
            ctx.fillStyle    = pc.text;
            ctx.font         = '10px Lato, sans-serif';
            ctx.textBaseline = 'top';
            ctx.fillText(truncate(ctx, data.cargo || 'Sem cargo', textMaxW), textX, midY + 3);
            ctx.restore();
        }
    }

    /**
     * Captura o organograma usando Canvas 2D puro.
     *
     * Passo 1 — aresta: clona o SVG, remove todos os <foreignObject> (que causam
     *   canvas tainted no Chrome) e desenha apenas as linhas de conexão.
     * Passo 2 — nós: itera sobre cada <foreignObject> no DOM, obtém posição real
     *   via getBoundingClientRect(), lê os dados de NODE_DATA_MAP e desenha o
     *   cartão com Canvas 2D (sem nenhuma biblioteca externa → sem taint).
     */
    async function capturarCanvas(escala = 2) {
        const container = document.getElementById('org-tree');
        if (!container) return null;

        // Ajusta e aguarda animação
        if (orgGraph && typeof orgGraph.fitScreen === 'function') {
            orgGraph.fitScreen();
            await new Promise(r => setTimeout(r, 500));
        }

        const svg = container.querySelector('svg');
        if (!svg) return null;

        const cRect = container.getBoundingClientRect();
        const W     = Math.round(cRect.width);
        const H     = Math.round(cRect.height);

        // Canvas de saída
        const canvas    = document.createElement('canvas');
        canvas.width    = W * escala;
        canvas.height   = H * escala;
        const ctx       = canvas.getContext('2d');
        ctx.scale(escala, escala);

        // Fundo branco
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, W, H);

        // ── Passo 1: arestas (SVG sem foreignObject) ──────────────────
        const svgClone  = svg.cloneNode(true);
        svgClone.querySelectorAll('foreignObject').forEach(el => el.remove());
        const svgRect   = svg.getBoundingClientRect();
        svgClone.setAttribute('width',  svgRect.width);
        svgClone.setAttribute('height', svgRect.height);

        const svgStr  = new XMLSerializer().serializeToString(svgClone);
        const svgBlob = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
        const svgUrl  = URL.createObjectURL(svgBlob);

        await new Promise((resolve, reject) => {
            const img    = new Image();
            img.onload  = () => {
                ctx.drawImage(
                    img,
                    svgRect.left - cRect.left,
                    svgRect.top  - cRect.top,
                    svgRect.width,
                    svgRect.height
                );
                URL.revokeObjectURL(svgUrl);
                resolve();
            };
            img.onerror = (e) => { URL.revokeObjectURL(svgUrl); reject(e); };
            img.src = svgUrl;
        });

        // ── Passo 2: cartões dos nós (Canvas 2D puro) ─────────────────
        for (const fo of svg.querySelectorAll('foreignObject')) {
            const foRect  = fo.getBoundingClientRect();
            const cx      = foRect.left - cRect.left;
            const cy      = foRect.top  - cRect.top;
            const cw      = foRect.width;
            const ch      = foRect.height;

            const rootDiv = fo.querySelector('[data-node-id]');
            const nodeId  = rootDiv ? rootDiv.getAttribute('data-node-id') : null;
            const data    = nodeId  ? NODE_DATA_MAP[nodeId] : null;

            if (!data) continue;
            await drawNodeCard(ctx, data, cx, cy, cw, ch);
        }

        return canvas;
    }

    async function exportarPNG() {
        try {
            const canvas = await capturarCanvas(2);
            if (!canvas) return;
            const link      = document.createElement('a');
            link.download   = 'organograma.png';
            link.href       = canvas.toDataURL('image/png');
            link.click();
        } catch (err) {
            console.error('Erro ao exportar PNG:', err);
        }
    }

    async function exportarPDF() {
        try {
            const canvas    = await capturarCanvas(1.5);
            if (!canvas) return;
            const imgData   = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const landscape = canvas.width > canvas.height;
            const pdf       = new jsPDF({
                orientation: landscape ? 'landscape' : 'portrait',
                unit  : 'px',
                format: [canvas.width, canvas.height],
            });
            pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
            pdf.save('organograma.pdf');
        } catch (err) {
            console.error('Erro ao exportar PDF:', err);
        }
    }

    // ── Init ──────────────────────────────────────────────────────────
    initOrganograma();

    // Reinicializa ao navegar com Livewire (modo SPA)
    document.addEventListener('livewire:navigated', initOrganograma);
    </script>
@endpush
