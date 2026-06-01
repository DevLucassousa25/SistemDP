<?php

namespace App\Livewire\Pages\Treinamentos;

use App\Livewire\SecureComponent;
use App\Models\Department;
use App\Models\Treinamento;
use Illuminate\Support\Facades\Storage;
use App\Models\TreinamentoAula;
use App\Models\TreinamentoDuvida;
use App\Models\TreinamentoDuvidaResposta;
use App\Models\TreinamentoInscricao;
use App\Models\TreinamentoObrigatorio;
use App\Models\TreinamentoQuestao;
use App\Models\TreinamentoQuestaoOpcao;
use App\Models\TreinamentoTeste;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Gestao extends SecureComponent
{
    use WithPagination, WithFileUploads;

    // ── Aba principal ──────────────────────────────────────────────────────
    #[Url(except: 'cursos')]
    public string $aba = 'cursos'; // cursos | duvidas

    // ── Filtro / busca ─────────────────────────────────────────────────────
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: 'todos')]
    public string $statusFiltro = 'todos';

    // ── Curso selecionado (detail) ─────────────────────────────────────────
    #[Locked]
    public ?int $cursoSelecionadoId = null;

    public string $abaDetalhe = 'info'; // info | aulas | teste | alunos

    // ══════════════════════════════════════════════════════════════════════
    // MODAL: CRIAR / EDITAR CURSO
    // ══════════════════════════════════════════════════════════════════════
    public bool $modalCurso = false;
    public string $modalCursoModo = 'criar';

    #[Locked]
    public ?int $cursoEditandoId = null;

    #[Validate('required|string|min:3|max:200')]
    public string $cursoTitulo = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $cursoDescricao = '';

    #[Validate('required|in:interno,externo')]
    public string $cursoTipo = 'interno';

    #[Validate('nullable|string|max:100')]
    public ?string $cursoCategoria = '';

    #[Validate('nullable|string|max:100')]
    public ?string $cursoInstrutor = '';

    #[Validate('required|integer|min:0')]
    public int $cursoCargaHoraria = 60;

    #[Validate('required|in:basico,intermediario,avancado')]
    public string $cursoNivel = 'basico';

    #[Validate('required|in:rascunho,ativo,arquivado')]
    public string $cursoStatus = 'rascunho';

    public bool $cursoCertificadoHabilitado = true;

    #[Validate('required|integer|min:1|max:100')]
    public int $cursoNotaMinima = 70;

    public bool $cursoInscricaoAberta = true;

    #[Validate('nullable|date')]
    public ?string $cursoDataInicio = '';

    #[Validate('nullable|date')]
    public ?string $cursoDataFim = '';

    #[Validate('nullable|image|max:2048')]
    public $cursoCapa = null;

    public ?string $cursoCapaAtual = null; // path da capa já salva (ao editar)

    // ══════════════════════════════════════════════════════════════════════
    // MODAL: CRIAR / EDITAR AULA
    // ══════════════════════════════════════════════════════════════════════
    public bool $modalAula = false;
    public string $modalAulaModo = 'criar';

    #[Locked]
    public ?int $aulaEditandoId = null;

    #[Validate('required|string|min:3|max:200')]
    public string $aulaTitulo = '';

    #[Validate('nullable|string|max:1000')]
    public ?string $aulaDescricao = '';

    #[Validate('nullable|url|max:500')]
    public ?string $aulaVideoUrl = '';

    #[Validate('required|integer|min:1')]
    public int $aulaDuracao = 10;

    #[Validate('required|integer|min:0')]
    public int $aulaOrdem = 0;

    public bool $aulaObrigatoria = true;

    public bool $aulaExigirVideo = false;

    // ══════════════════════════════════════════════════════════════════════
    // MODAL: TESTE
    // ══════════════════════════════════════════════════════════════════════
    public bool $modalTeste = false;

    #[Validate('required|string|min:3|max:200')]
    public string $testeTitulo = '';

    #[Validate('nullable|string|max:1000')]
    public ?string $testeDescricao = '';

    #[Validate('required|integer|min:1|max:100')]
    public int $testeNotaMinima = 70;

    #[Validate('required|integer|min:1|max:10')]
    public int $testeTentativasMaximas = 3;

    public bool $testeEmbaralhar = true;

    // ── Questões inline ────────────────────────────────────────────────────
    public array $questoes = [];
    // [ ['enunciado'=>'', 'tipo'=>'multipla_escolha', 'pontos'=>1, 'opcoes'=>[['texto'=>'','correta'=>false],...]] ]

    // ══════════════════════════════════════════════════════════════════════
    // DÚVIDAS
    // ══════════════════════════════════════════════════════════════════════
    #[Url(except: 'abertas')]
    public string $duvidaStatusFiltro = 'abertas';

    #[Locked]
    public ?int $duvidaAbiertaId = null;

    #[Validate('required|string|min:5|max:2000')]
    public string $respostaDuvida = '';

    // ══════════════════════════════════════════════════════════════════════
    // MODAL: INSCRIÇÃO OBRIGATÓRIA
    // ══════════════════════════════════════════════════════════════════════
    public bool   $modalObrigatorio     = false;
    public string $obrigatorioTipo      = 'usuario'; // usuario | departamento
    public ?int   $obrigatorioUserId    = null;
    public ?int   $obrigatorioDeptId    = null;
    public ?string $obrigatorioDeadline = null;

    // ══════════════════════════════════════════════════════════════════════
    // CONFIRMAÇÃO EXCLUSÃO
    // ══════════════════════════════════════════════════════════════════════
    public bool $confirmDelete = false;
    #[Locked]
    public ?int $deleteTargetId = null;
    public string $deleteTargetType = '';

    // ─────────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->requireAuth();
        $this->requireRhOrAdmin();
    }

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingStatusFiltro(): void { $this->resetPage(); }

    // ══════════════════════════════════════════════════════════════════════
    // LISTA DE CURSOS
    // ══════════════════════════════════════════════════════════════════════

    #[Computed]
    public function cursos()
    {
        return Treinamento::query()
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('titulo', 'like', "%{$this->search}%")
                       ->orWhere('categoria', 'like', "%{$this->search}%")
                ))
            ->when($this->statusFiltro !== 'todos', fn($q) => $q->where('status', $this->statusFiltro))
            ->withCount('inscricoes')
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total'      => Treinamento::count(),
            'ativos'     => Treinamento::where('status', 'ativo')->count(),
            'rascunho'   => Treinamento::where('status', 'rascunho')->count(),
            'inscritos'  => TreinamentoInscricao::count(),
            'concluidos' => TreinamentoInscricao::where('status', 'concluido')->count(),
        ];
    }

    // ── Selecionar curso para detalhe ──────────────────────────────────────
    public function selecionarCurso(int $id): void
    {
        $this->cursoSelecionadoId = $id;
        $this->abaDetalhe = 'info';
    }

    public function voltarLista(): void
    {
        $this->cursoSelecionadoId = null;
        unset($this->cursoSelecionado);
    }

    #[Computed]
    public function cursoSelecionado(): ?Treinamento
    {
        if (!$this->cursoSelecionadoId) return null;
        return Treinamento::with(['aulas', 'teste.questoes.opcoes', 'inscricoes.usuario'])->find($this->cursoSelecionadoId);
    }

    // ══════════════════════════════════════════════════════════════════════
    // CRUD CURSO
    // ══════════════════════════════════════════════════════════════════════

    public function abrirModalCurso(string $modo = 'criar', ?int $id = null): void
    {
        $this->resetCursoForm();
        $this->modalCursoModo = $modo;
        $this->cursoEditandoId = $id;

        if ($modo === 'editar' && $id) {
            $c = Treinamento::findOrFail($id);
            $this->cursoTitulo              = $c->titulo;
            $this->cursoDescricao           = $c->descricao;
            $this->cursoTipo                = $c->tipo;
            $this->cursoCategoria           = $c->categoria;
            $this->cursoInstrutor           = $c->instrutor;
            $this->cursoCargaHoraria        = $c->carga_horaria;
            $this->cursoNivel               = $c->nivel;
            $this->cursoStatus              = $c->status;
            $this->cursoCertificadoHabilitado = $c->certificado_habilitado;
            $this->cursoNotaMinima          = $c->nota_minima_aprovacao;
            $this->cursoInscricaoAberta     = $c->inscricao_aberta;
            $this->cursoDataInicio          = $c->data_inicio?->format('Y-m-d');
            $this->cursoDataFim             = $c->data_fim?->format('Y-m-d');
            $this->cursoCapaAtual           = $c->capa;
        }

        $this->modalCurso = true;
    }

    public function salvarCurso(): void
    {
        $this->requireRhOrAdmin();
        $this->validate([
            'cursoTitulo'       => 'required|string|min:3|max:200',
            'cursoDescricao'    => 'nullable|string|max:2000',
            'cursoTipo'         => 'required|in:interno,externo',
            'cursoCargaHoraria' => 'required|integer|min:0',
            'cursoNivel'        => 'required|in:basico,intermediario,avancado',
            'cursoStatus'       => 'required|in:rascunho,ativo,arquivado',
            'cursoNotaMinima'   => 'required|integer|min:1|max:100',
        ]);

        $dados = [
            'titulo'                  => $this->sanitize($this->cursoTitulo),
            'descricao'               => $this->sanitize($this->cursoDescricao ?? ''),
            'tipo'                    => $this->cursoTipo,
            'categoria'               => $this->sanitize($this->cursoCategoria ?? ''),
            'instrutor'               => $this->sanitize($this->cursoInstrutor ?? ''),
            'carga_horaria'           => $this->cursoCargaHoraria,
            'nivel'                   => $this->cursoNivel,
            'status'                  => $this->cursoStatus,
            'certificado_habilitado'  => $this->cursoCertificadoHabilitado,
            'nota_minima_aprovacao'   => $this->cursoNotaMinima,
            'inscricao_aberta'        => $this->cursoInscricaoAberta,
            'data_inicio'             => $this->cursoDataInicio ?: null,
            'data_fim'                => $this->cursoDataFim ?: null,
            'criado_por'              => Auth::id(),
        ];

        // Processar upload da capa
        if ($this->cursoCapa) {
            // Apagar capa anterior se existir
            if ($this->cursoCapaAtual && Storage::disk('public')->exists($this->cursoCapaAtual)) {
                Storage::disk('public')->delete($this->cursoCapaAtual);
            }
            $dados['capa'] = $this->cursoCapa->store('treinamentos/capas', 'public');
        }

        if ($this->modalCursoModo === 'editar' && $this->cursoEditandoId) {
            unset($dados['criado_por']);
            Treinamento::findOrFail($this->cursoEditandoId)->update($dados);
            $this->alertSuccess('Curso atualizado!');
        } else {
            $novo = Treinamento::create($dados);
            $this->cursoSelecionadoId = $novo->id;
        }

        $this->modalCurso = false;
        $this->resetCursoForm();
        unset($this->cursos, $this->stats, $this->cursoSelecionado);
        if ($this->cursoSelecionadoId) $this->abaDetalhe = 'aulas';
    }

    private function resetCursoForm(): void
    {
        $this->cursoTitulo = $this->cursoDescricao = $this->cursoCategoria = $this->cursoInstrutor = $this->cursoDataInicio = $this->cursoDataFim = '';
        $this->cursoTipo   = 'interno';
        $this->cursoNivel  = 'basico';
        $this->cursoStatus = 'rascunho';
        $this->cursoCargaHoraria = 60;
        $this->cursoNotaMinima   = 70;
        $this->cursoCertificadoHabilitado = $this->cursoInscricaoAberta = true;
        $this->cursoEditandoId = null;
        $this->cursoCapa = null;
        $this->cursoCapaAtual = null;
    }

    public function removerCapa(): void
    {
        $this->requireRhOrAdmin();
        if ($this->cursoEditandoId && $this->cursoCapaAtual) {
            if (Storage::disk('public')->exists($this->cursoCapaAtual)) {
                Storage::disk('public')->delete($this->cursoCapaAtual);
            }
            Treinamento::findOrFail($this->cursoEditandoId)->update(['capa' => null]);
            $this->cursoCapaAtual = null;
            unset($this->cursoSelecionado, $this->cursos);
        }
        $this->cursoCapa = null;
    }

    // ── Excluir curso ──────────────────────────────────────────────────────
    public function confirmarExclusao(int $id, string $tipo): void
    {
        $this->deleteTargetId   = $id;
        $this->deleteTargetType = $tipo;
        $this->confirmDelete    = true;
    }

    public function executarExclusao(): void
    {
        $this->requireRhOrAdmin();
        match($this->deleteTargetType) {
            'curso' => Treinamento::findOrFail($this->deleteTargetId)->delete(),
            'aula'  => TreinamentoAula::findOrFail($this->deleteTargetId)->delete(),
            default => null,
        };
        $this->confirmDelete    = false;
        $this->deleteTargetId   = null;
        $this->deleteTargetType = '';
        if ($this->deleteTargetType === 'curso') $this->voltarLista();
        unset($this->cursos, $this->cursoSelecionado);
        $this->alertSuccess('Excluído com sucesso.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // CRUD AULA
    // ══════════════════════════════════════════════════════════════════════

    public function abrirModalAula(string $modo = 'criar', ?int $id = null): void
    {
        $this->resetAulaForm();
        $this->modalAulaModo  = $modo;
        $this->aulaEditandoId = $id;

        if ($modo === 'editar' && $id) {
            $a = TreinamentoAula::findOrFail($id);
            $this->aulaTitulo     = $a->titulo;
            $this->aulaDescricao  = $a->descricao;
            $this->aulaVideoUrl   = $a->video_url;
            $this->aulaDuracao    = $a->duracao;
            $this->aulaOrdem      = $a->ordem;
            $this->aulaObrigatoria  = $a->obrigatoria;
            $this->aulaExigirVideo  = $a->exigir_video;
        } else {
            $this->aulaOrdem = $this->cursoSelecionado?->aulas()->max('ordem') + 1 ?? 0;
        }

        $this->modalAula = true;
    }

    public function salvarAula(): void
    {
        $this->requireRhOrAdmin();
        $this->validate([
            'aulaTitulo'   => 'required|string|min:3|max:200',
            'aulaVideoUrl' => 'nullable|url|max:500',
            'aulaDuracao'  => 'required|integer|min:1',
            'aulaOrdem'    => 'required|integer|min:0',
        ]);

        $dados = [
            'treinamento_id' => $this->cursoSelecionadoId,
            'titulo'         => $this->sanitize($this->aulaTitulo),
            'descricao'      => $this->sanitize($this->aulaDescricao ?? ''),
            'video_url'      => $this->aulaVideoUrl,
            'duracao'        => $this->aulaDuracao,
            'ordem'          => $this->aulaOrdem,
            'obrigatoria'    => $this->aulaObrigatoria,
            'exigir_video'   => $this->aulaExigirVideo,
        ];

        if ($this->modalAulaModo === 'editar' && $this->aulaEditandoId) {
            TreinamentoAula::findOrFail($this->aulaEditandoId)->update($dados);
            $this->alertSuccess('Aula atualizada!');
        } else {
            TreinamentoAula::create($dados);
            $this->alertSuccess('Aula criada!');
        }

        $this->modalAula = false;
        $this->resetAulaForm();
        unset($this->cursoSelecionado);
    }

    private function resetAulaForm(): void
    {
        $this->aulaTitulo = $this->aulaDescricao = $this->aulaVideoUrl = '';
        $this->aulaDuracao  = 10;
        $this->aulaOrdem    = 0;
        $this->aulaObrigatoria = true;
        $this->aulaExigirVideo = false;
        $this->aulaEditandoId = null;
    }

    // ══════════════════════════════════════════════════════════════════════
    // CRUD TESTE + QUESTÕES
    // ══════════════════════════════════════════════════════════════════════

    public function abrirModalTeste(): void
    {
        $teste = $this->cursoSelecionado?->teste;

        $this->testeTitulo           = $teste?->titulo           ?? 'Avaliação Final';
        $this->testeDescricao        = $teste?->descricao        ?? '';
        $this->testeNotaMinima       = $teste?->nota_minima      ?? 70;
        $this->testeTentativasMaximas = $teste?->tentativas_maximas ?? 3;
        $this->testeEmbaralhar       = $teste?->embaralhar_questoes ?? true;

        // Carrega questões existentes no array
        if ($teste) {
            $this->questoes = $teste->questoes->map(fn($q) => [
                'id'       => $q->id,
                'enunciado'=> $q->enunciado,
                'tipo'     => $q->tipo,
                'pontos'   => $q->pontos,
                'opcoes'   => $q->opcoes->map(fn($o) => [
                    'id'     => $o->id,
                    'texto'  => $o->texto,
                    'correta'=> $o->correta,
                ])->toArray(),
            ])->toArray();
        } else {
            $this->questoes = [];
            $this->adicionarQuestao();
        }

        $this->modalTeste = true;
    }

    public function adicionarQuestao(): void
    {
        $this->questoes[] = [
            'id'        => null,
            'enunciado' => '',
            'tipo'      => 'multipla_escolha',
            'pontos'    => 1,
            'opcoes'    => [
                ['id' => null, 'texto' => '', 'correta' => false],
                ['id' => null, 'texto' => '', 'correta' => false],
                ['id' => null, 'texto' => '', 'correta' => false],
                ['id' => null, 'texto' => '', 'correta' => false],
            ],
        ];
    }

    public function removerQuestao(int $idx): void
    {
        array_splice($this->questoes, $idx, 1);
    }

    public function adicionarOpcao(int $qIdx): void
    {
        $this->questoes[$qIdx]['opcoes'][] = ['id' => null, 'texto' => '', 'correta' => false];
    }

    public function removerOpcao(int $qIdx, int $oIdx): void
    {
        array_splice($this->questoes[$qIdx]['opcoes'], $oIdx, 1);
    }

    public function marcarCorreta(int $qIdx, int $oIdx): void
    {
        foreach ($this->questoes[$qIdx]['opcoes'] as $i => $op) {
            $this->questoes[$qIdx]['opcoes'][$i]['correta'] = ($i === $oIdx);
        }
    }

    public function salvarTeste(): void
    {
        $this->requireRhOrAdmin();
        $this->validate([
            'testeTitulo'            => 'required|string|min:3|max:200',
            'testeNotaMinima'        => 'required|integer|min:1|max:100',
            'testeTentativasMaximas' => 'required|integer|min:1|max:10',
        ]);

        // Salvar teste
        $teste = TreinamentoTeste::updateOrCreate(
            ['treinamento_id' => $this->cursoSelecionadoId],
            [
                'titulo'               => $this->sanitize($this->testeTitulo),
                'descricao'            => $this->sanitize($this->testeDescricao ?? ''),
                'nota_minima'          => $this->testeNotaMinima,
                'tentativas_maximas'   => $this->testeTentativasMaximas,
                'embaralhar_questoes'  => $this->testeEmbaralhar,
            ]
        );

        // Salvar questões
        $idsExistentes = [];
        foreach ($this->questoes as $ordem => $qData) {
            if (empty(trim($qData['enunciado']))) continue;

            $questao = TreinamentoQuestao::updateOrCreate(
                ['id' => $qData['id'] ?? 0],
                [
                    'teste_id'  => $teste->id,
                    'enunciado' => $this->sanitize($qData['enunciado']),
                    'tipo'      => $qData['tipo'],
                    'ordem'     => $ordem,
                    'pontos'    => max(1, (int) $qData['pontos']),
                ]
            );
            $idsExistentes[] = $questao->id;

            // Salvar opções
            $idsOpcoes = [];
            foreach ($qData['opcoes'] as $oOrdem => $oData) {
                if (empty(trim($oData['texto']))) continue;
                $opcao = TreinamentoQuestaoOpcao::updateOrCreate(
                    ['id' => $oData['id'] ?? 0],
                    [
                        'questao_id' => $questao->id,
                        'texto'      => $this->sanitize($oData['texto']),
                        'correta'    => (bool) $oData['correta'],
                        'ordem'      => $oOrdem,
                    ]
                );
                $idsOpcoes[] = $opcao->id;
            }
            // Remover opções excluídas
            $questao->opcoes()->whereNotIn('id', $idsOpcoes)->delete();
        }
        // Remover questões excluídas
        $teste->questoes()->whereNotIn('id', $idsExistentes)->delete();

        $this->modalTeste = false;
        $this->questoes   = [];
        unset($this->cursoSelecionado);
        $this->alertSuccess('Teste salvo com sucesso!');
    }

    // ══════════════════════════════════════════════════════════════════════
    // DÚVIDAS DOS ALUNOS
    // ══════════════════════════════════════════════════════════════════════

    #[Computed]
    public function duvidas()
    {
        return TreinamentoDuvida::with(['usuario', 'treinamento', 'aula', 'respostas.usuario'])
            ->when($this->duvidaStatusFiltro === 'abertas', fn($q) => $q->where('status', 'aberta'))
            ->when($this->duvidaStatusFiltro === 'respondidas', fn($q) => $q->where('status', 'respondida'))
            ->latest()
            ->paginate(20);
    }

    public function abrirDuvida(int $id): void
    {
        $this->duvidaAbiertaId = $id;
        $this->respostaDuvida  = '';
    }

    public function fecharDuvida(): void
    {
        $this->duvidaAbiertaId = null;
        $this->respostaDuvida  = '';
    }

    #[Computed]
    public function duvidaAberta(): ?TreinamentoDuvida
    {
        if (!$this->duvidaAbiertaId) return null;
        return TreinamentoDuvida::with(['usuario', 'treinamento', 'aula', 'respostas.usuario'])
            ->find($this->duvidaAbiertaId);
    }

    public function responderDuvida(): void
    {
        $this->requireRhOrAdmin();
        $this->validate(['respostaDuvida' => 'required|string|min:5|max:2000']);

        $duvida = TreinamentoDuvida::findOrFail($this->duvidaAbiertaId);

        TreinamentoDuvidaResposta::create([
            'duvida_id' => $duvida->id,
            'user_id'   => Auth::id(),
            'resposta'  => $this->sanitize($this->respostaDuvida),
        ]);

        $duvida->update(['status' => 'respondida']);

        $this->respostaDuvida = '';
        unset($this->duvidas, $this->duvidaAberta);
        $this->alertSuccess('Resposta enviada!');
    }

    // ══════════════════════════════════════════════════════════════════════
    // INSCRIÇÃO OBRIGATÓRIA
    // ══════════════════════════════════════════════════════════════════════

    #[Computed]
    public function usuarios()
    {
        return User::orderBy('name')->get(['id', 'name', 'email']);
    }

    #[Computed]
    public function departamentos()
    {
        return Department::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function obrigatorios()
    {
        if (!$this->cursoSelecionadoId) return collect();
        return TreinamentoObrigatorio::with(['usuario', 'departamento'])
            ->where('treinamento_id', $this->cursoSelecionadoId)
            ->get();
    }

    public function abrirModalObrigatorio(): void
    {
        $this->obrigatorioTipo     = 'usuario';
        $this->obrigatorioUserId   = null;
        $this->obrigatorioDeptId   = null;
        $this->obrigatorioDeadline = null;
        $this->modalObrigatorio    = true;
    }

    public function salvarObrigatorio(): void
    {
        $this->requireRhOrAdmin();

        if ($this->obrigatorioTipo === 'usuario' && !$this->obrigatorioUserId) {
            $this->alertError('Selecione um colaborador.');
            return;
        }
        if ($this->obrigatorioTipo === 'departamento' && !$this->obrigatorioDeptId) {
            $this->alertError('Selecione um departamento.');
            return;
        }

        $data = [
            'treinamento_id' => $this->cursoSelecionadoId,
            'criado_por'     => Auth::id(),
            'prazo'          => $this->obrigatorioDeadline ?: null,
        ];

        if ($this->obrigatorioTipo === 'usuario') {
            $data['tipo_alvo']     = 'usuario';
            $data['user_id']       = $this->obrigatorioUserId;
            $data['department_id'] = null;

            // Evitar duplicata
            $existe = TreinamentoObrigatorio::where('treinamento_id', $this->cursoSelecionadoId)
                ->where('user_id', $this->obrigatorioUserId)->exists();
            if ($existe) { $this->alertError('Este colaborador já está na lista de obrigatórios.'); return; }

            TreinamentoObrigatorio::create($data);

            // Auto-inscrever o usuário se ainda não estiver inscrito
            $inscricao = TreinamentoInscricao::firstOrCreate(
                ['treinamento_id' => $this->cursoSelecionadoId, 'user_id' => $this->obrigatorioUserId],
                ['status' => 'inscrito', 'progresso' => 0]
            );
            if ($this->obrigatorioDeadline) {
                $inscricao->update(['prazo_conclusao' => $this->obrigatorioDeadline]);
            }

        } else {
            $data['tipo_alvo']     = 'departamento';
            $data['user_id']       = null;
            $data['department_id'] = $this->obrigatorioDeptId;

            $existe = TreinamentoObrigatorio::where('treinamento_id', $this->cursoSelecionadoId)
                ->where('department_id', $this->obrigatorioDeptId)->exists();
            if ($existe) { $this->alertError('Este departamento já está na lista.'); return; }

            TreinamentoObrigatorio::create($data);

            // Auto-inscrever todos os usuários do departamento
            $users = User::where('department_id', $this->obrigatorioDeptId)->get();
            foreach ($users as $u) {
                $inscricao = TreinamentoInscricao::firstOrCreate(
                    ['treinamento_id' => $this->cursoSelecionadoId, 'user_id' => $u->id],
                    ['status' => 'inscrito', 'progresso' => 0]
                );
                if ($this->obrigatorioDeadline) {
                    $inscricao->update(['prazo_conclusao' => $this->obrigatorioDeadline]);
                }
            }
        }

        $this->modalObrigatorio = false;
        unset($this->obrigatorios, $this->cursoSelecionado);
        $this->alertSuccess('Inscrição obrigatória configurada!');
    }

    public function removerObrigatorio(int $id): void
    {
        $this->requireRhOrAdmin();
        TreinamentoObrigatorio::findOrFail($id)->delete();
        unset($this->obrigatorios);
        $this->alertSuccess('Removido da lista de obrigatórios.');
    }

    // ─────────────────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.pages.treinamentos.gestao')
            ->layout('components.layouts.app', ['title' => 'Gestão de Treinamentos']);
    }
}
