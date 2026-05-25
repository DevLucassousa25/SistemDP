<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 py-10 px-4">

    {{-- ── Topo / Branding ──────────────────────────────────────────── --}}
    <div class="max-w-2xl mx-auto mb-8 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg mb-4">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Candidate-se</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Preencha o formulário e envie seu currículo. Entraremos em contato em breve.</p>
    </div>

    {{-- ── Tela de sucesso ─────────────────────────────────────────── --}}
    @if ($enviado)
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl p-10 text-center">
            <div class="w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-xl font-black text-slate-800 dark:text-white mb-2">Candidatura enviada!</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed max-w-md mx-auto">
                Recebemos seus dados com sucesso. Nossa equipe de RH irá analisar seu perfil e entrar em contato pelo e-mail <strong class="text-slate-700 dark:text-slate-300">{{ $email }}</strong>.
            </p>
            @if ($vagaId && $this->vagaSelecionada)
            <div class="mt-6 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 text-sm text-indigo-700 dark:text-indigo-300 font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                Inscrito em: {{ $this->vagaSelecionada->titulo }}
            </div>
            @endif
            <p class="text-xs text-slate-400 mt-6">Você pode fechar esta página.</p>
        </div>
    </div>

    @else

    {{-- ── Progress bar ─────────────────────────────────────────────── --}}
    <div class="max-w-2xl mx-auto mb-6">
        <div class="flex items-center gap-3">
            @foreach ([1 => 'Dados pessoais', 2 => 'Perfil', 3 => 'Vaga & CV'] as $n => $label)
            <div class="flex-1 flex flex-col items-center gap-1.5">
                <div class="flex items-center w-full">
                    @if ($n > 1)<div class="flex-1 h-0.5 {{ $step >= $n ? 'bg-indigo-400' : 'bg-slate-200 dark:bg-slate-700' }} transition-all duration-300"></div>@endif
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shrink-0 transition-all duration-300
                        {{ $step > $n ? 'bg-green-500 text-white' : ($step === $n ? 'bg-indigo-600 text-white ring-4 ring-indigo-200 dark:ring-indigo-900' : 'bg-slate-200 dark:bg-slate-700 text-slate-400') }}">
                        @if ($step > $n)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @else
                        {{ $n }}
                        @endif
                    </div>
                    @if ($n < 3)<div class="flex-1 h-0.5 {{ $step > $n ? 'bg-indigo-400' : 'bg-slate-200 dark:bg-slate-700' }} transition-all duration-300"></div>@endif
                </div>
                <span class="text-[10px] font-bold {{ $step === $n ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }} uppercase tracking-wider">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Card principal ───────────────────────────────────────────── --}}
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">

            {{-- ════ PASSO 1: Dados pessoais ════ --}}
            @if ($step === 1)
            <div class="px-8 py-7 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-r from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/20">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-4.5 h-4.5 text-white w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-800 dark:text-white">Dados pessoais</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Suas informações de contato</p>
                    </div>
                </div>
            </div>
            <div class="px-8 py-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Nome completo <span class="text-red-400">*</span></label>
                        <input type="text" wire:model="nome" placeholder="Seu nome completo"
                               class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition" />
                        @error('nome') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">E-mail <span class="text-red-400">*</span></label>
                        <input type="email" wire:model="email" placeholder="seu@email.com"
                               class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition" />
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Telefone / WhatsApp</label>
                        <input type="text" inputmode="tel"
                               placeholder="(00) 00000-0000"
                               :value="$wire.telefone"
                               x-on:input="
                                   let v = $event.target.value.replace(/\D/g,'').slice(0,11);
                                   let f = '';
                                   if (v.length > 0) f = '(' + v.slice(0,2);
                                   if (v.length > 2) f += ') ' + v.slice(2, v.length > 6 ? 7 : undefined);
                                   if (v.length > 7) f += '-' + v.slice(7);
                                   $event.target.value = f;
                                   $wire.telefone = f;
                               "
                               class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Cidade</label>
                        <input type="text" wire:model="cidade" placeholder="Ex: São Paulo"
                               class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Estado (UF)</label>
                        <select wire:model="estado"
                                class="cursor-pointer w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition appearance-none">
                            <option value="">Selecione</option>
                            @foreach (['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                            <option value="{{ $uf }}">{{ $uf }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Data de nascimento</label>
                        <input type="date" wire:model="dataNasc"
                               class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition" />
                    </div>
                </div>
            </div>
            @endif

            {{-- ════ PASSO 2: Perfil profissional ════ --}}
            @if ($step === 2)
            <div class="px-8 py-7 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-r from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-violet-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-800 dark:text-white">Perfil profissional</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Sua formação e experiência</p>
                    </div>
                </div>
            </div>
            <div class="px-8 py-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Escolaridade</label>
                        <select wire:model="escolaridade"
                                class="cursor-pointer w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition appearance-none">
                            <option value="">Selecione</option>
                            <option value="fundamental">Ensino Fundamental</option>
                            <option value="medio">Ensino Médio</option>
                            <option value="tecnico">Técnico</option>
                            <option value="graduacao">Graduação</option>
                            <option value="pos_graduacao">Pós-graduação</option>
                            <option value="mestrado">Mestrado</option>
                            <option value="doutorado">Doutorado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Área de interesse</label>
                        <input type="text" wire:model="areaInteresse" placeholder="Ex: Desenvolvimento de Software"
                               class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Pretensão salarial (R$)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-medium pointer-events-none select-none">R$</span>
                            <input type="text" inputmode="numeric"
                                   placeholder="0,00"
                                   :value="$wire.pretensaoSalarial
                                       ? Number($wire.pretensaoSalarial).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                                       : ''"
                                   x-on:input="
                                       let digits = $event.target.value.replace(/\D/g,'');
                                       let cents = parseInt(digits || '0');
                                       let reais = cents / 100;
                                       let f = reais > 0
                                           ? reais.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                                           : '';
                                       $event.target.value = f;
                                       $wire.pretensaoSalarial = reais > 0 ? reais : '';
                                   "
                                   class="w-full pl-10 pr-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition" />
                        </div>
                    </div>
                    <div class="sm:col-span-2" x-data="{ chars: {{ mb_strlen($resumo) }} }">
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Resumo profissional</label>
                        <textarea wire:model="resumo" rows="5"
                                  x-on:input="chars = $event.target.value.length"
                                  placeholder="Conte um pouco sobre sua trajetória, habilidades e objetivos profissionais..."
                                  class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition resize-none leading-relaxed"></textarea>
                        <p class="mt-1 text-[10px] text-right transition-colors"
                           :class="chars > 2800 ? (chars >= 3000 ? 'text-red-500 font-semibold' : 'text-amber-500') : 'text-slate-400'">
                            <span x-text="chars"></span>/3000
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- ════ PASSO 3: Vaga & CV ════ --}}
            @if ($step === 3)
            <div class="px-8 py-7 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-800 dark:text-white">Vaga & Currículo</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Escolha a vaga e envie seu arquivo</p>
                    </div>
                </div>
            </div>
            <div class="px-8 py-6 space-y-6">

                {{-- Seleção de vaga --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-3">
                        Vaga de interesse
                        <span class="ml-1 text-[10px] font-normal text-slate-400">(opcional — ou candidatura espontânea)</span>
                    </label>

                    @if ($this->vagasPublicadas->isEmpty())
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-amber-700 dark:text-amber-400">Nenhuma vaga aberta no momento. Sua candidatura será mantida no banco de talentos.</p>
                    </div>
                    @else
                    <div x-data="{
                            busca: '',
                            get vagas() {
                                if (!this.busca.trim()) return null; // null = mostrar tudo
                                const q = this.busca.toLowerCase().trim();
                                return q;
                            }
                        }"
                        class="space-y-3">

                        {{-- Busca (só aparece se tiver mais de 4 vagas) --}}
                        @if ($this->vagasPublicadas->count() > 4)
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" x-model="busca" placeholder="Buscar vaga por título, cargo ou cidade..."
                                   class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition" />
                            <template x-if="busca.trim()">
                                <button type="button" x-on:click="busca = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </template>
                        </div>
                        @endif

                        {{-- Lista de vagas com scroll --}}
                        <div class="space-y-2 max-h-72 overflow-y-auto pr-0.5 scroll-smooth"
                             style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">

                            {{-- Candidatura espontânea --}}
                            <label x-show="!busca.trim()"
                                   class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-xl border transition
                                {{ is_null($vagaId) ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-700 hover:border-indigo-200 dark:hover:border-indigo-700' }}">
                                <input type="radio" wire:model.live="vagaId" value="" class="sr-only" />
                                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Candidatura espontânea</p>
                                    <p class="text-[11px] text-slate-400">Entrarei no banco de talentos</p>
                                </div>
                                @if (is_null($vagaId))
                                <svg class="w-5 h-5 text-indigo-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @endif
                            </label>

                            {{-- Vagas --}}
                            @foreach ($this->vagasPublicadas as $vaga)
                            @php
                                $vagaColors = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500'];
                                $vc = $vagaColors[abs(crc32($vaga->titulo)) % count($vagaColors)];
                                $searchText = strtolower($vaga->titulo . ' ' . $vaga->cargo . ' ' . $vaga->cidade . ' ' . $vaga->modalidade);
                            @endphp
                            <label
                                x-show="!busca.trim() || '{{ $searchText }}'.includes(busca.toLowerCase().trim())"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-xl border transition
                                {{ (string)$vagaId === (string)$vaga->id ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-700 hover:border-indigo-200 dark:hover:border-indigo-700' }}">
                                <input type="radio" wire:model.live="vagaId" value="{{ $vaga->id }}" class="sr-only" />
                                <div class="w-8 h-8 rounded-lg {{ $vc }} flex items-center justify-center shrink-0 text-white text-xs font-black">
                                    {{ strtoupper(substr($vaga->titulo, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200 truncate">{{ $vaga->titulo }}</p>
                                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                        @if ($vaga->cargo)
                                        <span class="text-[10px] text-slate-400">{{ $vaga->cargo }}</span>
                                        @endif
                                        @if ($vaga->modalidade)
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-500">
                                            {{ ['presencial'=>'Presencial','remoto'=>'Remoto','hibrido'=>'Híbrido'][$vaga->modalidade] ?? $vaga->modalidade }}
                                        </span>
                                        @endif
                                        @if ($vaga->cidade)
                                        <span class="text-[10px] text-slate-400">{{ $vaga->cidade }}/{{ $vaga->estado }}</span>
                                        @endif
                                        @if ($vaga->salario_min || $vaga->salario_max)
                                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">
                                            R$ {{ number_format($vaga->salario_min ?? $vaga->salario_max, 0, ',', '.') }}{{ $vaga->salario_min && $vaga->salario_max ? ' – '.number_format($vaga->salario_max, 0, ',', '.') : '' }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @if ((string)$vagaId === (string)$vaga->id)
                                <svg class="w-5 h-5 text-indigo-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @endif
                            </label>
                            @endforeach

                            {{-- Sem resultados --}}
                            <div x-cloak
                                 x-show="busca.trim() && $el.parentElement.querySelectorAll('label[x-show]:not([style*=\'display: none\'])').length === 0"
                                 class="py-8 text-center">
                                <svg class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-sm text-slate-400">Nenhuma vaga encontrada para <strong x-text="'&quot;' + busca + '&quot;'" class="text-slate-500"></strong></p>
                            </div>

                        </div>

                        {{-- Contador --}}
                        <p class="text-[10px] text-slate-400 text-right">
                            {{ $this->vagasPublicadas->count() }} {{ $this->vagasPublicadas->count() === 1 ? 'vaga disponível' : 'vagas disponíveis' }}
                        </p>

                    </div>
                    @endif
                </div>

                {{-- Upload CV --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-2">
                        Currículo (PDF, DOC, DOCX — máx. 5 MB)
                        <span class="ml-1 text-[10px] font-normal text-slate-400">(opcional)</span>
                    </label>
                    <label class="cursor-pointer flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl hover:border-indigo-400 dark:hover:border-indigo-500 bg-slate-50 dark:bg-slate-900 transition group">
                        <input type="file" wire:model="arquivo" accept=".pdf,.doc,.docx" class="sr-only" />
                        @if ($arquivo)
                        <div class="flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ $arquivo->getClientOriginalName() }}
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Clique para trocar o arquivo</p>
                        @else
                        <svg class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2 group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Arraste ou <span class="text-indigo-600 dark:text-indigo-400 font-semibold">clique para selecionar</span></p>
                        <p class="text-xs text-slate-400 mt-1">PDF, DOC, DOCX até 5 MB</p>
                        @endif
                    </label>
                    @error('arquivo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

            </div>
            @endif

            {{-- ── Footer de navegação ───────────────────────────────────── --}}
            <div class="px-8 py-5 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                @if ($step > 1)
                <button wire:click="voltarPasso" type="button"
                        class="cursor-pointer flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Voltar
                </button>
                @else
                <div></div>
                @endif

                @if ($step < 3)
                <button wire:click="proximoPasso" type="button"
                        class="cursor-pointer flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold transition shadow-sm">
                    Próximo
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                @else
                <button wire:click="enviar" type="button"
                        wire:loading.attr="disabled" wire:target="enviar"
                        class="cursor-pointer flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-sm font-bold transition shadow-sm disabled:opacity-60">
                    <span wire:loading.remove wire:target="enviar" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Enviar candidatura
                    </span>
                    <span wire:loading wire:target="enviar">Enviando...</span>
                </button>
                @endif
            </div>

        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            Seus dados são tratados com segurança e utilizados apenas para fins de recrutamento.
        </p>
    </div>

    @endif

</div>
