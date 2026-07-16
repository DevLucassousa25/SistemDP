<div class="min-h-screen relative overflow-hidden"
     style="background: linear-gradient(135deg, #f0f4ff 0%, #fafafe 50%, #f5f0ff 100%);">

    {{-- Blobs decorativos de fundo --}}
    <div class="pointer-events-none fixed inset-0 overflow-hidden -z-10">
        <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full opacity-30"
             style="background: radial-gradient(circle, #818cf8, transparent 70%); filter: blur(60px);"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full opacity-20"
             style="background: radial-gradient(circle, #a78bfa, transparent 70%); filter: blur(60px);"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full opacity-10"
             style="background: radial-gradient(circle, #6366f1, transparent 70%); filter: blur(80px);"></div>
    </div>

    <div class="relative py-12 px-4">

    {{-- ── Topo / Branding ──────────────────────────────────────────── --}}
    <div class="max-w-xl mx-auto mb-10 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-xl mb-5"
             style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">Candidate-se</h1>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">
            Preencha o formulário e envie seu currículo.<br class="hidden sm:block"> Entraremos em contato em breve.
        </p>
    </div>

    {{-- ── Tela de sucesso ─────────────────────────────────────────── --}}
    @if ($enviado)
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl p-12 text-center border border-slate-100"
             style="box-shadow: 0 25px 60px -15px rgba(99,102,241,.18);">
            <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6"
                 style="background: linear-gradient(135deg, #dcfce7, #bbf7d0);">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-2xl font-black text-slate-800 mb-3">Candidatura enviada!</h2>
            <p class="text-slate-500 text-sm leading-relaxed max-w-sm mx-auto">
                Recebemos seus dados com sucesso. Nossa equipe de RH irá analisar seu perfil e entrar em contato pelo e-mail
                <strong class="text-indigo-600">{{ $email }}</strong>.
            </p>
            @if ($vagaId && $this->vagaSelecionada)
            <div class="mt-6 inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-sm font-semibold text-indigo-700"
                 style="background: linear-gradient(135deg, #eef2ff, #ede9fe); border: 1px solid #c7d2fe;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Inscrito em: {{ $this->vagaSelecionada->titulo }}
            </div>
            @endif
            <p class="text-xs text-slate-400 mt-8">Você pode fechar esta página.</p>
        </div>
    </div>

    @else

    {{-- ── Stepper ─────────────────────────────────────────────────── --}}
    <div class="max-w-xl mx-auto mb-8">
        <div class="flex items-center">
            @foreach ([1 => 'Dados pessoais', 2 => 'Perfil', 3 => 'Vaga & CV'] as $n => $label)
            {{-- Linha antes --}}
            @if ($n > 1)
            <div class="flex-1 h-0.5 mx-1 rounded-full transition-all duration-500
                {{ $step >= $n ? 'bg-gradient-to-r from-indigo-400 to-violet-400' : 'bg-slate-200' }}"></div>
            @endif

            {{-- Círculo --}}
            <div class="flex flex-col items-center gap-1.5 shrink-0">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300
                    {{ $step > $n
                        ? 'bg-green-500 text-white shadow-lg shadow-green-200'
                        : ($step === $n
                            ? 'text-white shadow-lg shadow-indigo-300'
                            : 'bg-slate-100 text-slate-400') }}"
                     @if($step === $n) style="background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 0 0 4px rgba(99,102,241,.18);" @endif>
                    @if ($step > $n)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        {{ $n }}
                    @endif
                </div>
                <span class="text-[10px] font-bold tracking-wider uppercase transition-colors duration-300
                    {{ $step === $n ? 'text-indigo-600' : ($step > $n ? 'text-green-600' : 'text-slate-400') }}">
                    {{ $label }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Card principal ───────────────────────────────────────────── --}}
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden"
             style="box-shadow: 0 25px 60px -15px rgba(99,102,241,.15), 0 4px 16px -4px rgba(0,0,0,.06);">

            {{-- ════ PASSO 1: Dados pessoais ════ --}}
            @if ($step === 1)

            {{-- Header do passo --}}
            <div class="px-8 py-6 relative overflow-hidden"
                 style="background: linear-gradient(135deg, #eef2ff 0%, #ede9fe 100%);">
                <div class="absolute right-0 top-0 w-48 h-24 opacity-10"
                     style="background: radial-gradient(circle at 100% 0%, #6366f1, transparent 70%);"></div>
                <div class="flex items-center gap-3.5 relative">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 shadow-md"
                         style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-800">Dados pessoais</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Suas informações de contato</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-7 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Nome --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            Nome completo <span class="text-red-400">*</span>
                        </label>
                        <input type="text" wire:model="nome" placeholder="Seu nome completo"
                               class="w-full px-4 py-3 text-sm border border-slate-200 rounded-2xl bg-slate-50
                                      text-slate-700 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-300/50 focus:border-indigo-400
                                      focus:bg-white transition-all" />
                        @error('nome') <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            E-mail <span class="text-red-400">*</span>
                        </label>
                        <input type="email" wire:model="email" placeholder="seu@email.com"
                               class="w-full px-4 py-3 text-sm border border-slate-200 rounded-2xl bg-slate-50
                                      text-slate-700 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-300/50 focus:border-indigo-400
                                      focus:bg-white transition-all" />
                        @error('email') <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}</p> @enderror
                    </div>

                    {{-- Telefone --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Telefone / WhatsApp</label>
                        <input type="text" inputmode="tel" placeholder="(00) 00000-0000"
                               :value="$wire.telefone"
                               x-on:input="
                                   let v = $event.target.value.replace(/\D/g,'').slice(0,11);
                                   let f = '';
                                   if (v.length > 0) f = '(' + v.slice(0,2);
                                   if (v.length > 2) f += ') ' + v.slice(2, v.length > 6 ? 7 : undefined);
                                   if (v.length > 7) f += '-' + v.slice(7);
                                   $event.target.value = f; $wire.telefone = f;
                               "
                               class="w-full px-4 py-3 text-sm border border-slate-200 rounded-2xl bg-slate-50
                                      text-slate-700 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-300/50 focus:border-indigo-400
                                      focus:bg-white transition-all" />
                    </div>

                    {{-- Cidade --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Cidade</label>
                        <input type="text" wire:model="cidade" placeholder="Ex: São Paulo"
                               class="w-full px-4 py-3 text-sm border border-slate-200 rounded-2xl bg-slate-50
                                      text-slate-700 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-300/50 focus:border-indigo-400
                                      focus:bg-white transition-all" />
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Estado (UF)</label>
                        <div class="relative">
                            <select wire:model="estado"
                                    class="cursor-pointer w-full px-4 py-3 text-sm border border-slate-200 rounded-2xl bg-slate-50
                                           text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300/50
                                           focus:border-indigo-400 focus:bg-white transition-all appearance-none">
                                <option value="">Selecione</option>
                                @foreach (['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                <option value="{{ $uf }}">{{ $uf }}</option>
                                @endforeach
                            </select>
                            <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Data de nascimento --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Data de nascimento</label>
                        <input type="date" wire:model="dataNasc"
                               class="w-full px-4 py-3 text-sm border border-slate-200 rounded-2xl bg-slate-50
                                      text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300/50
                                      focus:border-indigo-400 focus:bg-white transition-all" />
                    </div>

                </div>
            </div>
            @endif

            {{-- ════ PASSO 2: Perfil profissional ════ --}}
            @if ($step === 2)

            {{-- Header do passo --}}
            <div class="px-8 py-6 relative overflow-hidden"
                 style="background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);">
                <div class="absolute right-0 top-0 w-48 h-full opacity-10"
                     style="background: radial-gradient(circle at 100% 50%, white, transparent 70%);"></div>
                <div class="flex items-center gap-3.5 relative">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-white">Perfil profissional</h2>
                        <p class="text-[11px] text-blue-100 mt-0.5">Sua formação e experiência</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-7 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Escolaridade --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Escolaridade</label>
                        <div class="relative">
                            <select wire:model="escolaridade"
                                    class="cursor-pointer w-full px-4 py-3 text-sm border border-slate-200 rounded-2xl bg-slate-50
                                           text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300/50
                                           focus:border-indigo-400 focus:bg-white transition-all appearance-none">
                                <option value="">Selecione</option>
                                <option value="fundamental">Ensino Fundamental</option>
                                <option value="medio">Ensino Médio</option>
                                <option value="tecnico">Técnico</option>
                                <option value="graduacao">Graduação</option>
                                <option value="pos_graduacao">Pós-graduação</option>
                                <option value="mestrado">Mestrado</option>
                                <option value="doutorado">Doutorado</option>
                            </select>
                            <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Área de interesse --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Área de interesse</label>
                        <input type="text" wire:model="areaInteresse" placeholder="Ex: Desenvolvimento de Software"
                               class="w-full px-4 py-3 text-sm border border-slate-200 rounded-2xl bg-slate-50
                                      text-slate-700 placeholder-slate-300
                                      focus:outline-none focus:ring-2 focus:ring-indigo-300/50 focus:border-indigo-400
                                      focus:bg-white transition-all" />
                    </div>

                    {{-- Pretensão salarial --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Pretensão salarial (R$)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-semibold pointer-events-none select-none">R$</span>
                            <input type="text" inputmode="numeric" placeholder="0,00"
                                   :value="$wire.pretensaoSalarial
                                       ? Number($wire.pretensaoSalarial).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                                       : ''"
                                   x-on:input="
                                       let digits = $event.target.value.replace(/\D/g,'');
                                       let cents = parseInt(digits || '0');
                                       let reais = cents / 100;
                                       let f = reais > 0 ? reais.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
                                       $event.target.value = f;
                                       $wire.pretensaoSalarial = reais > 0 ? reais : '';
                                   "
                                   class="w-full pl-10 pr-4 py-3 text-sm border border-slate-200 rounded-2xl bg-slate-50
                                          text-slate-700 placeholder-slate-300
                                          focus:outline-none focus:ring-2 focus:ring-indigo-300/50 focus:border-indigo-400
                                          focus:bg-white transition-all" />
                        </div>
                    </div>

                    {{-- Resumo profissional --}}
                    <div class="sm:col-span-2" x-data="{ chars: {{ mb_strlen($resumo) }} }">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Resumo profissional</label>
                        <textarea wire:model="resumo" rows="5"
                                  x-on:input="chars = $event.target.value.length"
                                  placeholder="Conte um pouco sobre sua trajetória, habilidades e objetivos profissionais..."
                                  class="w-full px-4 py-3 text-sm border border-slate-200 rounded-2xl bg-slate-50
                                         text-slate-700 placeholder-slate-300
                                         focus:outline-none focus:ring-2 focus:ring-indigo-300/50 focus:border-indigo-400
                                         focus:bg-white transition-all resize-none leading-relaxed"></textarea>
                        <p class="mt-1.5 text-[10px] text-right transition-colors"
                           :class="chars > 2800 ? (chars >= 3000 ? 'text-red-500 font-semibold' : 'text-amber-500') : 'text-slate-400'">
                            <span x-text="chars"></span>/3000
                        </p>
                    </div>

                </div>
            </div>
            @endif

            {{-- ════ PASSO 3: Vaga & CV ════ --}}
            @if ($step === 3)

            {{-- Header do passo --}}
            <div class="px-8 py-6 relative overflow-hidden"
                 style="background: linear-gradient(135deg, #eef2ff 0%, #dbeafe 100%);">
                <div class="absolute right-0 top-0 w-48 h-24 opacity-10"
                     style="background: radial-gradient(circle at 100% 0%, #3b82f6, transparent 70%);"></div>
                <div class="flex items-center gap-3.5 relative">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 shadow-md"
                         style="background: linear-gradient(135deg, #3b82f6, #6366f1);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-800">Vaga & Currículo</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Escolha a vaga e envie seu arquivo</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-7 space-y-7">

                {{-- Seleção de vaga --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-xs font-bold text-slate-600">Vaga de interesse</label>
                        <span class="text-[10px] font-normal text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                            opcional — ou candidatura espontânea
                        </span>
                    </div>

                    @if ($this->vagasPublicadas->isEmpty())
                    <div class="flex items-center gap-3 px-4 py-3.5 rounded-2xl bg-amber-50 border border-amber-200">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-amber-700">Nenhuma vaga aberta no momento. Sua candidatura será mantida no banco de talentos.</p>
                    </div>
                    @else
                    <div x-data="{ busca: '' }" class="space-y-3">

                        @if ($this->vagasPublicadas->count() > 4)
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" x-model="busca" placeholder="Buscar por título, cargo ou cidade..."
                                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-2xl bg-slate-50
                                          text-slate-700 placeholder-slate-300
                                          focus:outline-none focus:ring-2 focus:ring-indigo-300/50 focus:border-indigo-400
                                          focus:bg-white transition-all" />
                            <template x-if="busca.trim()">
                                <button type="button" x-on:click="busca = ''"
                                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </template>
                        </div>
                        @endif

                        <div class="space-y-2 max-h-72 overflow-y-auto pr-0.5"
                             style="scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;">

                            {{-- Candidatura espontânea --}}
                            <label x-show="!busca.trim()"
                                   class="cursor-pointer group flex items-center gap-3.5 px-4 py-3.5 rounded-2xl border-2 transition-all
                                {{ is_null($vagaId) ? 'border-indigo-400 bg-indigo-50' : 'border-slate-200 hover:border-indigo-200 hover:bg-slate-50' }}">
                                <input type="radio" wire:model.live="vagaId" value="" class="sr-only" />
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-all
                                    {{ is_null($vagaId) ? 'bg-indigo-100' : 'bg-slate-100 group-hover:bg-indigo-50' }}">
                                    <svg class="w-4.5 h-4.5 {{ is_null($vagaId) ? 'text-indigo-600' : 'text-slate-400' }}"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold {{ is_null($vagaId) ? 'text-indigo-700' : 'text-slate-700' }}">
                                        Candidatura espontânea
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Entrarei no banco de talentos</p>
                                </div>
                                @if (is_null($vagaId))
                                <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0" style="background:#6366f1;">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                @endif
                            </label>

                            {{-- Vagas --}}
                            @foreach ($this->vagasPublicadas as $vaga)
                            @php
                                $vagaColors = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500'];
                                $vc = $vagaColors[abs(crc32($vaga->titulo)) % count($vagaColors)];
                                $searchText = strtolower($vaga->titulo . ' ' . $vaga->cargo . ' ' . $vaga->cidade . ' ' . $vaga->modalidade);
                                $isSelected = (string)$vagaId === (string)$vaga->id;
                            @endphp
                            <label
                                x-show="!busca.trim() || '{{ $searchText }}'.includes(busca.toLowerCase().trim())"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="cursor-pointer group flex items-center gap-3.5 px-4 py-3.5 rounded-2xl border-2 transition-all
                                {{ $isSelected ? 'border-indigo-400 bg-indigo-50' : 'border-slate-200 hover:border-indigo-200 hover:bg-slate-50' }}">
                                <input type="radio" wire:model.live="vagaId" value="{{ $vaga->id }}" class="sr-only" />
                                <div class="w-9 h-9 rounded-xl {{ $vc }} flex items-center justify-center shrink-0 text-white text-xs font-black shadow-sm">
                                    {{ strtoupper(substr($vaga->titulo, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold {{ $isSelected ? 'text-indigo-700' : 'text-slate-700' }} truncate">
                                        {{ $vaga->titulo }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                        @if ($vaga->cargo)
                                        <span class="text-[10px] text-slate-400">{{ $vaga->cargo }}</span>
                                        @endif
                                        @if ($vaga->modalidade)
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-500">
                                            {{ ['presencial'=>'Presencial','remoto'=>'Remoto','hibrido'=>'Híbrido'][$vaga->modalidade] ?? $vaga->modalidade }}
                                        </span>
                                        @endif
                                        @if ($vaga->cidade)
                                        <span class="text-[10px] text-slate-400">{{ $vaga->cidade }}/{{ $vaga->estado }}</span>
                                        @endif
                                        @if ($vaga->salario_min || $vaga->salario_max)
                                        <span class="text-[10px] text-emerald-600 font-semibold">
                                            R$ {{ number_format($vaga->salario_min ?? $vaga->salario_max, 0, ',', '.') }}{{ $vaga->salario_min && $vaga->salario_max ? ' – '.number_format($vaga->salario_max, 0, ',', '.') : '' }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @if ($isSelected)
                                <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0" style="background:#6366f1;">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                @endif
                            </label>
                            @endforeach

                            {{-- Sem resultados --}}
                            <div x-cloak
                                 x-show="busca.trim() && $el.parentElement.querySelectorAll('label[x-show]:not([style*=\'display: none\'])').length === 0"
                                 class="py-10 text-center">
                                <svg class="w-9 h-9 text-slate-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-slate-400">Nenhuma vaga para <strong x-text="'&quot;' + busca + '&quot;'" class="text-slate-500"></strong></p>
                            </div>
                        </div>

                        <p class="text-[10px] text-slate-400 text-right">
                            {{ $this->vagasPublicadas->count() }} {{ $this->vagasPublicadas->count() === 1 ? 'vaga disponível' : 'vagas disponíveis' }}
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Upload CV --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-slate-600">Currículo (PDF, DOC, DOCX — máx. 5 MB)</label>
                        <span class="text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">opcional</span>
                    </div>
                    <label class="cursor-pointer flex flex-col items-center justify-center w-full h-36 rounded-2xl border-2 border-dashed transition-all group
                        {{ $arquivo ? 'border-indigo-300 bg-indigo-50' : 'border-slate-200 bg-slate-50 hover:border-indigo-300 hover:bg-indigo-50/50' }}">
                        <input type="file" wire:model="arquivo" accept=".pdf,.doc,.docx" class="sr-only" />
                        @if ($arquivo)
                        <div class="w-10 h-10 rounded-2xl bg-indigo-100 flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-indigo-600">{{ $arquivo->getClientOriginalName() }}</p>
                        <p class="text-xs text-slate-400 mt-1">Clique para trocar o arquivo</p>
                        @else
                        <div class="w-10 h-10 rounded-2xl bg-slate-100 group-hover:bg-indigo-100 flex items-center justify-center mb-2 transition-colors">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <p class="text-sm text-slate-500">Arraste ou <span class="text-indigo-600 font-semibold">clique para selecionar</span></p>
                        <p class="text-xs text-slate-400 mt-1">PDF, DOC, DOCX até 5 MB</p>
                        @endif
                    </label>
                    @error('arquivo') <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}</p> @enderror
                </div>

            </div>
            @endif

            {{-- ── Footer de navegação ───────────────────────────────────── --}}
            <div class="px-8 py-5 border-t border-slate-100 flex items-center justify-between"
                 style="background: linear-gradient(to right, #fafafe, #f8f9ff);">
                @if ($step > 1)
                <button wire:click="voltarPasso" type="button"
                        class="cursor-pointer flex items-center gap-2 px-5 py-2.5 rounded-2xl border border-slate-200
                               bg-white text-slate-600 text-sm font-bold hover:bg-slate-50 hover:border-slate-300
                               active:scale-95 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Voltar
                </button>
                @else
                <div></div>
                @endif

                @if ($step < 3)
                <button wire:click="proximoPasso" type="button"
                        class="cursor-pointer flex items-center gap-2 px-6 py-2.5 rounded-2xl text-white text-sm font-bold
                               active:scale-95 transition-all shadow-lg"
                        style="background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 4px 16px -2px rgba(99,102,241,.4);">
                    Próximo
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                @else
                <button wire:click="enviar" type="button"
                        wire:loading.attr="disabled" wire:target="enviar"
                        class="cursor-pointer flex items-center gap-2 px-6 py-2.5 rounded-2xl text-white text-sm font-bold
                               active:scale-95 transition-all disabled:opacity-60"
                        style="background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 4px 16px -2px rgba(99,102,241,.4);">
                    <span wire:loading.remove wire:target="enviar" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enviar candidatura
                    </span>
                    <span wire:loading wire:target="enviar" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Enviando...
                    </span>
                </button>
                @endif
            </div>

        </div>

        <p class="text-center text-xs text-slate-400 mt-6 flex items-center justify-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Seus dados são tratados com segurança e utilizados apenas para fins de recrutamento.
        </p>
    </div>

    @endif

    </div>
</div>
