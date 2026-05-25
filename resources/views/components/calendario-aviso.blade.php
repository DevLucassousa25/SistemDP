@props(['data' => null])

@if($data)
    @php
        $eventosAviso = \App\Models\CalendarioEvento::eventosNaData($data);
    @endphp

    @if($eventosAviso->isNotEmpty())
        <div class="rounded-lg border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 px-3 py-2.5 flex gap-2.5 items-start">
            <x-lucide-alert-triangle class="w-4 h-4 text-amber-500 dark:text-amber-400 mt-0.5 shrink-0" />
            <div class="min-w-0">
                <p class="text-sm font-semibold text-amber-700 dark:text-amber-400 leading-snug">
                    Atenção: esta data tem eventos no calendário corporativo
                </p>
                <ul class="mt-1 space-y-0.5">
                    @foreach($eventosAviso as $ev)
                        <li class="text-xs text-amber-600 dark:text-amber-300 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background-color: {{ $ev->cor }}"></span>
                            <span class="font-medium">{{ $ev->titulo }}</span>
                            <span class="text-amber-500/70 dark:text-amber-400/60">({{ $ev->tipo_label }})</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endif
