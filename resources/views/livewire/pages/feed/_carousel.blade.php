{{--
    Carrossel de imagens do feed.
    Variáveis esperadas:
      $imgs      — Collection de objetos com ->path (storage path)
      $lightbox  — bool: true para abrir lightbox ao clicar (padrão true)
--}}
@php
    use Illuminate\Support\Js;
    $lightbox ??= true;
    $imgCount = $imgs->count();
    $imgsJs   = Js::from(
        $imgs->sortBy('order')
             ->values()
             ->map(fn($i) => ['src' => Storage::url($i->path)])
             ->toArray()
    );
@endphp

<div class="rounded-2xl overflow-hidden mb-3 relative group"
     x-data="{ ci: 0, imgs: {{ $imgsJs }} }">

    {{-- Slides: sempre renderizados, x-show controla visibilidade --}}
    <template x-for="(img, i) in imgs" :key="i">
        <div x-show="ci === i"
             class="relative bg-slate-100 dark:bg-slate-900">

            <img :src="img.src"
                 class="w-full max-h-96 object-cover block {{ $lightbox ? 'cursor-zoom-in' : '' }}"
                 @if($lightbox) @click="$dispatch('open-lightbox', { src: img.src, alt: '' })" @endif />

            {{-- Contador: só aparece com mais de 1 imagem --}}
            <span x-show="imgs.length > 1"
                  x-text="(i + 1) + ' / ' + imgs.length"
                  class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-full bg-black/55 backdrop-blur-sm text-white text-[11px] lato-bold select-none pointer-events-none"></span>
        </div>
    </template>

    {{-- Seta anterior — sempre no DOM, x-show controla visibilidade --}}
    <button x-show="imgs.length > 1"
            @click.stop="ci = (ci - 1 + imgs.length) % imgs.length"
            type="button"
            class="cursor-pointer absolute left-2.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full
                   bg-black/50 hover:bg-black/70 backdrop-blur-sm text-white
                   flex items-center justify-center shadow
                   opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10"
            style="display:none">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </button>

    {{-- Seta próxima --}}
    <button x-show="imgs.length > 1"
            @click.stop="ci = (ci + 1) % imgs.length"
            type="button"
            class="cursor-pointer absolute right-2.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full
                   bg-black/50 hover:bg-black/70 backdrop-blur-sm text-white
                   flex items-center justify-center shadow
                   opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10"
            style="display:none">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </button>

    {{-- Dots — sempre no DOM, x-show controla o container --}}
    <div x-show="imgs.length > 1"
         class="absolute bottom-2.5 left-0 right-0 flex items-center justify-center gap-1.5"
         style="display:none">
        <template x-for="(img, i) in imgs" :key="'dot-' + i">
            <button @click.stop="ci = i" type="button"
                    :class="ci === i ? 'w-4 bg-white shadow' : 'w-2 bg-white/50 hover:bg-white/75'"
                    class="cursor-pointer h-2 rounded-full transition-all duration-200"></button>
        </template>
    </div>
</div>
