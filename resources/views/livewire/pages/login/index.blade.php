<div>
    {{-- resources/views/livewire/auth/login.blade.php --}}
    <div class="min-h-screen flex items-center justify-center bg-zinc-950 px-4">

        {{-- Card principal --}}
        <div class="w-full max-w-sm">

            {{-- Logo / marca --}}
            <div class="mb-8 text-center">
                <div
                    class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white/5 border border-white/10 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white font-display tracking-tight">Bem-vindo de volta</h1>
                <p class="text-sm text-zinc-400 mt-1.5 font-normal">Acesse sua conta para continuar</p>
            </div>

            {{-- Formulário --}}
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-sm">

                {{-- Alerta de erro global --}}
                @if (session('error'))
                    <div
                        class="mb-4 flex items-start gap-2 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-xl px-3 py-2.5">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="login" class="space-y-4">

                    {{-- E-mail --}}
                    <div>
                        <label for="email"
                            class="block text-xs font-medium text-zinc-400 mb-1.5 uppercase tracking-wider">
                            E-mail
                        </label>
                        <input wire:model="email" type="email" id="email" autocomplete="email"
                            placeholder="voce@empresa.com"
                            class="w-full bg-white/5 border rounded-xl px-3.5 py-2.5 text-sm text-white placeholder-zinc-600
                               focus:outline-none focus:ring-1 transition-colors
                               @error('email') border-red-500/50 focus:ring-red-500/30
                               @else border-white/10 focus:border-white/25 focus:ring-white/10 @enderror" />
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Senha --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password"
                                class="block text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                Senha
                            </label>
                        </div>
                        <input wire:model="password" type="password" id="password" autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full bg-white/5 border rounded-xl px-3.5 py-2.5 text-sm text-white placeholder-zinc-600
                               focus:outline-none focus:ring-1 transition-colors
                               @error('password') border-red-500/50 focus:ring-red-500/30
                               @else border-white/10 focus:border-white/25 focus:ring-white/10 @enderror" />
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Lembrar-me --}}
                    <div class="flex items-center gap-2">
                        <input wire:model="remember" type="checkbox" id="remember"
                            class="w-4 h-4 rounded bg-white/5 border-white/10 text-white focus:ring-0 focus:ring-offset-0 cursor-pointer" />
                        <label for="remember" class="text-sm text-zinc-400 cursor-pointer select-none">
                            Manter conectado
                        </label>
                    </div>

                    {{-- Botão --}}
                    <button type="submit" wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-not-allowed"
                        class="w-full mt-2 bg-white text-zinc-900 font-medium text-sm rounded-xl py-2.5 px-4
                           hover:bg-zinc-100 active:scale-[0.98] transition-all duration-150 flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove wire:target="login">Entrar</span>
                        <span wire:loading wire:target="login" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                        </span>
                    </button>
                </form>
            </div>

            {{-- Rodapé --}}
            <p class="text-center text-xs text-zinc-600 mt-6">
                Problemas para acessar? Fale com o administrador do sistema.
            </p>

        </div>
    </div>
</div>
