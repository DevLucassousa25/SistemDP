<div>

    @if ($show)

        @php
            $config = match ($type) {
                'success' => [
                    'wrapper' => 'bg-white border-0 dark:bg-gray-800',
                    'icon_wrap' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/60 dark:text-emerald-400',
                    'icon' => 'circle-check-big',
                    'title' => 'text-gray-900 dark:text-gray-100',
                    'description' => 'text-gray-600 dark:text-gray-300',
                    'btn_confirm' => 'bg-emerald-600 hover:bg-emerald-700 focus-visible:ring-emerald-500 text-white',
                    'btn_close' => 'text-zinc-600 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200',
                ],
                'warning' => [
                    'wrapper' => 'bg-white border-0 dark:bg-gray-800',
                    'icon_wrap' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/60 dark:text-amber-400',
                    'icon' => 'triangle-alert',
                    'title' => 'text-gray-900 dark:text-gray-100',
                    'description' => 'text-gray-600 dark:text-gray-300',
                    'btn_confirm' => 'bg-amber-500 hover:bg-amber-600 focus-visible:ring-amber-400 text-white',
                    'btn_close' => 'text-zinc-600 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200',
                ],
                'error' => [
                    'wrapper' => 'bg-white border-0 dark:bg-gray-800',
                    'icon_wrap' => 'bg-rose-100 text-rose-600 dark:bg-rose-900/60 dark:text-rose-400',
                    'icon' => 'circle-x',
                    'title' => 'text-gray-900 dark:text-gray-100',
                    'description' => 'text-gray-600 dark:text-gray-300',
                    'btn_confirm' => 'bg-rose-600 hover:bg-rose-700 focus-visible:ring-rose-500 text-white',
                    'btn_close' => 'text-zinc-600 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200',
                ],
                default => [
                    'wrapper' => 'bg-white border-0 dark:bg-gray-800',
                    'icon_wrap' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/60 dark:text-blue-400',
                    'icon' => 'info',
                    'title' => 'text-gray-900 dark:text-gray-100',
                    'description' => 'text-gray-600 dark:text-gray-300',
                    'btn_confirm' => 'bg-blue-600 hover:bg-blue-700 focus-visible:ring-blue-500 text-white',
                    'btn_close' => 'text-zinc-600 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200',
                ],
            };
        @endphp


        <div x-data x-show="$wire.show"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                wire:click="close"></div>

            {{-- Modal --}}
            <div
                class="relative z-10 w-full max-w-md rounded-2xl border shadow-xl {{ $config['wrapper'] }}">

                {{-- Botão fechar --}}
                <button wire:click="close"
                    class="absolute right-4 top-4 p-1 {{ $config['btn_close'] }} cursor-pointer rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-{{ explode(' ', $config['btn_close'])[1] }}">
                    <x-lucide-x class="h-4 w-4" />
                </button>

                {{-- Conteúdo --}}
                <div class="flex flex-col items-center gap-4 px-8 pb-7 pt-8 text-center">

                    <div class="flex h-16 w-16 items-center justify-center rounded-full {{ $config['icon_wrap'] }}">
                        <x-dynamic-component :component="'lucide-' . $config['icon']" class="h-8 w-8" />
                    </div>

                    <h2 class="text-xl font-semibold {{ $config['title'] }}">
                        {{ $title }}
                    </h2>

                    <p class="text-sm {{ $config['description'] }}">
                        {{ $description }}
                    </p>

                    <button wire:click="close"
                        class="w-full rounded-xl px-6 py-2.5 text-sm {{ $config['btn_confirm'] }} cursor-pointer focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-{{ explode(' ', $config['btn_confirm'])[1] }}">
                        Entendido
                    </button>

                </div>

            </div>
        </div>

    @endif

</div>
