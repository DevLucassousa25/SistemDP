<?php

namespace App\Livewire\Pages\Equipamentos;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Livewire\SecureComponent;
use App\Models\Equipamento;
use App\Models\EquipamentoAtribuicao;
use App\Models\ConfiguracaoTermo;
use App\Models\EquipamentoInventario;
use App\Models\EquipamentoInventarioItem;
use App\Models\EquipamentoManutencao;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\WithFileUploads;

class Index extends SecureComponent
{
    use EnviaNotificacoes, WithFileUploads;

    // ── Navegação ─────────────────────────────────────────────────────────────
    public string $aba = 'dashboard';

    // ── Filtros (Ativos) ──────────────────────────────────────────────────────
    public string $searchAtivo      = '';
    public string $filtroCategoria  = '';
    public string $filtroStatus     = '';

    // ── Filtros (Entregas) ────────────────────────────────────────────────────
    public string $searchEntrega    = '';
    public string $filtroTipoEntrega = '';

    // ── Filtros (Manutenção) ──────────────────────────────────────────────────
    public string $searchManutencao   = '';
    public string $filtroManutStatus  = '';

    // ── Filtros (Relatório) ───────────────────────────────────────────────────
    public string $relPeriodoDe  = '';
    public string $relPeriodoAte = '';
    public string $relCategoria  = '';

    // ── Drawer: Detalhes do equipamento ──────────────────────────────────────
    public bool   $equipDrawerOpen   = false;
    #[Locked]
    public ?int   $equipDrawerId     = null;

    // ── Modal: Equipamento ────────────────────────────────────────────────────
    public bool   $modalEquipamento = false;
    public string $modoEquipamento  = 'create';
    #[Locked]
    public ?int   $editEquipamentoId = null;

    public string $eqNome            = '';
    public string $eqCategoria       = 'notebook';
    public string $eqDescricao       = '';
    public string $eqNumeroSerie     = '';
    public string $eqCodPatrimonio   = '';
    public string $eqMarca           = '';
    public string $eqModelo          = '';
    public string $eqStatus          = 'disponivel';
    public string $eqDataAquisicao   = '';
    public string $eqDataGarantia    = '';
    public string $eqLocal           = '';
    public string $eqValor           = '';
    public string $eqTaxaDepreciacao = '';
    public string $eqObservacoes     = '';

    // ── Modal: Atribuição ─────────────────────────────────────────────────────
    public bool   $modalAtribuicao   = false;
    public string $modoAtribuicao    = 'entrega';
    #[Locked]
    public ?int   $atribuicaoEquipamentoId = null;
    #[Locked]
    public ?int   $atribuicaoId      = null;

    public ?int   $atFuncionarioId   = null;
    public string $atDataEntrega     = '';
    public string $atCondicaoEntrega = 'novo';
    public string $atObservacoes     = '';

    // ── Modal: Manutenção ─────────────────────────────────────────────────────
    public bool   $modalManutencao    = false;
    public string $modoManutencao     = 'create';
    #[Locked]
    public ?int   $editManutencaoId   = null;
    #[Locked]
    public ?int   $manutEquipamentoId = null;

    public string $mnTitulo          = '';
    public string $mnDescricao       = '';
    public string $mnTipo            = 'corretiva';
    public string $mnStatus          = 'aberta';
    public string $mnDataEntrada     = '';
    public string $mnDataPrevisao    = '';
    public string $mnDataConclusao   = '';
    public string $mnFornecedor      = '';
    public string $mnCustoEstimado   = '';
    public string $mnCustoReal       = '';
    public string $mnResolucao       = '';
    public string $mnObservacoes     = '';

    // ── Modal: Gerar Termo (preenchimento de campos extras) ───────────────────
    public bool   $modalGerarTermo        = false;
    #[Locked]
    public ?int   $gerarTermoAtribuicaoId = null;
    public ?int   $gerarTermoTemplateId   = null;  // template selecionado para gerar
    public array  $gerarTermoExtras       = [];    // ['campo_id' => 'valor']
    public array  $gerarTermoCampos       = [];    // definições dos campos ativos

    // ── Editor de Termo ───────────────────────────────────────────────────────
    public bool    $modalTermoPreview      = false;
    // Template sendo editado
    #[Locked]
    public ?int    $termoTemplateId        = null;
    public string  $termoTemplateNome      = '';
    public bool    $termoTemplateEhPadrao  = false;
    // Biblioteca de templates
    public bool    $modalBibliotecaTemplates = false;
    // Modal: salvar como novo template
    public bool    $modalSalvarTemplate    = false;
    public string  $novoTemplateNome       = '';
    // Modal: renomear template
    public bool    $modalRenomearTemplate  = false;
    public string  $renomearTemplateNome   = '';
    // Modal: confirmar exclusão de template
    public bool    $modalConfirmExcluirTemplate = false;
    #[Locked]
    public ?int    $excluirTemplateId      = null;
    public string  $excluirTemplateNome    = '';

    // ── Dialog de confirmação genérico ────────────────────────────────────────
    public bool    $confirmDialog     = false;
    public string  $confirmTitle      = '';
    public string  $confirmMessage    = '';
    public string  $confirmType       = 'danger'; // danger | warning
    public string  $confirmAction     = '';
    public ?int    $confirmActionArg  = null;
    public string  $confirmLabel      = 'Confirmar';
    // Campos do editor
    public string $termoTitulo         = '';
    public string $termoSubtitulo      = '';
    public string $termoIntroTexto     = '';
    public string $termoRodapeTexto    = '';
    public array  $termoClausulas      = [];
    public array  $termoCamposVisiveis = [];
    public array  $termoCamposExtras   = [];
    public array  $termoAssinaturas    = [];
    // Logo do termo
    public ?string $termoLogoPath     = null;
    public string  $termoLogoPosicao  = 'esquerda';
    public $termoLogoFile             = null;

    // Campos disponíveis para toggle
    public array $termoCamposDisponiveis = [
        'numero_serie'      => 'Número de Série',
        'codigo_patrimonio' => 'Código de Patrimônio',
        'marca'             => 'Marca',
        'modelo'            => 'Modelo',
        'condicao'          => 'Condição na Entrega',
        'data_entrega'      => 'Data de Entrega',
        'garantia'          => 'Garantia até',
        'local'             => 'Local/Setor',
        'valor'             => 'Valor do Bem',
        'departamento'      => 'Departamento',
        'cargo'             => 'Cargo',
        'email'             => 'E-mail',
        'observacoes'       => 'Observações',
    ];

