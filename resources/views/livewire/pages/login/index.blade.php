<div class="min-h-screen flex bg-[#F8FAFC] dark:bg-slate-900">

    {{-- ── Painel esquerdo — branding ────────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-[52%] relative overflow-hidden
                bg-gradient-to-br from-slate-800 via-slate-900 to-slate-950
                flex-col items-center justify-center p-14">

        {{-- Padrão de fundo sutil --}}
        <div class="absolute inset-0 opacity-[0.04]"
             style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>

        {{-- Blob decorativo --}}
        <div class="absolute top-[-80px] right-[-80px] w-[420px] h-[420px] rounded-full opacity-20"
             style="background: radial-gradient(circle, #6366f1 0%, transparent 70%)"></div>
        <div class="absolute bottom-[-60px] left-[-60px] w-[320px] h-[320px] rounded-full opacity-15"
             style="background: radial-gradient(circle, #3b82f6 0%, transparent 70%)"></div>

        <div class="relative z-10 max-w-md text-center">

            {{-- Logo --}}
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-8
                        bg-gradient-to-br from-blue-500 to-purple-600 shadow-2xl shadow-blue-500/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
            </div>

            <h2 class="text-4xl font-bold text-white lato-black tracking-tight mb-3">PeopleHub</h2>
            <p class="text-slate-400 lato-regular text-base leading-relaxed">
                Plataforma integrada de gestão de pessoas.<br>
                Avaliações, tarefas, feedbacks e muito mais.
            </p>

            {{-- Destaques --}}
            <div class="mt-12 grid grid-cols-3 gap-4 text-center">
                @foreach ([
                    ['icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'label' => 'Avaliações'],
                    ['icon' => 'M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z', 'label' => 'Indicadores'],
                    ['icon' => 'M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z', 'label' => 'Feedback'],
                ] as $item)
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                            </svg>
                        </div>
                        <span class="text-xs text-slate-400 lato-regular">{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── Painel direito — formulário ────────────────────────────────── --}}
    <div class="flex-1 flex flex-col items-center justify-center px-6 sm:px-12 py-12">

        {{-- Logo mobile --}}
        <div class="lg:hidden flex items-center gap-3 mb-10">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg shadow-blue-500/25">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
            </div>
            <span class="text-xl font-bold text-slate-800 dark:text-white lato-black">PeopleHub</span>
        </div>

        <div class="w-full max-w-sm">

            {{-- Cabeçalho --}}
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white lato-black tracking-tight">
                    Bem-vindo de volta
                </h1>
                <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular mt-1.5">
                    Acesse sua conta para continuar
                </p>
            </div>

            {{-- Alerta de erro global --}}
            @if (session('error'))
                <div class="mb-5 flex items-start gap-2.5 text-sm text-red-600 dark:text-red-400
                            bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800
                            rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <span class="lato-regular">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Formulário --}}
            <form wire:submit.prevent="login" class="space-y-5">

                {{-- E-mail --}}
                <div>
                    <label for="email"
                           class="block text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wider mb-2">
                        E-mail
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input wire:model="email" type="email" id="email" autocomplete="email"
                               placeholder="voce@empresa.com"
                               class="w-full pl-10 pr-4 py-2.5 text-sm lato-regular
                                      bg-white dark:bg-slate-800
                                      border rounded-xl
                                      text-slate-700 dark:text-slate-200
                                      placeholder-slate-300 dark:placeholder-slate-600
                                      focus:outline-none focus:ring-2 transition-all
                                      @error('email')
                                          border-red-300 dark:border-red-700 focus:ring-red-100 dark:focus:ring-red-900/30
                                      @else
                                          border-slate-200 dark:border-slate-700 focus:border-blue-400 dark:focus:border-blue-500 focus:ring-blue-50 dark:focus:ring-blue-900/20
                                      @enderror" />
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500 lato-regular flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Senha --}}
                <div>
                    <label for="password"
                           class="block text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wider mb-2">
                        Senha
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <input wire:model="password" type="password" id="password" autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-2.5 text-sm lato-regular
                                      bg-white dark:bg-slate-800
                                      border rounded-xl
                                      text-slate-700 dark:text-slate-200
                                      placeholder-slate-300 dark:placeholder-slate-600
                                      focus:outline-none focus:ring-2 transition-all
                                      @error('password')
                                          border-red-300 dark:border-red-700 focus:ring-red-100 dark:focus:ring-red-900/30
                                      @else
                                          border-slate-200 dark:border-slate-700 focus:border-blue-400 dark:focus:border-blue-500 focus:ring-blue-50 dark:focus:ring-blue-900/20
                                      @enderror" />
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500 lato-regular flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Lembrar-me --}}
                <div class="flex items-center gap-2.5">
                    <input wire:model="remember" type="checkbox" id="remember"
                           class="w-4 h-4 rounded border-slate-300 dark:border-slate-600
                                  text-blue-500 focus:ring-blue-200 dark:focus:ring-blue-800
                                  bg-white dark:bg-slate-800 cursor-pointer" />
                    <label for="remember" class="text-sm text-slate-500 dark:text-slate-400 lato-regular cursor-pointer select-none">
                        Manter conectado
                    </label>
                </div>

                {{-- Botão entrar --}}
                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="w-full mt-1 py-2.5 px-4 rounded-xl text-sm font-semibold lato-bold
                               bg-gradient-to-r from-blue-500 to-purple-600
                               hover:from-blue-600 hover:to-purple-700
                               text-white shadow-lg shadow-blue-500/25
                               active:scale-[0.98] transition-all duration-150
                               flex items-center justify-center gap-2 cursor-pointer
                               disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="login">
                        Entrar
                    </span>
                    <span wire:loading wire:target="login" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                    </span>
                </button>

            </form>

            {{-- Rodapé --}}
            <p class="mt-8 text-center text-xs text-slate-400 dark:text-slate-600 lato-regular">
                Problemas para acessar? Fale com o administrador do sistema.
            </p>

        </div>
    </div>

</div>
