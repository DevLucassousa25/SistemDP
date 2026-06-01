@if ($vagaModal2)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     wire:click.self="$set('vagaModal2', '')">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col" @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-700 shrink-0 rounded-t-2xl">
            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                    <x-lucide-briefcase class="w-3.5 h-3.5 text-white" />
                </span>
                {{ $vagaModal2 === 'edit' ? 'Editar Vaga' : 'Nova Vaga' }}
            </h3>
            <button wire:click="$set('vagaModal2', '')" type="button"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-6">

            {{-- ── Seção 1: Identificação ─────────────────────────── --}}
            <div>
                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <x-lucide-file-text class="w-3.5 h-3.5" /> Identificação
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Título da vaga <span class="text-red-400">*</span></label>
                        <input wire:model="vTitulo" type="text" placeholder="Ex: Desenvolvedor Full Stack Sênior"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                        @error('vTitulo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Cargo <span class="text-red-400">*</span></label>
                        <input wire:model="vCargo" type="text" placeholder="Ex: Analista de TI Pleno"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                        @error('vCargo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Modalidade</label>
                        <div class="flex gap-2">
                            @foreach (['presencial'=>['Presencial','building-2'],'remoto'=>['Remoto','wifi'],'hibrido'=>['Híbrido','split']] as $mv=>[$ml,$mi])
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="vModalidade" value="{{ $mv }}" class="sr-only peer" />
                                <div class="flex flex-col items-center gap-1 p-2.5 rounded-xl border text-center transition
                                            peer-checked:border-indigo-400 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 peer-checked:text-indigo-700 dark:peer-checked:text-indigo-300
                                            border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600">
                                    <x-dynamic-component :component="'lucide-'.$mi" class="w-4 h-4" />
                                    <span class="text-[10px] lato-bold leading-none">{{ $ml }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

            {{-- ── Seção 2: Localização & Remuneração ─────────────── --}}
            <div x-data="vagaGeo($wire)">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <x-lucide-map-pin class="w-3.5 h-3.5" /> Localização & Remuneração
                    </p>
                    <button @click="getLocation" type="button"
                            :disabled="geoLoading"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs lato-bold transition
                                   border border-indigo-200 dark:border-indigo-700
                                   text-indigo-600 dark:text-indigo-300
                                   bg-indigo-50 dark:bg-indigo-900/20
                                   hover:bg-indigo-100 dark:hover:bg-indigo-900/40
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="!geoLoading">
                            <x-lucide-locate class="w-3.5 h-3.5" />
                        </template>
                        <template x-if="geoLoading">
                            <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                        </template>
                        <span x-text="geoLoading ? 'Detectando...' : 'Usar minha localização'"></span>
                    </button>
                </div>

                {{-- Erro: permissão bloqueada --}}
                <template x-if="geoError === 'blocked'">
                    <div class="flex items-start gap-2.5 px-3 py-3 mb-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700">
                        <x-lucide-lock class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                        <div class="flex-1 min-w-0">
                            <p class="text-xs lato-bold text-amber-700 dark:text-amber-400 mb-1">Localização bloqueada pelo browser</p>
                            <p class="text-[11px] text-amber-600 dark:text-amber-500 lato-regular leading-relaxed" x-html="geoHint"></p>
                        </div>
                    </div>
                </template>

                {{-- Erro: falha de rede --}}
                <template x-if="geoError === 'fetch_error'">
                    <div class="flex items-center gap-2 px-3 py-2 mb-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <x-lucide-wifi-off class="w-3.5 h-3.5 text-red-500 shrink-0" />
                        <p class="text-xs text-red-600 dark:text-red-400 lato-regular">Não foi possível obter o endereço. Verifique sua conexão.</p>
                    </div>
                </template>

                {{-- Erro: timeout --}}
                <template x-if="geoError === 'timeout'">
                    <div class="flex items-center gap-2 px-3 py-2 mb-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <x-lucide-clock class="w-3.5 h-3.5 text-red-500 shrink-0" />
                        <p class="text-xs text-red-600 dark:text-red-400 lato-regular">Tempo esgotado. Tente novamente.</p>
                    </div>
                </template>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Cidade</label>
                        <input wire:model="vCidade" type="text" placeholder="São Paulo"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">UF</label>
                        <input wire:model="vEstado" type="text" maxlength="2" placeholder="SP"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition uppercase" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Sal. mínimo (R$)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 lato-bold pointer-events-none">R$</span>
                            <input wire:model="vSalMin" type="number" step="100" placeholder="0"
                                   class="w-full pl-8 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Sal. máximo (R$)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 lato-bold pointer-events-none">R$</span>
                            <input wire:model="vSalMax" type="number" step="100" placeholder="0"
                                   class="w-full pl-8 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

            {{-- ── Seção 3: Descrição ──────────────────────────────── --}}
            <div>
                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <x-lucide-align-left class="w-3.5 h-3.5" /> Descrição da Vaga
                </p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descrição geral</label>
                        <textarea wire:model="vDescricao" rows="3" placeholder="Descreva as responsabilidades e o contexto da vaga..."
                                  class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                         bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                         focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                         lato-regular placeholder-slate-400 resize-none transition"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Requisitos</label>
                            <textarea wire:model="vRequisitos" rows="3" placeholder="Experiências e conhecimentos necessários..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                             bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                             focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                             lato-regular placeholder-slate-400 resize-none transition"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Competências desejáveis</label>
                            <textarea wire:model="vCompetencias" rows="3" placeholder="Soft skills e diferenciais valorizados..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                             bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                             focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                             lato-regular placeholder-slate-400 resize-none transition"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Benefícios</label>
                            <textarea wire:model="vBeneficios" rows="2" placeholder="VR, VT, plano de saúde, home office..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                             bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                             focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                             lato-regular placeholder-slate-400 resize-none transition"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

            {{-- ── Seção 4: Configurações ──────────────────────────── --}}
            <div>
                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <x-lucide-settings-2 class="w-3.5 h-3.5" /> Configurações
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Vagas</label>
                        <input wire:model="vQtd" type="number" min="1" placeholder="1"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">SLA (dias)</label>
                        <input wire:model="vSla" type="number" min="1" placeholder="30"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Nota mínima (%)</label>
                        <input wire:model="vNota" type="number" min="0" max="100" placeholder="70"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Encerramento</label>
                        <input wire:model="vDataEnc" type="date"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                    </div>
                </div>

                {{-- Status da vaga --}}
                <div class="mt-4">
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Status da vaga</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['rascunho'=>['Rascunho','bg-slate-400','slate'],'publicada'=>['Publicada','bg-green-400','green'],'pausada'=>['Pausada','bg-amber-400','amber'],'encerrada'=>['Encerrada','bg-red-400','red']] as $sv=>[$sl,$sd,$sc])
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="vStatus" value="{{ $sv }}" class="sr-only peer" />
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs lato-bold transition
                                        peer-checked:border-indigo-400 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 peer-checked:text-indigo-700 dark:peer-checked:text-indigo-300
                                        border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600">
                                <span class="w-2 h-2 rounded-full {{ $sd }}"></span> {{ $sl }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

            {{-- ── Seção 5: Etapas do Processo Seletivo ───────────── --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <x-lucide-git-branch class="w-3.5 h-3.5" /> Etapas do Processo Seletivo
                    </p>
                    <button wire:click="addEtapa" type="button"
                            class="cursor-pointer flex items-center gap-1 text-xs lato-bold text-indigo-500 hover:text-indigo-700 transition">
                        <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar etapa
                    </button>
                </div>
                <div class="space-y-2">
                    @foreach ($etapas as $i => $etapa)
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 group">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $etapa['cor'] }}"></span>
                        <input wire:model="etapas.{{ $i }}.nome" type="text" placeholder="Nome da etapa"
                               class="flex-1 min-w-0 text-sm bg-transparent border-none outline-none text-slate-700 dark:text-slate-200 lato-regular placeholder-slate-400" />
                        <select wire:model="etapas.{{ $i }}.cor"
                                class="text-xs border border-slate-200 dark:border-slate-600 rounded-lg px-2 py-1
                                       bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300
                                       focus:outline-none focus:ring-1 focus:ring-indigo-400/30 shrink-0">
                            @foreach ($etapaCores as $cor)
                            <option value="{{ $cor }}">{{ ucfirst(str_replace(['bg-','-400','-500','-'], ['','','',' '], $cor)) }}</option>
                            @endforeach
                        </select>
                        <label class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400 cursor-pointer shrink-0">
                            <input type="checkbox" wire:model="etapas.{{ $i }}.is_aprovado" class="w-3 h-3 rounded accent-green-500 cursor-pointer" />
                            <span class="text-green-600 dark:text-green-400 lato-bold">Aprova</span>
                        </label>
                        <label class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400 cursor-pointer shrink-0">
                            <input type="checkbox" wire:model="etapas.{{ $i }}.is_reprovado" class="w-3 h-3 rounded accent-red-500 cursor-pointer" />
                            <span class="text-red-500 dark:text-red-400 lato-bold">Reprova</span>
                        </label>
                        @if (count($etapas) > 1)
                        <button wire:click="removeEtapa({{ $i }})" type="button"
                                class="cursor-pointer p-1 rounded-lg text-slate-300 dark:text-slate-600 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition shrink-0 opacity-0 group-hover:opacity-100">
                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between px-5 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/30 rounded-b-2xl shrink-0">
            <p class="text-xs text-slate-400 lato-regular">
                <span class="text-red-400">*</span> campos obrigatórios
            </p>
            <div class="flex items-center gap-2">
                <button wire:click="$set('vagaModal2', '')" type="button"
                        class="cursor-pointer px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm text-slate-600 dark:text-slate-300 lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button wire:click="saveVaga" type="button" wire:loading.attr="disabled" wire:target="saveVaga"
                        class="cursor-pointer inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60">
                    <span wire:loading wire:target="saveVaga">
                        <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                    </span>

                    <span wire:loading.remove wire:target="saveVaga" class="flex items-center gap-1.5">
                        @if ($vagaModal2 === 'edit')
                            <x-lucide-square-pen class="w-4 h-4" />
                            Salvar Alterações
                        @else
                            <x-lucide-circle-check class="w-4 h-4" />
                            Confirmar
                        @endif
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif
