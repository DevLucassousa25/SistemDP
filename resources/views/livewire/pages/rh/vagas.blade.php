@php
    if (!function_exists('rhVagaBg')) {
        function rhVagaBg(string $n): string {
            $pal = ['bg-indigo-500','bg-indigo-600','bg-teal-500','bg-orange-500','bg-cyan-500','bg-rose-500'];
            return $pal[abs(crc32($n)) % count($pal)];
        }
    }
    $statusCls = ['rascunho'=>'bg-slate-100 text-slate-600','publicada'=>'bg-green-100 text-green-700','pausada'=>'bg-amber-100 text-amber-700','encerrada'=>'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'];
    $statusLbl = ['rascunho'=>'Rascunho','publicada'=>'Publicada','pausada'=>'Pausada','encerrada'=>'Encerrada'];
    $modalLbl  = ['presencial'=>'Presencial','remoto'=>'Remoto','hibrido'=>'Híbrido'];
    $escOptions = ['fundamental'=>'Fundamental','medio'=>'Médio','tecnico'=>'Técnico','graduacao'=>'Graduação','pos_graduacao'=>'Pós-Graduação','mestrado'=>'Mestrado','doutorado'=>'Doutorado'];
    $etapaCores = ['bg-slate-400','bg-blue-400','bg-indigo-400','bg-violet-400','bg-purple-400','bg-green-400','bg-red-400','bg-amber-400','bg-teal-400','bg-orange-400'];
@endphp

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 p-4 sm:p-6 lg:p-8"
     x-data="{ confirmEnc: null }">
    <div class="max-w-7xl mx-auto space-y-5">

        {{-- Header --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl lato-bold text-slate-800 dark:text-slate-100">Gestão de Vagas</h1>
                <p class="text-sm text-slate-500 lato-regular mt-0.5">Crie, publique e gerencie as vagas da empresa</p>
            </div>
            <button wire:click="openCreate" type="button"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm">
                <x-lucide-plus class="w-4 h-4" /> Nova Vaga
            </button>
        </div>

        {{-- Filtros --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <input wire:model.live.debounce.400ms="search" type="search" placeholder="Buscar vaga..."
                       class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
            </div>
            <select wire:model.live="filterStatus"
                    class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 focus:outline-none">
                <option value="">Todos os status</option>
                <option value="rascunho">Rascunho</option>
                <option value="publicada">Publicada</option>
                <option value="pausada">Pausada</option>
                <option value="encerrada">Encerrada</option>
            </select>
            <select wire:model.live="filterModal"
                    class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 focus:outline-none">
                <option value="">Toda modalidade</option>
                <option value="presencial">Presencial</option>
                <option value="remoto">Remoto</option>
                <option value="hibrido">Híbrido</option>
            </select>
        </div>

        {{-- Grid de vagas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse ($this->vagas as $vaga)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-md transition flex flex-col gap-3"
                     x-data="{ menu: false }" @click.away="menu = false">

                    {{-- Top --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ rhVagaBg($vaga->titulo) }} flex items-center justify-center shrink-0 text-white lato-bold text-sm">
                            {{ strtoupper(substr($vaga->titulo, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 truncate">{{ $vaga->titulo }}</h3>
                            <p class="text-xs text-slate-500 lato-regular">{{ $vaga->cargo }}</p>
                        </div>
                        <div class="relative shrink-0">
                            <button @click="menu = !menu" type="button" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                <x-lucide-more-vertical class="w-4 h-4" />
                            </button>
                            <div x-show="menu" x-transition
                                 class="absolute right-0 mt-1 w-44 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg py-1 z-20">
                                <button wire:click="openEdit({{ $vaga->id }})" @click="menu=false" type="button"
                                        class="w-full text-left px-3 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                                    <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                                </button>
                                <button wire:click="openDrawer({{ $vaga->id }})" @click="menu=false" type="button"
                                        class="w-full text-left px-3 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                                    <x-lucide-eye class="w-3.5 h-3.5" /> Ver detalhes
                                </button>
                                <a href="{{ route('rh.pipeline') }}?vaga={{ $vaga->id }}"
                                   class="w-full text-left px-3 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                                    <x-lucide-git-branch class="w-3.5 h-3.5" /> Pipeline
                                </a>
                                <button wire:click="duplicarVaga({{ $vaga->id }})" @click="menu=false" type="button"
                                        class="w-full text-left px-3 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                                    <x-lucide-copy class="w-3.5 h-3.5" /> Duplicar
                                </button>
                                @if ($vaga->status !== 'publicada')
                                    <button wire:click="publicarVaga({{ $vaga->id }})" @click="menu=false" type="button"
                                            class="w-full text-left px-3 py-2 text-xs text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 flex items-center gap-2">
                                        <x-lucide-send class="w-3.5 h-3.5" /> Publicar
                                    </button>
                                @endif
                                @if ($vaga->status !== 'encerrada')
                                    <button wire:click="encerrarVaga({{ $vaga->id }})" @click="menu=false" type="button"
                                            class="w-full text-left px-3 py-2 text-xs text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 flex items-center gap-2">
                                        <x-lucide-x-circle class="w-3.5 h-3.5" /> Encerrar
                                    </button>
                                @endif
                                <button wire:click="confirmDelete({{ $vaga->id }})" @click="menu=false" type="button"
                                        class="w-full text-left px-3 py-2 text-xs text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" /> Excluir
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Badges --}}
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $statusCls[$vaga->status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $statusLbl[$vaga->status] ?? $vaga->status }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] lato-regular bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                            {{ $modalLbl[$vaga->modalidade] }}
                        </span>
                        @if ($vaga->cidade)
                            <span class="px-2 py-0.5 rounded-full text-[10px] lato-regular bg-slate-100 dark:bg-slate-700 text-slate-500 flex items-center gap-1">
                                <x-lucide-map-pin class="w-2.5 h-2.5" /> {{ $vaga->cidade }}{{ $vaga->estado ? '/'.$vaga->estado : '' }}
                            </span>
                        @endif
                    </div>

                    {{-- Salário --}}
                    @if ($vaga->salario_min || $vaga->salario_max)
                        <p class="text-xs text-slate-600 dark:text-slate-300 lato-regular flex items-center gap-1">
                            <x-lucide-dollar-sign class="w-3.5 h-3.5 text-green-500" />
                            @if ($vaga->salario_min && $vaga->salario_max)
                                R$ {{ number_format($vaga->salario_min, 0, ',', '.') }} – {{ number_format($vaga->salario_max, 0, ',', '.') }}
                            @elseif ($vaga->salario_min)
                                A partir de R$ {{ number_format($vaga->salario_min, 0, ',', '.') }}
                            @else
                                Até R$ {{ number_format($vaga->salario_max, 0, ',', '.') }}
                            @endif
                        </p>
                    @endif

                    {{-- Footer --}}
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <x-lucide-users class="w-3.5 h-3.5" /> {{ $vaga->candidaturas_count }} candidato{{ $vaga->candidaturas_count !== 1 ? 's' : '' }}
                        </span>
                        <a href="{{ route('rh.pipeline') }}?vaga={{ $vaga->id }}"
                           class="text-xs text-blue-500 hover:text-blue-700 lato-bold flex items-center gap-1 transition">
                            Pipeline <x-lucide-arrow-right class="w-3 h-3" />
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                        <x-lucide-briefcase class="w-8 h-8 text-slate-400" />
                    </div>
                    <p class="text-slate-500 lato-bold text-sm">Nenhuma vaga encontrada.</p>
                    <p class="text-slate-400 lato-regular text-xs mt-1">Crie sua primeira vaga para começar o processo seletivo.</p>
                </div>
            @endforelse
        </div>

        {{ $this->vagas->links() }}

    </div>

    {{-- ══════ MODAL CRIAR/EDITAR VAGA ══════ --}}
    @if ($vagaModal)
        <div class="fixed inset-0 z-50 flex items-start justify-center bg-black/60 backdrop-blur-sm overflow-y-auto py-6 px-4"
             @click.self="$wire.set('vagaModal', false)">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-3xl" @click.stop>

                <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-700 sticky top-0 bg-white dark:bg-slate-800 z-10 rounded-t-2xl">
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100">
                        {{ $vagaId ? 'Editar Vaga' : 'Nova Vaga' }}
                    </h3>
                    <button wire:click="$set('vagaModal', false)" type="button" class="p-1.5 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <div class="p-5 space-y-5">

                    {{-- Dados básicos --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Título da vaga *</label>
                            <input wire:model="titulo" type="text" placeholder="Ex: Desenvolvedor Full Stack"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                            @error('titulo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Cargo *</label>
                            <input wire:model="cargo" type="text" placeholder="Ex: Analista de TI"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Modalidade</label>
                            <select wire:model="modalidade"
                                    class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none">
                                <option value="presencial">Presencial</option>
                                <option value="remoto">Remoto</option>
                                <option value="hibrido">Híbrido</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Salário mínimo</label>
                            <input wire:model="salarioMin" type="number" step="100" placeholder="Ex: 3000"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Salário máximo</label>
                            <input wire:model="salarioMax" type="number" step="100" placeholder="Ex: 6000"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Cidade</label>
                            <input wire:model="cidade" type="text"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Estado (UF)</label>
                            <input wire:model="estado" type="text" maxlength="2" placeholder="SP"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                        </div>
                    </div>

                    {{-- Descrição e requisitos --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descrição da vaga</label>
                        <textarea wire:model="descricao" rows="4" placeholder="Descreva as responsabilidades..."
                                  class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Requisitos</label>
                            <textarea wire:model="requisitos" rows="3" placeholder="Requisitos obrigatórios..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Competências</label>
                            <textarea wire:model="competencias" rows="3" placeholder="Competências desejadas..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Benefícios</label>
                            <textarea wire:model="beneficios" rows="2" placeholder="VT, VR, Plano de saúde..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Nota mínima de aprovação (%)</label>
                                <input wire:model="notaMinima" type="number" min="0" max="100"
                                       class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular" />
                            </div>
                            <div>
                                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">SLA (dias)</label>
                                <input wire:model="slaDias" type="number" min="1" placeholder="Ex: 30"
                                       class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular" />
                            </div>
                        </div>
                    </div>

                    {{-- Configurações finais --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Vagas disponíveis</label>
                            <input wire:model="vagasDisp" type="number" min="1"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular" />
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Data encerramento</label>
                            <input wire:model="dataEnc" type="date"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Status da vaga</label>
                            <select wire:model="status"
                                    class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none">
                                <option value="rascunho">Rascunho</option>
                                <option value="publicada">Publicada</option>
                                <option value="pausada">Pausada</option>
                                <option value="encerrada">Encerrada</option>
                            </select>
                        </div>
                    </div>

                    {{-- Etapas do processo --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs lato-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Etapas do Processo Seletivo</h4>
                            <button wire:click="addEtapa" type="button" class="text-xs text-blue-500 hover:underline lato-bold">+ Adicionar etapa</button>
                        </div>
                        <div class="space-y-2">
                            @foreach ($etapas as $i => $etapa)
                                <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                                    <div class="w-3 h-3 rounded-full shrink-0 {{ $etapa['cor'] }}"></div>
                                    <input wire:model="etapas.{{ $i }}.nome" type="text"
                                           class="flex-1 text-xs bg-transparent border-none outline-none text-slate-700 dark:text-slate-200 lato-regular" />
                                    <select wire:model="etapas.{{ $i }}.cor"
                                            class="text-xs bg-transparent border border-slate-200 dark:border-slate-600 rounded-lg px-2 py-1 text-slate-600 dark:text-slate-300">
                                        @foreach ($etapaCores as $cor)
                                            <option value="{{ $cor }}">{{ str_replace(['bg-','-400','-500'], ['','',''], $cor) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="flex items-center gap-2 text-[10px] text-slate-500 lato-regular shrink-0">
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="checkbox" wire:model="etapas.{{ $i }}.is_aprovado" class="rounded" />
                                            Aprova
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="checkbox" wire:model="etapas.{{ $i }}.is_reprovado" class="rounded" />
                                            Reprova
                                        </label>
                                    </div>
                                    @if (count($etapas) > 1)
                                        <button wire:click="removeEtapa({{ $i }})" type="button" class="p-1 text-slate-400 hover:text-red-500 transition shrink-0">
                                            <x-lucide-x class="w-3.5 h-3.5" />
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="flex justify-end gap-2 p-5 border-t border-slate-200 dark:border-slate-700 sticky bottom-0 bg-white dark:bg-slate-800 rounded-b-2xl">
                    <button wire:click="$set('vagaModal', false)" type="button"
                            class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button wire:click="saveVaga" type="button" wire:loading.attr="disabled"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 disabled:opacity-50 transition shadow-sm">
                        <span wire:loading.remove wire:target="saveVaga">{{ $vagaId ? 'Salvar alterações' : 'Criar vaga' }}</span>
                        <span wire:loading wire:target="saveVaga">Salvando…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════ MODAL EXCLUIR ══════ --}}
    @if ($deleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <x-lucide-trash-2 class="w-6 h-6 text-red-500" />
                </div>
                <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 mb-1">Excluir vaga?</h3>
                <p class="text-xs text-slate-500 lato-regular mb-5">Todos os dados e candidaturas serão apagados permanentemente.</p>
                <div class="flex gap-2 justify-center">
                    <button wire:click="$set('deleteModal', false)" type="button"
                            class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button wire:click="deleteVaga" type="button" wire:loading.attr="disabled"
                            class="px-5 py-2.5 rounded-xl bg-red-500 text-white text-sm lato-bold hover:bg-red-600 disabled:opacity-50 transition">
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
