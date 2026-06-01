@if ($verbasModal)
<div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     x-data x-on:keydown.escape.window="$wire.set('verbasModal', false)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col"
         @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shrink-0">
                    <x-lucide-calculator class="w-3.5 h-3.5 text-white" />
                </span>
                Calcular Verbas Rescisórias
            </h3>
            <button wire:click="$set('verbasModal', false)" type="button"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        {{-- Body --}}
        <div class="overflow-y-auto flex-1 p-5 space-y-5">

            {{-- Dados básicos --}}
            <div>
                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <x-lucide-user class="w-3.5 h-3.5" /> Dados do Funcionário
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Salário Base (R$) <span class="text-red-400">*</span></label>
                        <input wire:model="vSalarioBase" type="number" step="0.01" min="0" placeholder="0,00"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-400
                                      lato-regular placeholder-slate-400 transition" />
                        @error('vSalarioBase') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Data de Admissão <span class="text-red-400">*</span></label>
                        <input wire:model="vDataAdmissao" type="date"
                               class="cursor-pointer w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-400
                                      lato-regular transition" />
                        @error('vDataAdmissao') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Data de Demissão <span class="text-red-400">*</span></label>
                        <input wire:model="vDataDemissao" type="date"
                               class="cursor-pointer w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-400
                                      lato-regular transition" />
                        @error('vDataDemissao') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

            {{-- Aviso prévio + opções --}}
            <div>
                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <x-lucide-settings class="w-3.5 h-3.5" /> Parâmetros
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Aviso Prévio</label>
                        <div class="grid grid-cols-2 gap-1.5">
                            @foreach (['indenizado' => 'Indenizado', 'trabalhado' => 'Trabalhado'] as $av => $al)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="vAvisoPrevioTipo" value="{{ $av }}" class="sr-only peer" />
                                <div class="flex items-center justify-center py-2 rounded-xl border text-xs lato-bold text-center transition
                                            peer-checked:border-emerald-400 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 peer-checked:text-emerald-700 dark:peer-checked:text-emerald-300
                                            border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800">
                                    {{ $al }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Dias de Aviso <span class="text-slate-400 font-normal">(0 = proporcional)</span></label>
                        <input wire:model="vAvisoPrevioDias" type="number" min="0" placeholder="30"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-400
                                      lato-regular transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Nº de Dependentes</label>
                        <input wire:model="vNumeroDependentes" type="number" min="0" placeholder="0"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-400
                                      lato-regular transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Saldo FGTS (R$) <span class="text-slate-400 font-normal">(vazio = estimado)</span></label>
                        <input wire:model="vSaldoFgts" type="number" step="0.01" min="0" placeholder="Automático"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-400
                                      lato-regular placeholder-slate-400 transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Outros Créditos (R$)</label>
                        <input wire:model="vOutrosCreditos" type="number" step="0.01" min="0" placeholder="0,00"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-400
                                      lato-regular placeholder-slate-400 transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descontos Extras (R$)</label>
                        <input wire:model="vDescontos" type="number" step="0.01" min="0" placeholder="0,00"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-400
                                      lato-regular placeholder-slate-400 transition" />
                    </div>
                </div>

                {{-- Férias vencidas toggle --}}
                <div class="mt-4 flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                    <div>
                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">Possui férias vencidas?</p>
                        <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Adiciona mais 1/3 ao cálculo de férias</p>
                    </div>
                    <button wire:click="$toggle('vTemFeriasVencidas')" type="button"
                            class="cursor-pointer relative w-10 h-6 rounded-full transition-colors duration-200 focus:outline-none shrink-0
                                   {{ $vTemFeriasVencidas ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-600' }}">
                        <span class="absolute top-1 left-1 w-4 h-4 rounded-full bg-white shadow transition-transform duration-200
                                     {{ $vTemFeriasVencidas ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>

            {{-- Resultado do cálculo --}}
            @if ($verbasCalculadas)
            <div class="border-t border-slate-100 dark:border-slate-700/60"></div>
            <div>
                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <x-lucide-receipt class="w-3.5 h-3.5" /> Resultado do Cálculo
                </p>
                @php $vc = $verbasCalculadas; @endphp
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-4 text-white text-center mb-4">
                    <p class="text-xs opacity-80 mb-1">Total Líquido a Receber</p>
                    <p class="text-3xl lato-black">R$ {{ number_format($vc['total_liquido'], 2, ',', '.') }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- Créditos --}}
                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-4 space-y-2">
                        <p class="text-xs lato-bold text-slate-500 uppercase tracking-wider mb-2">Créditos</p>
                        @foreach ([
                            'Saldo de Salário'      => $vc['saldo_salario'] ?? 0,
                            'Férias Proporcionais'  => $vc['ferias_proporcionais'] ?? 0,
                            'Férias Vencidas'       => $vc['ferias_vencidas'] ?? 0,
                            '1/3 de Férias'         => $vc['um_terco_ferias'] ?? 0,
                            '13° Proporcional'      => $vc['decimo_terceiro'] ?? 0,
                            'Aviso Prévio'          => $vc['aviso_previo_valor'] ?? 0,
                            'Outros Créditos'       => $vc['outros_creditos'] ?? 0,
                        ] as $label => $val)
                            @if ($val > 0)
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">{{ $label }}</span>
                                <span class="lato-bold text-slate-700 dark:text-slate-200">R$ {{ number_format($val, 2, ',', '.') }}</span>
                            </div>
                            @endif
                        @endforeach
                        <div class="border-t border-slate-200 dark:border-slate-700 pt-2 flex justify-between text-xs">
                            <span class="lato-bold text-slate-600 dark:text-slate-300">Total Bruto</span>
                            <span class="lato-black text-slate-800 dark:text-white">R$ {{ number_format($vc['total_bruto'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                    {{-- Descontos + FGTS --}}
                    <div class="space-y-3">
                        <div class="bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-4 space-y-2">
                            <p class="text-xs lato-bold text-slate-500 uppercase tracking-wider mb-2">Descontos</p>
                            @foreach (['INSS' => $vc['inss'] ?? 0, 'IRRF' => $vc['irrf'] ?? 0, 'Outros' => $vc['descontos'] ?? 0] as $label => $val)
                                @if ($val > 0)
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-500">{{ $label }}</span>
                                    <span class="lato-bold text-rose-600">- R$ {{ number_format($val, 2, ',', '.') }}</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-700/40 p-4 space-y-2">
                            <p class="text-xs lato-bold text-blue-600 dark:text-blue-400 mb-2">FGTS</p>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Saldo FGTS</span>
                                <span class="lato-bold text-slate-700 dark:text-slate-200">R$ {{ number_format($vc['saldo_fgts'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                            @if (($vc['multa_fgts'] ?? 0) > 0)
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Multa FGTS</span>
                                <span class="lato-bold text-emerald-600">+ R$ {{ number_format($vc['multa_fgts'], 2, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="border-t border-blue-200 dark:border-blue-700/40 pt-2 flex justify-between text-xs">
                                <span class="lato-bold text-blue-700 dark:text-blue-300">Disponível FGTS</span>
                                <span class="lato-black text-blue-700 dark:text-blue-300">R$ {{ number_format($vc['fgts_disponivel'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0">
            <button wire:click="$set('verbasModal', false)" type="button"
                    class="cursor-pointer px-4 py-2 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                Cancelar
            </button>
            <div class="flex items-center gap-3">
                <button wire:click="calcularVerbas" type="button"
                        class="cursor-pointer px-5 py-2 rounded-xl text-sm lato-bold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition flex items-center gap-2">
                    <x-lucide-calculator class="w-3.5 h-3.5" /> Calcular
                </button>
                @if ($verbasCalculadas)
                <button wire:click="saveVerbas" type="button"
                        class="cursor-pointer px-5 py-2 rounded-xl text-sm lato-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-90 transition flex items-center gap-2 shadow-sm">
                    <x-lucide-save class="w-3.5 h-3.5" /> Salvar verbas
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