    // ── Inventário ────────────────────────────────────────────────────────────
    public bool   $modalInventario    = false;
    #[Locked]
    public ?int   $inventarioAtivoId  = null;
    public string $invTitulo          = '';
    public string $invObservacoes     = '';
    // Modal: observação de item (divergência / não encontrado)
    public bool   $modalObsItem   = false;
    #[Locked]
    public ?int   $obsItemId      = null;
    public string $obsItemStatus  = '';
    public string $obsItemNome    = '';
    public string $obsItemTexto   = '';

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->autorizar();
        $this->atDataEntrega  = now()->toDateString();
        $this->mnDataEntrada  = now()->toDateString();
        $this->relPeriodoDe   = now()->startOfMonth()->toDateString();
        $this->relPeriodoAte  = now()->toDateString();
        $this->carregarTermoConfig();
    }

    // ── Autorização ───────────────────────────────────────────────────────────

    private function autorizar(): void
    {
        $this->requireAuth();
        $user  = Auth::user();
        $isTi  = str_contains(strtolower($user->department?->name ?? ''), 'ti')
               || str_contains(strtolower($user->department?->name ?? ''), 'tecnologia')
               || str_contains(strtolower($user->department?->name ?? ''), 'informação');
        if (! $user->isRhOuDp() && ! $isTi) {
            abort(403, 'Acesso restrito ao RH/DP e Departamento de TI.');
        }
    }

    // ── Computed: Dashboard ───────────────────────────────────────────────────

    #[Computed]
    public function dashStats(): array
    {
        $total       = Equipamento::count();
        $disponivel  = Equipamento::where('status', 'disponivel')->count();
        $emUso       = Equipamento::where('status', 'em_uso')->count();
        $manutencao  = Equipamento::where('status', 'manutencao')->count();
        $descartado  = Equipamento::where('status', 'descartado')->count();

        $entregasHoje    = EquipamentoAtribuicao::whereDate('data_entrega', today())->count();
        $devolucoesHoje  = EquipamentoAtribuicao::whereDate('data_devolucao', today())->count();

        $porCategoria = Equipamento::selectRaw('categoria, count(*) as total')
            ->groupBy('categoria')
            ->pluck('total', 'categoria')
            ->toArray();

        $garantiaVencendo = Equipamento::garantiaVencendo(30)->count();
        $garantiaVencida  = Equipamento::garantiaVencida()->count();

        $manutAbertas = EquipamentoManutencao::whereIn('status', ['aberta', 'em_andamento'])->count();

        // Valor total do patrimônio
        $valorTotal = Equipamento::whereNotNull('valor')->sum('valor');

        $invAtivo = EquipamentoInventario::where('status', 'em_andamento')->latest()->first();

        return compact(
            'total', 'disponivel', 'emUso', 'manutencao', 'descartado',
            'entregasHoje', 'devolucoesHoje', 'porCategoria',
            'garantiaVencendo', 'garantiaVencida', 'manutAbertas',
            'valorTotal', 'invAtivo'
        );
    }

    // ── Computed: Ativos ──────────────────────────────────────────────────────

    #[Computed]
    public function equipamentos()
    {
        return Equipamento::with('cadastradoPor')
            ->when($this->searchAtivo, fn ($q) =>
                $q->where(fn ($sq) =>
                    $sq->where('nome', 'ilike', "%{$this->searchAtivo}%")
                       ->orWhere('numero_serie', 'ilike', "%{$this->searchAtivo}%")
                       ->orWhere('codigo_patrimonio', 'ilike', "%{$this->searchAtivo}%")
                       ->orWhere('marca', 'ilike', "%{$this->searchAtivo}%")
                       ->orWhere('modelo', 'ilike', "%{$this->searchAtivo}%")
                )
            )
            ->when($this->filtroCategoria, fn ($q) => $q->where('categoria', $this->filtroCategoria))
            ->when($this->filtroStatus,    fn ($q) => $q->where('status', $this->filtroStatus))
            ->orderBy('nome')
            ->get();
    }

    // ── Computed: Entregas ────────────────────────────────────────────────────

    #[Computed]
    public function atribuicoes()
    {
        return EquipamentoAtribuicao::with(['equipamento', 'funcionario', 'responsavel'])
            ->when($this->searchEntrega, fn ($q) =>
                $q->whereHas('funcionario', fn ($sq) =>
                    $sq->where('name', 'ilike', "%{$this->searchEntrega}%")
                       ->orWhere('email', 'ilike', "%{$this->searchEntrega}%")
                )->orWhereHas('equipamento', fn ($sq) =>
                    $sq->where('nome', 'ilike', "%{$this->searchEntrega}%")
                )
            )
            ->when($this->filtroTipoEntrega, fn ($q) => $q->where('tipo', $this->filtroTipoEntrega))
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();
    }

    // ── Computed: Manutenções ─────────────────────────────────────────────────

    #[Computed]
    public function manutencoes()
    {
        return EquipamentoManutencao::with(['equipamento', 'registradoPor'])
            ->when($this->searchManutencao, fn ($q) =>
                $q->where('titulo', 'ilike', "%{$this->searchManutencao}%")
                  ->orWhereHas('equipamento', fn ($sq) =>
                      $sq->where('nome', 'ilike', "%{$this->searchManutencao}%")
                  )
            )
            ->when($this->filtroManutStatus, fn ($q) => $q->where('status', $this->filtroManutStatus))
            ->orderByDesc('created_at')
            ->get();
    }

    // ── Computed: Inventários ─────────────────────────────────────────────────

    #[Computed]
    public function inventarios()
    {
        return EquipamentoInventario::with(['criadoPor', 'itens'])
            ->orderByDesc('created_at')
            ->get();
    }

    #[Computed]
    public function inventarioAtivo(): ?EquipamentoInventario
    {
        if (! $this->inventarioAtivoId) return null;
        return EquipamentoInventario::with([
            'criadoPor',
            'itens.equipamento',
            'itens.conferidoPor',
        ])->find($this->inventarioAtivoId);
    }

    // ── Computed: Drawer ──────────────────────────────────────────────────────

    #[Computed]
    public function equipDrawerData(): ?Equipamento
    {
        if (! $this->equipDrawerId) return null;
        return Equipamento::with([
            'cadastradoPor:id,name',
            'atribuicoes' => fn ($q) => $q
                ->with([
                    'funcionario:id,name,position,department_id',
                    'funcionario.department:id,name',
                    'responsavel:id,name',
                ])
                ->orderByDesc('data_entrega'),
            'manutencoes' => fn ($q) => $q->orderByDesc('data_entrada')->limit(5),
        ])->find($this->equipDrawerId);
    }

    // ── Computed: Funcionários ────────────────────────────────────────────────

    #[Computed]
    public function funcionarios()
    {
        return User::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'department_id']);
    }

    // ── Computed: Relatório ───────────────────────────────────────────────────

    #[Computed]
    public function relatorioData(): array
    {
        $de  = $this->relPeriodoDe  ?: now()->startOfMonth()->toDateString();
        $ate = $this->relPeriodoAte ?: now()->toDateString();

        $entregas = EquipamentoAtribuicao::with(['equipamento', 'funcionario', 'funcionario.department'])
            ->where('tipo', 'entrega')
            ->whereBetween('data_entrega', [$de, $ate])
            ->when($this->relCategoria, fn ($q) =>
                $q->whereHas('equipamento', fn ($sq) => $sq->where('categoria', $this->relCategoria))
            )
            ->get();

        $devolucoes = EquipamentoAtribuicao::with(['equipamento', 'funcionario'])
            ->where('tipo', 'entrega')
            ->whereNotNull('data_devolucao')
            ->whereBetween('data_devolucao', [$de, $ate])
            ->when($this->relCategoria, fn ($q) =>
                $q->whereHas('equipamento', fn ($sq) => $sq->where('categoria', $this->relCategoria))
            )
            ->get();

        $porDepartamento = $entregas->groupBy(fn ($a) => $a->funcionario?->department?->name ?? 'Sem depto')
            ->map(fn ($g) => $g->count());

        $valorPatrimonio = Equipamento::whereNotNull('valor')
            ->when($this->relCategoria, fn ($q) => $q->where('categoria', $this->relCategoria))
            ->sum('valor');

        $custoManutencao = EquipamentoManutencao::whereNotNull('custo_real')
            ->whereBetween('data_conclusao', [$de, $ate])
            ->sum('custo_real');

        return compact('entregas', 'devolucoes', 'porDepartamento', 'valorPatrimonio', 'custoManutencao');
    }

    // ── Setters de aba ────────────────────────────────────────────────────────

    public function setAba(string $aba): void
    {
        $this->aba = $aba;
        unset($this->dashStats, $this->equipamentos, $this->atribuicoes, $this->manutencoes, $this->inventarios);
    }

    // ── Drawer: Detalhes ─────────────────────────────────────────────────────

    public function openEquipDrawer(int $id): void
    {
        $this->equipDrawerId   = $id;
        $this->equipDrawerOpen = true;
        unset($this->equipDrawerData);
    }

    public function closeEquipDrawer(): void
    {
        $this->equipDrawerOpen = false;
        $this->equipDrawerId   = null;
    }

    // ── CRUD: Equipamento ─────────────────────────────────────────────────────

    public function novoEquipamento(): void
    {
        $this->autorizar();
        $this->resetEquipamentoForm();
        $this->modoEquipamento   = 'create';
        $this->editEquipamentoId = null;
        $this->modalEquipamento  = true;
    }

    public function editarEquipamento(int $id): void
    {
        $this->autorizar();
        $eq = Equipamento::findOrFail($id);
        $this->modoEquipamento      = 'edit';
        $this->editEquipamentoId    = $eq->id;
        $this->eqNome               = $eq->nome;
        $this->eqCategoria          = $eq->categoria;
        $this->eqDescricao          = $eq->descricao ?? '';
        $this->eqNumeroSerie        = $eq->numero_serie ?? '';
        $this->eqCodPatrimonio      = $eq->codigo_patrimonio ?? '';
        $this->eqMarca              = $eq->marca ?? '';
        $this->eqModelo             = $eq->modelo ?? '';
        $this->eqStatus             = $eq->status;
        $this->eqDataAquisicao      = $eq->data_aquisicao?->toDateString() ?? '';
        $this->eqDataGarantia       = $eq->data_garantia?->toDateString() ?? '';
        $this->eqLocal              = $eq->local ?? '';
        $this->eqValor              = $eq->valor ? number_format((float)$eq->valor, 2, '.', '') : '';
        $this->eqTaxaDepreciacao    = $eq->taxa_depreciacao ? number_format((float)$eq->taxa_depreciacao, 2, '.', '') : '';
        $this->eqObservacoes        = $eq->observacoes ?? '';
        $this->modalEquipamento     = true;
    }

    public function salvarEquipamento(): void
    {
        $this->autorizar();

        $rules = [
            'eqNome'             => 'required|string|max:200',
            'eqCategoria'        => 'required|string',
            'eqNumeroSerie'      => 'nullable|string|max:100',
            'eqCodPatrimonio'    => 'nullable|string|max:100',
            'eqMarca'            => 'nullable|string|max:100',
            'eqModelo'           => 'nullable|string|max:100',
            'eqStatus'           => 'required|in:disponivel,em_uso,manutencao,descartado',
            'eqDataAquisicao'    => 'nullable|date',
            'eqDataGarantia'     => 'nullable|date',
            'eqLocal'            => 'nullable|string|max:100',
            'eqValor'            => 'nullable|numeric|min:0',
            'eqTaxaDepreciacao'  => 'nullable|numeric|min:0|max:100',
        ];

        if ($this->modoEquipamento === 'edit') {
            $rules['eqNumeroSerie']   .= '|unique:equipamentos,numero_serie,' . $this->editEquipamentoId;
            $rules['eqCodPatrimonio'] .= '|unique:equipamentos,codigo_patrimonio,' . $this->editEquipamentoId;
        } else {
            $rules['eqNumeroSerie']   .= '|unique:equipamentos,numero_serie';
            $rules['eqCodPatrimonio'] .= '|unique:equipamentos,codigo_patrimonio';
        }

        $this->validate($rules, ['eqNome.required' => 'O nome é obrigatório.']);

        $data = [
            'nome'              => $this->sanitize($this->eqNome),
            'categoria'         => $this->eqCategoria,
            'descricao'         => $this->sanitize($this->eqDescricao) ?: null,
            'numero_serie'      => $this->sanitize($this->eqNumeroSerie) ?: null,
            'codigo_patrimonio' => $this->sanitize($this->eqCodPatrimonio) ?: null,
            'marca'             => $this->sanitize($this->eqMarca) ?: null,
            'modelo'            => $this->sanitize($this->eqModelo) ?: null,
            'status'            => $this->eqStatus,
            'data_aquisicao'    => $this->eqDataAquisicao ?: null,
            'data_garantia'     => $this->eqDataGarantia ?: null,
            'local'             => $this->sanitize($this->eqLocal) ?: null,
            'valor'             => $this->eqValor ?: null,
            'taxa_depreciacao'  => $this->eqTaxaDepreciacao ?: null,
            'observacoes'       => $this->sanitize($this->eqObservacoes) ?: null,
            'cadastrado_por'    => Auth::id(),
        ];

        if ($this->modoEquipamento === 'edit') {
            Equipamento::findOrFail($this->editEquipamentoId)->update($data);
            $this->alertSuccess('Equipamento atualizado!');
        } else {
            Equipamento::create($data);
            $this->alertSuccess('Equipamento cadastrado com sucesso!');
        }

        unset($this->equipamentos, $this->dashStats, $this->equipDrawerData);
        $this->modalEquipamento = false;
        $this->resetEquipamentoForm();
    }

    public function excluirEquipamento(int $id): void
    {
        $this->autorizar();
        $eq = Equipamento::findOrFail($id);
        if ($eq->atribuicoes()->whereNull('data_devolucao')->where('tipo', 'entrega')->exists()) {
            $this->alertError('Não é possível excluir', 'Este equipamento está atualmente atribuído a um funcionário.');
            return;
        }
        $eq->delete();
        unset($this->equipamentos, $this->dashStats);
        $this->alertSuccess('Equipamento excluído.');
    }

    private function resetEquipamentoForm(): void
    {
        $this->eqNome = $this->eqDescricao = $this->eqNumeroSerie = '';
        $this->eqCodPatrimonio = $this->eqMarca = $this->eqModelo = '';
        $this->eqObservacoes = $this->eqDataAquisicao = $this->eqDataGarantia = '';
        $this->eqValor = $this->eqTaxaDepreciacao = $this->eqLocal = '';
        $this->eqCategoria = 'notebook';
        $this->eqStatus    = 'disponivel';
    }

    // ── CRUD: Atribuição ──────────────────────────────────────────────────────

    public function abrirEntrega(int $equipamentoId): void
    {
        $this->autorizar();
        $eq = Equipamento::findOrFail($equipamentoId);
        if ($eq->status !== 'disponivel') {
            $this->alertError('Equipamento indisponível', 'Apenas equipamentos disponíveis podem ser entregues.');
            return;
        }
        $this->modoAtribuicao          = 'entrega';
        $this->atribuicaoEquipamentoId = $equipamentoId;
        $this->atribuicaoId            = null;
        $this->atFuncionarioId         = null;
        $this->atDataEntrega           = now()->toDateString();
        $this->atCondicaoEntrega       = 'novo';
        $this->atObservacoes           = '';
        $this->modalAtribuicao         = true;
    }

    public function abrirDevolucao(int $atribuicaoId): void
    {
        $this->autorizar();
        $atr = EquipamentoAtribuicao::with('equipamento', 'funcionario')->findOrFail($atribuicaoId);
        $this->modoAtribuicao          = 'devolucao';
        $this->atribuicaoId            = $atribuicaoId;
        $this->atribuicaoEquipamentoId = $atr->equipamento_id;
        $this->atFuncionarioId         = $atr->user_id;
        $this->atDataEntrega           = now()->toDateString();
        $this->atCondicaoEntrega       = 'bom';
        $this->atObservacoes           = '';
        $this->modalAtribuicao         = true;
    }

    public function salvarAtribuicao(): void
    {
        $this->autorizar();
        $this->validate([
            'atFuncionarioId'   => 'required|exists:users,id',
            'atDataEntrega'     => 'required|date',
            'atCondicaoEntrega' => 'required|string',
        ], ['atFuncionarioId.required' => 'Selecione um funcionário.']);

        $eq = Equipamento::findOrFail($this->atribuicaoEquipamentoId);

        if ($this->modoAtribuicao === 'entrega') {
            EquipamentoAtribuicao::create([
                'equipamento_id'   => $eq->id,
                'user_id'          => $this->atFuncionarioId,
                'responsavel_id'   => Auth::id(),
                'tipo'             => 'entrega',
                'data_entrega'     => $this->atDataEntrega,
                'condicao_entrega' => $this->atCondicaoEntrega,
                'observacoes'      => $this->sanitize($this->atObservacoes) ?: null,
            ]);
            $eq->update(['status' => 'em_uso']);
            $this->alertSuccess('Equipamento entregue!', 'Registro salvo com sucesso.');
        } else {
            $atr = EquipamentoAtribuicao::findOrFail($this->atribuicaoId);
            $atr->update([
                'data_devolucao'     => $this->atDataEntrega,
                'condicao_devolucao' => $this->atCondicaoEntrega,
                'observacoes'        => $this->sanitize($this->atObservacoes) ?: null,
            ]);
            $outrasAtivas = EquipamentoAtribuicao::where('equipamento_id', $eq->id)
                ->where('id', '!=', $atr->id)
                ->whereNull('data_devolucao')
                ->where('tipo', 'entrega')
                ->exists();
            if (! $outrasAtivas) {
                $eq->update(['status' => 'disponivel']);
            }
            $this->alertSuccess('Devolução registrada!');
        }

        unset($this->equipamentos, $this->atribuicoes, $this->dashStats, $this->equipDrawerData);
        $this->modalAtribuicao = false;
    }

    // ── CRUD: Manutenção ──────────────────────────────────────────────────────

    public function novaManutencao(?int $equipamentoId = null): void
    {
        $this->autorizar();
        $this->resetManutencaoForm();
        $this->modoManutencao     = 'create';
        $this->editManutencaoId   = null;
        $this->manutEquipamentoId = $equipamentoId;
        $this->modalManutencao    = true;
    }

    public function setManutEquipamento(int $id): void
    {
        $this->manutEquipamentoId = $id;
    }

    public function editarManutencao(int $id): void
    {
        $this->autorizar();
        $mn = EquipamentoManutencao::findOrFail($id);
        $this->modoManutencao     = 'edit';
        $this->editManutencaoId   = $mn->id;
        $this->manutEquipamentoId = $mn->equipamento_id;
        $this->mnTitulo           = $mn->titulo;
        $this->mnDescricao        = $mn->descricao ?? '';
        $this->mnTipo             = $mn->tipo;
        $this->mnStatus           = $mn->status;
        $this->mnDataEntrada      = $mn->data_entrada?->toDateString() ?? '';
        $this->mnDataPrevisao     = $mn->data_previsao?->toDateString() ?? '';
        $this->mnDataConclusao    = $mn->data_conclusao?->toDateString() ?? '';
        $this->mnFornecedor       = $mn->fornecedor ?? '';
        $this->mnCustoEstimado    = $mn->custo_estimado ? number_format((float)$mn->custo_estimado, 2, '.', '') : '';
        $this->mnCustoReal        = $mn->custo_real ? number_format((float)$mn->custo_real, 2, '.', '') : '';
        $this->mnResolucao        = $mn->resolucao ?? '';
        $this->mnObservacoes      = $mn->observacoes ?? '';
        $this->modalManutencao    = true;
    }

    public function salvarManutencao(): void
    {
        $this->autorizar();
        $this->validate([
            'mnTitulo'        => 'required|string|max:200',
            'manutEquipamentoId' => 'required|exists:equipamentos,id',
            'mnTipo'          => 'required|in:corretiva,preventiva,calibracao',
            'mnStatus'        => 'required|in:aberta,em_andamento,concluida,cancelada',
            'mnDataEntrada'   => 'required|date',
            'mnDataPrevisao'  => 'nullable|date',
            'mnDataConclusao' => 'nullable|date',
            'mnCustoEstimado' => 'nullable|numeric|min:0',
            'mnCustoReal'     => 'nullable|numeric|min:0',
        ], ['mnTitulo.required' => 'Informe o título da manutenção.']);

        $data = [
            'equipamento_id'  => $this->manutEquipamentoId,
            'registrado_por'  => Auth::id(),
            'titulo'          => $this->sanitize($this->mnTitulo),
            'descricao'       => $this->sanitize($this->mnDescricao) ?: null,
            'tipo'            => $this->mnTipo,
            'status'          => $this->mnStatus,
            'data_entrada'    => $this->mnDataEntrada,
            'data_previsao'   => $this->mnDataPrevisao ?: null,
            'data_conclusao'  => $this->mnDataConclusao ?: null,
            'fornecedor'      => $this->sanitize($this->mnFornecedor) ?: null,
            'custo_estimado'  => $this->mnCustoEstimado ?: null,
            'custo_real'      => $this->mnCustoReal ?: null,
            'resolucao'       => $this->sanitize($this->mnResolucao) ?: null,
            'observacoes'     => $this->sanitize($this->mnObservacoes) ?: null,
        ];

        if ($this->modoManutencao === 'edit') {
            EquipamentoManutencao::findOrFail($this->editManutencaoId)->update($data);
            // Atualiza status do equipamento conforme manutenção
            $eq = Equipamento::findOrFail($this->manutEquipamentoId);
            if ($this->mnStatus === 'concluida' && $eq->status === 'manutencao') {
                $eq->update(['status' => 'disponivel']);
            }
            $this->alertSuccess('Manutenção atualizada!');
        } else {
            EquipamentoManutencao::create($data);
            // Coloca equipamento em manutenção se estava disponível
            $eq = Equipamento::findOrFail($this->manutEquipamentoId);
            if (in_array($eq->status, ['disponivel'])) {
                $eq->update(['status' => 'manutencao']);
            }
            $this->alertSuccess('Manutenção registrada!');
        }

        unset($this->manutencoes, $this->dashStats, $this->equipamentos, $this->equipDrawerData);
        $this->modalManutencao = false;
        $this->resetManutencaoForm();
    }

    // ── Ações rápidas de manutenção ───────────────────────────────────────────

    public function iniciarManutencao(int $id): void
    {
        $this->autorizar();
        $mn = EquipamentoManutencao::findOrFail($id);

        if ($mn->status !== 'aberta') return;

        $mn->update(['status' => 'em_andamento']);

        // Coloca equipamento em manutenção
        $mn->equipamento->update(['status' => 'manutencao']);

        unset($this->manutencoes, $this->dashStats);
        $this->alertSuccess('Manutenção iniciada!', 'Status atualizado para Em andamento.');
    }

    // Modal: encerrar manutenção
    public bool   $modalEncerrarManut  = false;
    #[Locked]
    public ?int   $encerrarManutId     = null;
    public string $encerrarDataConclusao = '';
    public string $encerrarCustoReal     = '';
    public string $encerrarResolucao     = '';

    public function abrirEncerrarManut(int $id): void
    {
        $mn = EquipamentoManutencao::findOrFail($id);
        $this->encerrarManutId       = $id;
        $this->encerrarDataConclusao = now()->toDateString();
        $this->encerrarCustoReal     = $mn->custo_real
            ? number_format((float)$mn->custo_real, 2, '.', '')
            : ($mn->custo_estimado ? number_format((float)$mn->custo_estimado, 2, '.', '') : '');
        $this->encerrarResolucao     = $mn->resolucao ?? '';
        $this->modalEncerrarManut    = true;
    }

    public function confirmarEncerrarManut(): void
    {
        $this->autorizar();

        $this->validate([
            'encerrarDataConclusao' => 'required|date',
            'encerrarCustoReal'     => 'nullable|numeric|min:0',
            'encerrarResolucao'     => 'nullable|string|max:1000',
        ], [
            'encerrarDataConclusao.required' => 'Informe a data de conclusão.',
            'encerrarDataConclusao.date'     => 'Data inválida.',
        ]);

        $mn = EquipamentoManutencao::findOrFail($this->encerrarManutId);
        $mn->update([
            'status'          => 'concluida',
            'data_conclusao'  => $this->encerrarDataConclusao,
            'custo_real'      => $this->encerrarCustoReal ?: null,
            'resolucao'       => trim($this->encerrarResolucao) ?: null,
        ]);

        // Libera equipamento se estava em manutenção
        if ($mn->equipamento->status === 'manutencao') {
            $mn->equipamento->update(['status' => 'disponivel']);
        }

        $this->modalEncerrarManut    = false;
        $this->encerrarManutId       = null;
        $this->encerrarDataConclusao = '';
        $this->encerrarCustoReal     = '';
        $this->encerrarResolucao     = '';
        unset($this->manutencoes, $this->dashStats);
        $this->alertSuccess('Manutenção encerrada!', 'Equipamento marcado como disponível.');
    }

    public function excluirManutencao(int $id): void
    {
        $this->autorizar();
        EquipamentoManutencao::findOrFail($id)->delete();
        unset($this->manutencoes, $this->dashStats);
        $this->alertSuccess('Registro removido.');
    }

    private function resetManutencaoForm(): void
    {
        $this->mnTitulo = $this->mnDescricao = $this->mnFornecedor = '';
        $this->mnResolucao = $this->mnObservacoes = '';
        $this->mnDataPrevisao = $this->mnDataConclusao = '';
        $this->mnCustoEstimado = $this->mnCustoReal = '';
        $this->mnTipo   = 'corretiva';
        $this->mnStatus = 'aberta';
        $this->mnDataEntrada = now()->toDateString();
        $this->manutEquipamentoId = null;
    }

    // ── Inventário ────────────────────────────────────────────────────────────

    public function iniciarInventario(): void
    {
        $this->autorizar();
        $this->validate([
            'invTitulo' => 'required|string|max:200',
        ], ['invTitulo.required' => 'Informe o título do inventário.']);

        $inv = EquipamentoInventario::create([
            'criado_por'  => Auth::id(),
            'titulo'      => $this->sanitize($this->invTitulo),
            'status'      => 'em_andamento',
            'data_inicio' => now()->toDateString(),
            'observacoes' => $this->sanitize($this->invObservacoes) ?: null,
        ]);

        // Cria item para cada equipamento ativo
        $equipamentos = Equipamento::whereNotIn('status', ['descartado'])->get();
        foreach ($equipamentos as $eq) {
            EquipamentoInventarioItem::create([
                'inventario_id'  => $inv->id,
                'equipamento_id' => $eq->id,
                'status'         => 'pendente',
            ]);
        }

        $this->inventarioAtivoId = $inv->id;
        $this->invTitulo         = '';
        $this->invObservacoes    = '';
        $this->modalInventario   = false;
        unset($this->inventarios, $this->inventarioAtivo, $this->dashStats);
        $this->alertSuccess('Inventário iniciado!', "{$equipamentos->count()} itens adicionados.");
    }

    public function abrirInventario(int $invId): void
    {
        $this->inventarioAtivoId = $invId;
        unset($this->inventarioAtivo);
    }

    public function fecharInventario(): void
    {
        $this->inventarioAtivoId = null;
        unset($this->inventarioAtivo);
    }

    public function conferirItem(int $itemId, string $status, ?string $observacao = null): void
    {
        $this->autorizar();
        EquipamentoInventarioItem::findOrFail($itemId)->update([
            'status'        => $status,
            'conferido_por' => Auth::id(),
            'conferido_at'  => now(),
            'observacao'    => $observacao,
        ]);
        unset($this->inventarioAtivo, $this->inventarios);
    }

    public function abrirObsItem(int $itemId, string $status, string $nome): void
    {
        $this->obsItemId     = $itemId;
        $this->obsItemStatus = $status;
        $this->obsItemNome   = $nome;
        $this->obsItemTexto  = EquipamentoInventarioItem::find($itemId)?->observacao ?? '';
        $this->modalObsItem  = true;
    }

    public function salvarObsItem(): void
    {
        $this->autorizar();

        $this->validate([
            'obsItemTexto' => 'required|string|min:5|max:500',
        ], [
            'obsItemTexto.required' => 'Informe uma explicação (mínimo 5 caracteres).',
            'obsItemTexto.min'      => 'A explicação deve ter ao menos 5 caracteres.',
        ]);

        EquipamentoInventarioItem::findOrFail($this->obsItemId)->update([
            'status'        => $this->obsItemStatus,
            'conferido_por' => Auth::id(),
            'conferido_at'  => now(),
            'observacao'    => trim($this->obsItemTexto),
        ]);

        $this->modalObsItem  = false;
        $this->obsItemId     = null;
        $this->obsItemTexto  = '';
        $this->obsItemNome   = '';
        $this->obsItemStatus = '';
        unset($this->inventarioAtivo, $this->inventarios);
    }

    public function cancelarInventario(int $invId): void
    {
        $this->autorizar();
        EquipamentoInventario::findOrFail($invId)->update([
            'status'         => 'cancelado',
            'data_conclusao' => now()->toDateString(),
        ]);
        $this->inventarioAtivoId = null;
        unset($this->inventarios, $this->inventarioAtivo, $this->dashStats);
        $this->alertSuccess('Inventário cancelado.');
    }

    public function abrirConfirm(
        string $title,
        string $message,
        string $action,
        ?int   $arg   = null,
        string $type  = 'danger',
        string $label = 'Confirmar'
    ): void {
        $this->confirmTitle     = $title;
        $this->confirmMessage   = $message;
        $this->confirmAction    = $action;
        $this->confirmActionArg = $arg;
        $this->confirmType      = $type;
        $this->confirmLabel     = $label;
        $this->confirmDialog    = true;
    }

    public function executarConfirmacao(): void
    {
        $this->confirmDialog = false;
        $action = $this->confirmAction;
        $arg    = $this->confirmActionArg;
        $this->confirmAction    = '';
        $this->confirmActionArg = null;

        if (! $action || ! method_exists($this, $action)) return;

        $arg !== null ? $this->$action($arg) : $this->$action();
    }

    public function concluirInventario(int $invId): void
    {
        $this->autorizar();
        EquipamentoInventario::findOrFail($invId)->update([
            'status'          => 'concluido',
            'data_conclusao'  => now()->toDateString(),
        ]);
        $this->inventarioAtivoId = null;
        unset($this->inventarios, $this->inventarioAtivo, $this->dashStats);
        $this->alertSuccess('Inventário concluído!');
    }

    // ── Editor de Termo ───────────────────────────────────────────────────────

    // ── Computed: todos os templates ─────────────────────────────────────────

    #[Computed]
    public function todosTemplates(): \Illuminate\Support\Collection
    {
        return ConfiguracaoTermo::orderByDesc('padrao')->orderBy('nome')->get();
    }

    // ── Carregamento de template ──────────────────────────────────────────────

    private function carregarTermoConfig(?int $templateId = null): void
    {
        $cfg = $templateId
            ? (ConfiguracaoTermo::find($templateId) ?? ConfiguracaoTermo::instancia())
            : ConfiguracaoTermo::instancia();

        $this->termoTemplateId       = $cfg->id;
        $this->termoTemplateNome     = $cfg->nome ?? 'Padrão';
        $this->termoTemplateEhPadrao = (bool) ($cfg->padrao ?? false);
        $this->termoTitulo           = $cfg->titulo          ?? '';
        $this->termoSubtitulo        = $cfg->subtitulo       ?? '';
        $this->termoIntroTexto       = $cfg->intro_texto     ?? '';
        $this->termoRodapeTexto      = $cfg->rodape_texto    ?? '';
        $this->termoClausulas        = $cfg->clausulas       ?? ConfiguracaoTermo::clausulasPadrao();
        $this->termoCamposVisiveis   = $cfg->campos_visiveis ?? ConfiguracaoTermo::camposPadraoVisiveis();
        $this->termoCamposExtras     = $cfg->campos_extras   ?? ConfiguracaoTermo::camposExtrasPadrao();
        $this->termoAssinaturas      = $cfg->assinaturas     ?? ConfiguracaoTermo::assinaturasPadrao();
        $this->termoLogoPath         = $cfg->logo            ?? null;
        $this->termoLogoPosicao      = $cfg->logo_posicao    ?? 'esquerda';
        $this->termoLogoFile         = null;
    }

    public function carregarTemplate(int $id): void
    {
        $this->autorizar();
        $this->carregarTermoConfig($id);
        $this->modalBibliotecaTemplates = false;
        unset($this->todosTemplates);
    }

    public function salvarTermoConfig(): void
    {
        $this->autorizar();
        $this->validate([
            'termoTitulo'      => 'required|string|max:300',
            'termoRodapeTexto' => 'nullable|string|max:300',
            'termoClausulas'   => 'array',
            'termoAssinaturas' => 'array|min:1',
        ], [
            'termoTitulo.required'     => 'O título do documento é obrigatório.',
            'termoAssinaturas.min'     => 'Adicione ao menos uma assinatura.',
        ]);

        // Filtrar cláusulas vazias
        $clausulas = array_values(array_filter(
            $this->termoClausulas,
            fn ($c) => ! empty(trim($c['texto'] ?? ''))
        ));

        // Processa upload de logo se houver arquivo temporário
        if ($this->termoLogoFile) {
            $this->validate(['termoLogoFile' => 'image|max:2048'], [
                'termoLogoFile.image' => 'O arquivo deve ser uma imagem.',
                'termoLogoFile.max'   => 'O logo deve ter no máximo 2 MB.',
            ]);
            // Remove logo anterior
            if ($this->termoLogoPath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->termoLogoPath);
            }
            $this->termoLogoPath = $this->termoLogoFile->store('termo-logos', 'public');
            $this->termoLogoFile = null;
        }

        $cfg = $this->termoTemplateId
            ? (ConfiguracaoTermo::find($this->termoTemplateId) ?? ConfiguracaoTermo::instancia())
            : ConfiguracaoTermo::instancia();

        $cfg->update([
            'titulo'          => $this->sanitize($this->termoTitulo),
            'subtitulo'       => $this->sanitize($this->termoSubtitulo),
            'intro_texto'     => $this->sanitize($this->termoIntroTexto),
            'clausulas'       => $clausulas,
            'campos_visiveis' => $this->termoCamposVisiveis,
            'campos_extras'   => $this->termoCamposExtras,
            'assinaturas'     => $this->termoAssinaturas,
            'rodape_texto'    => $this->sanitize($this->termoRodapeTexto),
            'logo'            => $this->termoLogoPath,
            'logo_posicao'    => $this->termoLogoPosicao,
            'atualizado_por'  => \Illuminate\Support\Facades\Auth::id(),
        ]);

        $this->termoClausulas        = $clausulas;
        $this->termoTemplateEhPadrao = (bool) $cfg->fresh()->padrao;
        unset($this->todosTemplates);
        $this->alertSuccess('Template salvo!', "\"{$cfg->nome}\" foi atualizado com sucesso.");
    }

    public function resetarTermoConfig(): void
    {
        $this->autorizar();
        $cfg = $this->termoTemplateId
            ? (ConfiguracaoTermo::find($this->termoTemplateId) ?? ConfiguracaoTermo::instancia())
            : ConfiguracaoTermo::instancia();

        $cfg->update([
            'titulo'          => 'Termo de Entrega e Responsabilidade de Equipamento',
            'subtitulo'       => '',
            'intro_texto'     => '',
            'clausulas'       => ConfiguracaoTermo::clausulasPadrao(),
            'campos_visiveis' => ConfiguracaoTermo::camposPadraoVisiveis(),
            'campos_extras'   => ConfiguracaoTermo::camposExtrasPadrao(),
            'assinaturas'     => ConfiguracaoTermo::assinaturasPadrao(),
            'rodape_texto'    => 'Documento de controle interno',
            'atualizado_por'  => \Illuminate\Support\Facades\Auth::id(),
        ]);
        $this->carregarTermoConfig($cfg->id);
        $this->alertSuccess('Conteúdo restaurado!', 'O conteúdo padrão foi restaurado.');
    }

    // ── Gestão de Templates ───────────────────────────────────────────────────

    public function abrirModalNovoTemplate(): void
    {
        $this->autorizar();
        $this->novoTemplateNome    = '';
        $this->modalSalvarTemplate = true;
    }

    public function confirmarNovoTemplate(): void
    {
        $this->autorizar();
        $this->validate(['novoTemplateNome' => 'required|string|max:150'], [
            'novoTemplateNome.required' => 'Informe um nome para o template.',
        ]);

        $novo = ConfiguracaoTermo::create([
            'nome'            => trim($this->novoTemplateNome),
            'padrao'          => false,
            'titulo'          => $this->sanitize($this->termoTitulo),
            'subtitulo'       => $this->sanitize($this->termoSubtitulo),
            'intro_texto'     => $this->sanitize($this->termoIntroTexto),
            'clausulas'       => array_values(array_filter($this->termoClausulas, fn($c) => !empty(trim($c['texto'] ?? '')))),
            'campos_visiveis' => $this->termoCamposVisiveis,
            'campos_extras'   => $this->termoCamposExtras,
            'assinaturas'     => $this->termoAssinaturas,
            'rodape_texto'    => $this->sanitize($this->termoRodapeTexto),
            'logo'            => $this->termoLogoPath,
            'logo_posicao'    => $this->termoLogoPosicao,
            'atualizado_por'  => \Illuminate\Support\Facades\Auth::id(),
        ]);

        $this->modalSalvarTemplate = false;
        $this->carregarTermoConfig($novo->id);
        unset($this->todosTemplates);
        $this->alertSuccess('Template criado!', "\"{$novo->nome}\" foi criado com sucesso.");
    }

    public function duplicarTemplate(int $id): void
    {
        $this->autorizar();
        $original = ConfiguracaoTermo::findOrFail($id);
        $novo     = $original->duplicar($original->nome . ' (cópia)');
        $this->carregarTermoConfig($novo->id);
        unset($this->todosTemplates);
        $this->alertSuccess('Template duplicado!', "\"{$novo->nome}\" foi criado.");
    }

    public function definirTemplatePadrao(int $id): void
    {
        $this->autorizar();
        $cfg = ConfiguracaoTermo::findOrFail($id);
        $cfg->definirComoPadrao();
        $this->termoTemplateEhPadrao = ($this->termoTemplateId === $id);
        unset($this->todosTemplates);
        $this->alertSuccess('Template padrão definido!', "\"{$cfg->nome}\" será usado por padrão ao gerar termos.");
    }

    public function abrirModalRenomear(): void
    {
        $this->renomearTemplateNome  = $this->termoTemplateNome;
        $this->modalRenomearTemplate = true;
    }

    public function confirmarRenomear(): void
    {
        $this->autorizar();
        $this->validate(['renomearTemplateNome' => 'required|string|max:150'], [
            'renomearTemplateNome.required' => 'Informe um nome.',
        ]);
        if ($this->termoTemplateId) {
            $cfg = ConfiguracaoTermo::findOrFail($this->termoTemplateId);
            $cfg->update(['nome' => trim($this->renomearTemplateNome)]);
            $this->termoTemplateNome = $cfg->nome;
            unset($this->todosTemplates);
        }
        $this->modalRenomearTemplate = false;
        $this->alertSuccess('Renomeado!');
    }

    public function abrirConfirmExcluirTemplate(int $id): void
    {
        $cfg = ConfiguracaoTermo::findOrFail($id);
        $this->excluirTemplateId   = $id;
        $this->excluirTemplateNome = $cfg->nome;
        $this->modalConfirmExcluirTemplate = true;
    }

    public function confirmarExcluirTemplate(): void
    {
        if (! $this->excluirTemplateId) return;
        $this->excluirTemplate($this->excluirTemplateId);
        $this->modalConfirmExcluirTemplate = false;
        $this->excluirTemplateId   = null;
        $this->excluirTemplateNome = '';
    }

    public function excluirTemplate(int $id): void
    {
        $this->autorizar();

        $total = ConfiguracaoTermo::count();
        if ($total <= 1) {
            $this->alertError('Não é possível excluir o único template.');
            return;
        }

        $cfg = ConfiguracaoTermo::findOrFail($id);
        $eraPadrao = $cfg->padrao;
        $cfg->delete();

        // Se era o padrão, promove o próximo
        if ($eraPadrao) {
            $prox = ConfiguracaoTermo::orderBy('id')->first();
            $prox?->update(['padrao' => true]);
        }

        // Se era o que estava sendo editado, carrega o padrão
        if ($this->termoTemplateId === $id) {
            $this->carregarTermoConfig();
        }

        unset($this->todosTemplates);
        $this->alertSuccess('Template excluído!');
    }

    // Logo
    public function removerLogo(): void
    {
        $this->autorizar();
        if ($this->termoLogoPath) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($this->termoLogoPath);
            ConfiguracaoTermo::instancia()->update(['logo' => null]);
            $this->termoLogoPath = null;
        }
        $this->termoLogoFile = null;
        $this->alertSuccess('Logo removido!');
    }

    // Cláusulas
    public function addClausula(): void
    {
        $this->termoClausulas[] = ['texto' => ''];
    }

    public function removerClausula(int $idx): void
    {
        array_splice($this->termoClausulas, $idx, 1);
        $this->termoClausulas = array_values($this->termoClausulas);
    }

    public function moverClausula(int $idx, string $dir): void
    {
        $total = count($this->termoClausulas);
        if ($dir === 'up' && $idx > 0) {
            [$this->termoClausulas[$idx - 1], $this->termoClausulas[$idx]] =
                [$this->termoClausulas[$idx], $this->termoClausulas[$idx - 1]];
        } elseif ($dir === 'down' && $idx < $total - 1) {
            [$this->termoClausulas[$idx + 1], $this->termoClausulas[$idx]] =
                [$this->termoClausulas[$idx], $this->termoClausulas[$idx + 1]];
        }
        $this->termoClausulas = array_values($this->termoClausulas);
    }

    public function toggleCampoVisivel(string $campo): void
    {
        if (in_array($campo, $this->termoCamposVisiveis)) {
            $this->termoCamposVisiveis = array_values(
                array_filter($this->termoCamposVisiveis, fn ($c) => $c !== $campo)
            );
        } else {
            $this->termoCamposVisiveis[] = $campo;
        }
    }

    // Assinaturas
    public function addAssinatura(): void
    {
        if (count($this->termoAssinaturas) < 4) {
            $this->termoAssinaturas[] = ['label' => '', 'papel' => ''];
        }
    }

    public function removerAssinatura(int $idx): void
    {
        array_splice($this->termoAssinaturas, $idx, 1);
        $this->termoAssinaturas = array_values($this->termoAssinaturas);
    }

    // Campos extras (editor)
    public function toggleCampoExtra(string $id): void
    {
        foreach ($this->termoCamposExtras as $idx => $campo) {
            if (($campo['id'] ?? '') === $id) {
                $this->termoCamposExtras[$idx]['ativo'] = ! ($campo['ativo'] ?? false);
                return;
            }
        }
    }

    public function addCampoExtraCustom(): void
    {
        $this->termoCamposExtras[] = [
            'id'    => 'custom_' . uniqid(),
            'label' => '',
            'tipo'  => 'custom',
            'ativo' => true,
            'mascara' => '',
        ];
    }

    public function removerCampoExtra(int $idx): void
    {
        array_splice($this->termoCamposExtras, $idx, 1);
        $this->termoCamposExtras = array_values($this->termoCamposExtras);
    }

    // ── Modal: Gerar Termo ────────────────────────────────────────────────────

    public function mudarTemplateGerar(int $id): void
    {
        $cfg = ConfiguracaoTermo::find($id) ?? ConfiguracaoTermo::instancia();
        $this->gerarTermoTemplateId = $cfg->id;
        $extras = $cfg->campos_extras ?? ConfiguracaoTermo::camposExtrasPadrao();
        $this->gerarTermoCampos = array_values(array_filter($extras, fn ($c) => ! empty($c['ativo'])));
        $this->gerarTermoExtras = [];
        foreach ($this->gerarTermoCampos as $campo) {
            $this->gerarTermoExtras[$campo['id']] = '';
        }
    }

    public function abrirGerarTermo(int $atribuicaoId): void
    {
        $this->autorizar();
        $this->gerarTermoAtribuicaoId = $atribuicaoId;

        // Usa o template padrão por default
        $cfg = ConfiguracaoTermo::instancia();
        $this->gerarTermoTemplateId = $cfg->id;
        $extras = $cfg->campos_extras ?? ConfiguracaoTermo::camposExtrasPadrao();
        $this->gerarTermoCampos = array_values(array_filter($extras, fn ($c) => ! empty($c['ativo'])));

        // Inicializa valores (vazios por padrão)
        $this->gerarTermoExtras = [];
        foreach ($this->gerarTermoCampos as $campo) {
            $this->gerarTermoExtras[$campo['id']] = '';
        }

        // Pré-preenche CPF e telefone do perfil do funcionário (se disponíveis)
        $atribuicao = \App\Models\EquipamentoAtribuicao::with('funcionario')->find($atribuicaoId);
        if ($atribuicao?->funcionario) {
            $funcionario = $atribuicao->funcionario;
            foreach ($this->gerarTermoCampos as $campo) {
                $id = $campo['id'] ?? '';
                if ($id === 'cpf' && ! empty($funcionario->cpf)) {
                    $this->gerarTermoExtras['cpf'] = $funcionario->cpf;
                }
                if ($id === 'telefone' && ! empty($funcionario->telefone)) {
                    $this->gerarTermoExtras['telefone'] = $funcionario->telefone;
                }
            }
        }

        // Se não há campos extras ativos, vai direto para o PDF
        if (empty($this->gerarTermoCampos)) {
            $this->gerarTermoDireto();
            return;
        }

        $this->modalGerarTermo = true;
    }

    public function gerarTermo(): void
    {
        $this->autorizar();

        // Salva na session por 10 minutos
        session()->put('termo_extras_' . $this->gerarTermoAtribuicaoId, [
            'campos'      => $this->gerarTermoExtras,
            'template_id' => $this->gerarTermoTemplateId,
            'expires'     => now()->addMinutes(10)->timestamp,
        ]);

        $this->modalGerarTermo = false;
        $this->redirect(route('equipamentos.termo', $this->gerarTermoAtribuicaoId), navigate: false);
    }

    private function gerarTermoDireto(): void
    {
        // Sem campos extras — passa apenas o template_id
        session()->put('termo_extras_' . $this->gerarTermoAtribuicaoId, [
            'campos'      => [],
            'template_id' => $this->gerarTermoTemplateId,
            'expires'     => now()->addMinutes(10)->timestamp,
        ]);
        $this->redirect(route('equipamentos.termo', $this->gerarTermoAtribuicaoId), navigate: false);
    }

    // ── Exportação CSV ────────────────────────────────────────────────────────

    public function exportarAtivosCSV()
    {
        $this->autorizar();
        $equipamentos = Equipamento::with('cadastradoPor')->orderBy('nome')->get();

        $rows   = [];
        $rows[] = ['Nome', 'Categoria', 'Marca', 'Modelo', 'Nº Série', 'Patrimônio', 'Status', 'Local', 'Valor', 'Data Aquisição', 'Garantia até', 'Cadastrado por'];

        foreach ($equipamentos as $eq) {
            $rows[] = [
                $eq->nome,
                $eq->categoria_label,
                $eq->marca ?? '',
                $eq->modelo ?? '',
                $eq->numero_serie ?? '',
                $eq->codigo_patrimonio ?? '',
                $eq->status_label,
                $eq->local ?? '',
                $eq->valor ? number_format((float)$eq->valor, 2, ',', '.') : '',
                $eq->data_aquisicao?->format('d/m/Y') ?? '',
                $eq->data_garantia?->format('d/m/Y') ?? '',
                $eq->cadastradoPor?->name ?? '',
            ];
        }

        return response()->streamDownload(function () use ($rows) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            foreach ($rows as $row) {
                fputcsv($f, $row, ';');
            }
            fclose($f);
        }, 'ativos_' . now()->format('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportarEntregasCSV()
    {
        $this->autorizar();
        $atrs = EquipamentoAtribuicao::with(['equipamento', 'funcionario', 'funcionario.department', 'responsavel'])
            ->orderByDesc('data_entrega')
            ->get();

        $rows   = [];
        $rows[] = ['Equipamento', 'Categoria', 'Funcionário', 'Departamento', 'Tipo', 'Data Entrega', 'Condição Entrega', 'Data Devolução', 'Condição Devolução', 'Registrado por'];

        $condicaoLabel = ['novo' => 'Novo', 'bom' => 'Bom', 'regular' => 'Regular', 'danificado' => 'Danificado'];

        foreach ($atrs as $atr) {
            $rows[] = [
                $atr->equipamento->nome,
                $atr->equipamento->categoria_label,
                $atr->funcionario->name,
                $atr->funcionario->department?->name ?? '',
                $atr->tipo === 'entrega' ? 'Entrega' : 'Devolução',
                $atr->data_entrega?->format('d/m/Y') ?? '',
                $condicaoLabel[$atr->condicao_entrega] ?? $atr->condicao_entrega,
                $atr->data_devolucao?->format('d/m/Y') ?? '',
                $condicaoLabel[$atr->condicao_devolucao ?? ''] ?? '',
                $atr->responsavel?->name ?? '',
            ];
        }

        return response()->streamDownload(function () use ($rows) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($rows as $row) {
                fputcsv($f, $row, ';');
            }
            fclose($f);
        }, 'entregas_' . now()->format('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ── View ──────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.pages.equipamentos.index')
            ->title('Controle de Equipamentos');
    }
}
