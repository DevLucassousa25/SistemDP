<?php

namespace App\Livewire\Pages\Rh;

use App\Livewire\SecureComponent;
use App\Models\DpiPlan;
use App\Models\DpiGoal;
use App\Models\RhCurriculo;
use App\Models\RhCurriculoTag;
use App\Models\RhVaga;
use App\Models\RhVagaEtapa;
use App\Models\RhCandidatura;
use App\Models\RhCandidaturaHistorico;
use App\Models\RhCandidaturaComentario;
use App\Models\RhTeste;
use App\Models\RhQuestao;
use App\Models\RhOpcao;
use App\Models\RhCandidatoTeste;
use App\Models\RhCurriculo as Curriculo;
use App\Models\AccessProfile;
use App\Models\Department;
use App\Models\RhDesligamento;
use App\Models\RhDesligamentoChecklist;
use App\Models\RhEntrevistaDesligamento;
use App\Models\RhOnboarding;
use App\Models\RhOnboardingTarefa;
use App\Models\RhVerbaRescisoria;
use App\Models\User;
use App\Models\MoodCheckin;
use App\Models\Feedback;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\RhSolicitacao;
use App\Models\OkrKeyResult;
use App\Models\ManagerEvaluation;
use App\Models\SurveyResponse;
use App\Notifications\CandidatoContratadoNotification;
use App\Livewire\Concerns\EnviaNotificacoes;
use App\Models\ConfiguracaoEmpresa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Portal extends SecureComponent
{
    use WithFileUploads, WithPagination, EnviaNotificacoes;

    // ── Navegação interna ─────────────────────────────────────────────
    #[Url]
    public string $aba = 'dashboard';   // dashboard | curriculos | vagas | pipeline | testes

    public function mount(): void
    {
        $this->requireAuth();
        $this->requireRhOrAdmin();
        $this->resetEtapasPadrao();
    }

    public function setAba(string $aba): void
    {
        $this->aba = $aba;
        $this->resetPage();
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ABA: DASHBOARD
    // ═══════════════════════════════════════════════════════════════════

    #[Computed]
    public function stats(): array
    {
        $totalTestes  = RhCandidatoTeste::whereIn('status', ['concluido', 'expirado'])->count();
        $tempoMedio   = RhCandidatura::where('status', 'aprovado')
            ->whereNotNull('aprovado_at')
            ->selectRaw('AVG(EXTRACT(DAY FROM (aprovado_at - created_at))) as media')
            ->value('media');

        return [
            'total_candidatos'  => RhCurriculo::count(),
            'vagas_abertas'     => RhVaga::where('status', 'publicada')->count(),
            'processos_ativos'  => RhCandidatura::where('status', 'ativo')->distinct('vaga_id')->count('vaga_id'),
            'testes_realizados' => RhCandidatoTeste::where('status', 'concluido')->count(),
            'taxa_aprovacao'    => $totalTestes > 0
                ? round(RhCandidatoTeste::where('status','concluido')->where('aprovado', true)->count() / $totalTestes * 100)
                : 0,
            'tempo_medio_dias'  => round($tempoMedio ?? 0),
        ];
    }

    #[Computed]
    public function candidatosPorVaga(): array
    {
        return RhVaga::where('status', 'publicada')
            ->withCount('candidaturas')
            ->orderByDesc('candidaturas_count')
            ->limit(8)->get()
            ->map(fn($v) => ['label' => $v->titulo, 'value' => $v->candidaturas_count])
            ->toArray();
    }

    #[Computed]
    public function contratacoesPorMes(): array
    {
        return RhCandidatura::where('status', 'aprovado')
            ->whereNotNull('aprovado_at')
            ->where('aprovado_at', '>=', now()->subMonths(6))
            ->selectRaw("TO_CHAR(aprovado_at, 'Mon/YY') as mes, COUNT(*) as total, DATE_TRUNC('month', aprovado_at) as dt")
            ->groupByRaw("TO_CHAR(aprovado_at, 'Mon/YY'), DATE_TRUNC('month', aprovado_at)")
            ->orderBy('dt')
            ->get()
            ->map(fn($r) => ['label' => $r->mes, 'value' => $r->total])
            ->toArray();
    }

    #[Computed]
    public function rankingRecrutadores(): array
    {
        return DB::table('rh_candidaturas as c')
            ->join('rh_vagas as v', 'v.id', '=', 'c.vaga_id')
            ->join('users as u', 'u.id', '=', 'v.created_by')
            ->where('c.status', 'aprovado')
            ->selectRaw('u.name, COUNT(*) as total')
            ->groupBy('u.name', 'u.id')
            ->orderByDesc('total')
            ->limit(5)->get()->toArray();
    }

    #[Computed]
    public function vagasRecentes()
    {
        return RhVaga::withCount('candidaturas')->latest()->limit(5)->get();
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ABA: CURRÍCULOS
    // ═══════════════════════════════════════════════════════════════════

    public string $search            = '';
    public string $ocrSearch         = '';   // busca no texto extraído do PDF
    public string $filterStatus      = '';
    public string $filterEstado      = '';
    public string $filterEscolaridade= '';
    public string $filterArea        = '';
    public string $filterSalMin      = '';
    public string $filterSalMax      = '';
    public string $sortBy            = 'recente';

    // Upload
    public bool   $uploadModal       = false;
    public $arquivo                  = null;

    // Drawer currículo
    public bool   $currDrawer        = false;
    #[Locked]
    public ?int   $currDrawerId      = null;

    // Editar currículo
    public bool   $currEditModal     = false;
    #[Locked]
    public ?int   $currEditId        = null;
    public string $editNome          = '';
    public string $editEmail         = '';
    public string $editTelefone      = '';
    public string $editCidade        = '';
    public string $editEstado        = '';
    public string $editArea          = '';
    public string $editEscolaridade  = '';
    public string $editResumo        = '';
    public string $editPretensao     = '';
    public string $editNotas         = '';
    public string $editStatus        = 'ativo';

    // Excluir
    public bool   $currDeleteModal   = false;
    #[Locked]
    public ?int   $currDeleteId      = null;

    // Tag
    public string $newTag            = '';

    public function updatedSearch(): void              { $this->resetPage(); }
    public function updatedOcrSearch(): void           { $this->resetPage(); }
    public function updatedFilterStatus(): void        { $this->resetPage(); }
    public function updatedFilterEstado(): void        { $this->resetPage(); }
    public function updatedFilterEscolaridade(): void  { $this->resetPage(); }
    public function updatedSortBy(): void              { $this->resetPage(); }

    #[Computed]
    public function curriculos()
    {
        $q = RhCurriculo::with(['tags', 'experiencias', 'formacoes', 'habilidades'])
            ->when($this->search, fn($q) =>
                $q->where(fn($s) =>
                    $s->where('nome', 'ilike', "%{$this->search}%")
                      ->orWhere('email', 'ilike', "%{$this->search}%")
                      ->orWhere('area_interesse', 'ilike', "%{$this->search}%")
                      ->orWhere('cidade', 'ilike', "%{$this->search}%")
                )
            )
            ->when($this->ocrSearch, function ($q) {
                // Busca em múltiplas palavras no texto extraído do currículo
                foreach (array_filter(explode(' ', trim($this->ocrSearch))) as $palavra) {
                    $q->where('texto_ocr', 'ilike', "%{$palavra}%");
                }
            })
            ->when($this->filterStatus,      fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterEstado,      fn($q) => $q->where('estado', 'ilike', "%{$this->filterEstado}%"))
            ->when($this->filterEscolaridade,fn($q) => $q->where('escolaridade', $this->filterEscolaridade))
            ->when($this->filterArea,        fn($q) => $q->where('area_interesse', 'ilike', "%{$this->filterArea}%"))
            ->when($this->filterSalMin,      fn($q) => $q->where('pretensao_salarial', '>=', $this->filterSalMin))
            ->when($this->filterSalMax,      fn($q) => $q->where('pretensao_salarial', '<=', $this->filterSalMax));

        return match ($this->sortBy) {
            'nome'      => $q->orderBy('nome')->paginate(18),
            'pretensao' => $q->orderByDesc('pretensao_salarial')->paginate(18),
            default     => $q->latest()->paginate(18),
        };
    }

    #[Computed]
    public function curriculoDrawer(): ?RhCurriculo
    {
        if (!$this->currDrawerId) return null;
        return RhCurriculo::with([
            'experiencias','formacoes','habilidades','tags',
            'candidaturas.vaga','candidaturas.etapa',
            'testes.teste',
        ])->find($this->currDrawerId);
    }

    public function uploadCurriculo(): void
    {
        $this->requireRhOrAdmin();
        $this->validate(['arquivo' => 'required|file|mimes:pdf,doc,docx|max:5120']);
        if (!$this->rateLimit('upload-curriculo', 20, 60)) return;

        $original = $this->arquivo->getClientOriginalName();
        $mime     = $this->arquivo->getMimeType();
        $path     = $this->arquivo->store('rh/curriculos', 'public');
        $parsed   = $this->parseCurriculo($path, $mime);

        $curriculo = RhCurriculo::create(array_merge($parsed, [
            'arquivo_path'     => $path,
            'arquivo_original' => $original,
            'arquivo_mime'     => $mime,
            'status'           => 'ativo',
        ]));

        \App\Jobs\ExtrairTextoOcr::dispatch($curriculo->id);

        $this->arquivo     = null;
        $this->uploadModal = false;
        unset($this->curriculos);
        $this->alertSuccess('Currículo enviado!', 'Dados extraídos automaticamente.');
    }

    private function parseCurriculo(string $path, string $mime): array
    {
        $text = '';
        $full = Storage::disk('public')->path($path);
        if (str_contains($mime, 'pdf')) {
            $text = (string) shell_exec('pdftotext ' . escapeshellarg($full) . ' - 2>/dev/null');
        }
        $data = ['nome' => pathinfo($path, PATHINFO_FILENAME)];
        if (preg_match('/[\w.+-]+@[\w-]+\.\w{2,}/i', $text, $m))                           $data['email']             = $m[0];
        if (preg_match('/(?:\(?\d{2}\)?\s?)?(?:9\s?)?\d{4}[-\s]?\d{4}/', $text, $m))       $data['telefone']          = preg_replace('/\D/', '', $m[0]);
        if (preg_match('/\d{3}\.?\d{3}\.?\d{3}-?\d{2}/', $text, $m))                        $data['cpf']               = $m[0];
        if (preg_match('/pretens[ãa]o[:\s]+R?\$?\s*([\d.,]+)/i', $text, $m))               $data['pretensao_salarial']= (float) str_replace(['.', ','], ['', '.'], $m[1]);
        return $data;
    }

    public function openCurrDrawer(int $id): void
    {
        $this->currDrawerId = $id;
        $this->currDrawer   = true;
        unset($this->curriculoDrawer);
    }

    public function openCurrEdit(int $id): void
    {
        $c = RhCurriculo::findOrFail($id);
        $this->currEditId       = $id;
        $this->editNome         = $c->nome;
        $this->editEmail        = $c->email ?? '';
        $this->editTelefone     = $c->telefone ?? '';
        $this->editCidade       = $c->cidade ?? '';
        $this->editEstado       = $c->estado ?? '';
        $this->editArea         = $c->area_interesse ?? '';
        $this->editEscolaridade = $c->escolaridade ?? '';
        $this->editResumo       = $c->resumo_profissional ?? '';
        $this->editPretensao    = $c->pretensao_salarial ?? '';
        $this->editNotas        = $c->notas_internas ?? '';
        $this->editStatus       = $c->status;
        $this->currEditModal    = true;
    }

    public function saveCurrEdit(): void
    {
        $this->requireRhOrAdmin();
        $this->validate(['editNome' => 'required|string|max:200', 'editEmail' => 'nullable|email|max:200']);
        RhCurriculo::findOrFail($this->currEditId)->update([
            'nome'                => $this->sanitize($this->editNome),
            'email'               => $this->sanitize($this->editEmail),
            'telefone'            => $this->sanitize($this->editTelefone),
            'cidade'              => $this->sanitize($this->editCidade),
            'estado'              => $this->sanitize($this->editEstado),
            'area_interesse'      => $this->sanitize($this->editArea),
            'escolaridade'        => $this->editEscolaridade ?: null,
            'resumo_profissional' => $this->sanitize($this->editResumo),
            'pretensao_salarial'  => $this->editPretensao ? (float) $this->editPretensao : null,
            'notas_internas'      => $this->sanitize($this->editNotas),
            'status'              => $this->editStatus,
        ]);
        $this->currEditModal = false;
        unset($this->curriculos, $this->curriculoDrawer);
        $this->alertSuccess('Currículo atualizado!');
    }

    public function addTag(int $currId): void
    {
        $tag = trim($this->newTag);
        if (!$tag) return;
        RhCurriculoTag::create(['curriculo_id' => $currId, 'tag' => $this->sanitize($tag)]);
        $this->newTag = '';
        unset($this->curriculoDrawer);
    }

    public function removeTag(int $tagId): void
    {
        $this->requireRhOrAdmin();
        RhCurriculoTag::findOrFail($tagId)->delete();
        unset($this->curriculoDrawer);
    }

    public function toggleFavorito(int $id): void
    {
        $c = RhCurriculo::findOrFail($id);
        $c->update(['status' => $c->status === 'favorito' ? 'ativo' : 'favorito']);
        unset($this->curriculos, $this->curriculoDrawer);
    }

    public function moverBancoTalentos(int $id): void
    {
        RhCurriculo::findOrFail($id)->update(['status' => 'banco_talentos']);
        unset($this->curriculos, $this->curriculoDrawer);
        $this->alertSuccess('Movido para banco de talentos!');
    }

    public function confirmDeleteCurr(int $id): void { $this->currDeleteId = $id; $this->currDeleteModal = true; }

    public function deleteCurriculo(): void
    {
        $this->requireRhOrAdmin();
        $c = RhCurriculo::findOrFail($this->currDeleteId);
        if ($c->arquivo_path) Storage::disk('public')->delete($c->arquivo_path);
        $c->delete();
        $this->currDeleteModal = false;
        $this->currDrawer      = false;
        unset($this->curriculos);
        $this->alertSuccess('Currículo excluído.');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ABA: VAGAS
    // ═══════════════════════════════════════════════════════════════════

    public string $vagaSearch    = '';
    public string $vagaStatus    = '';
    public string $vagaModal2    = '';   // '' | 'create' | 'edit'
    #[Locked]
    public ?int   $vagaEditId    = null;

    // Campos vaga
    public string $vTitulo       = '';
    public string $vCargo        = '';
    public string $vDescricao    = '';
    public string $vRequisitos   = '';
    public string $vCompetencias = '';
    public string $vBeneficios   = '';
    public string $vSalMin       = '';
    public string $vSalMax       = '';
    public string $vModalidade   = 'presencial';
    public string $vCidade       = '';
    public string $vEstado       = '';
    public string $vStatus       = 'rascunho';
    public int    $vNota         = 60;
    public string $vSla          = '';
    public int    $vQtd          = 1;
    public string $vDataEnc      = '';
    public array  $etapas        = [];

    public bool   $vagaDeleteModal = false;
    #[Locked]
    public ?int   $vagaDeleteId    = null;

    public bool   $vagaDrawer      = false;
    #[Locked]
    public ?int   $vagaDrawerId    = null;

    #[Computed]
    public function vagas()
    {
        return RhVaga::withCount('candidaturas')
            ->when($this->vagaSearch, fn($q) =>
                $q->where('titulo', 'ilike', "%{$this->vagaSearch}%")
                  ->orWhere('cargo', 'ilike', "%{$this->vagaSearch}%")
            )
            ->when($this->vagaStatus, fn($q) => $q->where('status', $this->vagaStatus))
            ->latest()->paginate(12);
    }

    #[Computed]
    public function vagaDrawerData(): ?RhVaga
    {
        if (!$this->vagaDrawerId) return null;
        return RhVaga::with(['etapas','creator','candidaturas.curriculo','candidaturas.etapa'])
            ->withCount('candidaturas')->find($this->vagaDrawerId);
    }

    public function openVagaCreate(): void { $this->resetVagaForm(); $this->vagaEditId = null; $this->vagaModal2 = 'create'; }

    public function openVagaEdit(int $id): void
    {
        $v = RhVaga::with('etapas')->findOrFail($id);
        $this->vagaEditId    = $id;
        $this->vTitulo       = $v->titulo;
        $this->vCargo        = $v->cargo;
        $this->vDescricao    = $v->descricao ?? '';
        $this->vRequisitos   = $v->requisitos ?? '';
        $this->vCompetencias = $v->competencias ?? '';
        $this->vBeneficios   = $v->beneficios ?? '';
        $this->vSalMin       = $v->salario_min ?? '';
        $this->vSalMax       = $v->salario_max ?? '';
        $this->vModalidade   = $v->modalidade;
        $this->vCidade       = $v->cidade ?? '';
        $this->vEstado       = $v->estado ?? '';
        $this->vStatus       = $v->status;
        $this->vNota         = $v->nota_minima_aprovacao;
        $this->vSla          = $v->sla_dias ?? '';
        $this->vQtd          = $v->vagas_disponiveis;
        $this->vDataEnc      = $v->data_encerramento?->format('Y-m-d') ?? '';
        $this->etapas        = $v->etapas->map(fn($e) => [
            'nome' => $e->nome, 'cor' => $e->cor,
            'is_aprovado' => $e->is_aprovado, 'is_reprovado' => $e->is_reprovado,
            'is_banco_talentos' => $e->is_banco_talentos,
        ])->toArray();
        $this->vagaModal2 = 'edit';
    }

    public function saveVaga(): void
    {
        $this->requireRhOrAdmin();
        $this->validate(['vTitulo' => 'required|string|max:200', 'vCargo' => 'required|string|max:200']);

        $data = [
            'created_by'            => Auth::id(),
            'titulo'                => $this->sanitize($this->vTitulo),
            'cargo'                 => $this->sanitize($this->vCargo),
            'descricao'             => $this->sanitize($this->vDescricao),
            'requisitos'            => $this->sanitize($this->vRequisitos),
            'competencias'          => $this->sanitize($this->vCompetencias),
            'beneficios'            => $this->sanitize($this->vBeneficios),
            'salario_min'           => $this->vSalMin ? (float)$this->vSalMin : null,
            'salario_max'           => $this->vSalMax ? (float)$this->vSalMax : null,
            'modalidade'            => $this->vModalidade,
            'cidade'                => $this->sanitize($this->vCidade),
            'estado'                => $this->sanitize($this->vEstado),
            'status'                => $this->vStatus,
            'nota_minima_aprovacao' => $this->vNota,
            'sla_dias'              => $this->vSla ?: null,
            'vagas_disponiveis'     => $this->vQtd,
            'data_encerramento'     => $this->vDataEnc ?: null,
        ];

        if ($this->vagaEditId) {
            $vaga = RhVaga::findOrFail($this->vagaEditId);
            $vaga->update($data);
            $vaga->etapas()->delete();
        } else {
            $vaga = RhVaga::create($data);
        }

        foreach ($this->etapas as $ord => $e) {
            $vaga->etapas()->create([
                'nome'              => $this->sanitize($e['nome']),
                'cor'               => $e['cor'],
                'ordem'             => $ord,
                'is_aprovado'       => $e['is_aprovado'] ?? false,
                'is_reprovado'      => $e['is_reprovado'] ?? false,
                'is_banco_talentos' => $e['is_banco_talentos'] ?? false,
            ]);
        }

        $this->vagaModal2 = '';
        unset($this->vagas, $this->vagaDrawerData);
        $this->alertSuccess($this->vagaEditId ? 'Vaga atualizada!' : 'Vaga criada!');
    }

    public function duplicarVaga(int $id): void
    {
        $orig = RhVaga::with('etapas')->findOrFail($id);
        $nova = $orig->replicate(['created_at','updated_at']);
        $nova->titulo = $orig->titulo . ' (cópia)';
        $nova->status = 'rascunho';
        $nova->created_by = Auth::id();
        $nova->save();
        foreach ($orig->etapas as $e) {
            $nova->etapas()->create($e->only(['nome','cor','ordem','is_aprovado','is_reprovado','is_banco_talentos']));
        }
        unset($this->vagas);
        $this->alertSuccess('Vaga duplicada!');
    }

    public function publicarVaga(int $id): void
    {
        RhVaga::findOrFail($id)->update(['status' => 'publicada']);
        unset($this->vagas, $this->vagaDrawerData);
        $this->alertSuccess('Vaga publicada!');
    }

    public function encerrarVaga(int $id): void
    {
        RhVaga::findOrFail($id)->update(['status' => 'encerrada']);
        unset($this->vagas, $this->vagaDrawerData);
        $this->alertSuccess('Vaga encerrada.');
    }

    public function openVagaDrawer(int $id): void { $this->vagaDrawerId = $id; $this->vagaDrawer = true; unset($this->vagaDrawerData); }

    public function confirmDeleteVaga(int $id): void { $this->vagaDeleteId = $id; $this->vagaDeleteModal = true; }

    public function deleteVaga(): void
    {
        $this->requireRhOrAdmin();
        RhVaga::findOrFail($this->vagaDeleteId)->delete();
        $this->vagaDeleteModal = false;
        unset($this->vagas);
        $this->alertSuccess('Vaga excluída.');
    }

    public function addEtapa(): void { $this->etapas[] = ['nome'=>'Nova etapa','cor'=>'bg-slate-400','is_aprovado'=>false,'is_reprovado'=>false,'is_banco_talentos'=>false]; }
    public function removeEtapa(int $i): void { unset($this->etapas[$i]); $this->etapas = array_values($this->etapas); }

    private function resetEtapasPadrao(): void
    {
        $this->etapas = [
            ['nome'=>'Recebido',        'cor'=>'bg-slate-400', 'is_aprovado'=>false,'is_reprovado'=>false,'is_banco_talentos'=>false],
            ['nome'=>'Triagem',          'cor'=>'bg-blue-400',  'is_aprovado'=>false,'is_reprovado'=>false,'is_banco_talentos'=>false],
            ['nome'=>'Em análise',       'cor'=>'bg-indigo-400','is_aprovado'=>false,'is_reprovado'=>false,'is_banco_talentos'=>false],
            ['nome'=>'Teste',            'cor'=>'bg-violet-400','is_aprovado'=>false,'is_reprovado'=>false,'is_banco_talentos'=>false],
            ['nome'=>'Entrevista',       'cor'=>'bg-purple-400','is_aprovado'=>false,'is_reprovado'=>false,'is_banco_talentos'=>false],
            ['nome'=>'Aprovado',         'cor'=>'bg-green-400', 'is_aprovado'=>true, 'is_reprovado'=>false,'is_banco_talentos'=>false],
            ['nome'=>'Reprovado',        'cor'=>'bg-red-400',   'is_aprovado'=>false,'is_reprovado'=>true, 'is_banco_talentos'=>false],
            ['nome'=>'Banco de talentos','cor'=>'bg-amber-400', 'is_aprovado'=>false,'is_reprovado'=>false,'is_banco_talentos'=>true],
        ];
    }

    private function resetVagaForm(): void
    {
        $this->vTitulo = $this->vCargo = $this->vDescricao = $this->vRequisitos =
        $this->vCompetencias = $this->vBeneficios = $this->vSalMin = $this->vSalMax =
        $this->vCidade = $this->vEstado = $this->vSla = $this->vDataEnc = '';
        $this->vModalidade = 'presencial'; $this->vStatus = 'rascunho';
        $this->vNota = 60; $this->vQtd = 1;
        $this->resetEtapasPadrao();
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ABA: PIPELINE
    // ═══════════════════════════════════════════════════════════════════

    public ?int   $pipelineVagaId   = null;
    public string $pipelineSearch   = '';
    public bool   $pipeDrawer       = false;
    #[Locked]
    public ?int   $pipeDrawerId     = null;
    public string $pipeComentario   = '';
    public bool   $vincularModal    = false;
    public string $vincularSearch   = '';
    public string $pipeNotaInput    = '';
    #[Locked]
    public ?int   $pipeNotaCandId   = null;

    // ── Modal confirmação de contratação (onboarding) ─────────────────
    public bool   $contratarModal      = false;
    #[Locked]
    public ?int   $contratarCandId     = null;
    #[Locked]
    public ?int   $contratarEtapaId    = null;
    public string $onbNome             = '';
    public string $onbEmail            = '';
    public string $onbCargo            = '';
    public string $onbDepartamento     = '';
    public string $onbPerfil           = '';
    public string $onbDataInicio       = '';
    public string $onbObservacoes      = '';

    #[Computed]
    public function vagasParaPipeline()
    {
        return RhVaga::whereIn('status', ['publicada','pausada'])->withCount('candidaturas')->orderByDesc('created_at')->get();
    }

    #[Computed]
    public function departamentosOnb(): \Illuminate\Database\Eloquent\Collection
    {
        return Department::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function perfisOnb(): \Illuminate\Database\Eloquent\Collection
    {
        return AccessProfile::orderBy('name')->get(['id', 'name', 'slug']);
    }

    #[Computed]
    public function vagaAtualPipeline(): ?RhVaga
    {
        if (!$this->pipelineVagaId) return null;
        return RhVaga::with('etapas')->find($this->pipelineVagaId);
    }

    #[Computed]
    public function kanban(): array
    {
        if (!$this->pipelineVagaId) return [];
        $etapas = RhVagaEtapa::where('vaga_id', $this->pipelineVagaId)->orderBy('ordem')->get();
        $vagaId = $this->pipelineVagaId;
        $cands  = RhCandidatura::with([
                'curriculo.tags',
                'curriculo.habilidades',
                'curriculo.testes' => fn($q) => $q->where(fn($w) => $w->where('vaga_id', $vagaId)->orWhereNull('vaga_id'))
                                                   ->whereIn('status', ['concluido', 'em_andamento', 'pendente'])
                                                   ->orderByDesc('created_at'),
            ])
            ->where('vaga_id', $vagaId)
            ->when($this->pipelineSearch, fn($q) =>
                $q->whereHas('curriculo', fn($s) => $s->where('nome', 'ilike', "%{$this->pipelineSearch}%"))
            )
            ->get()->groupBy('etapa_id');
        return $etapas->map(fn($e) => [
            'etapa'        => $e,
            'candidaturas' => $cands->get($e->id, collect()),
            'count'        => $cands->get($e->id, collect())->count(),
        ])->toArray();
    }

    #[Computed]
    public function pipeDrawerCand(): ?RhCandidatura
    {
        if (!$this->pipeDrawerId) return null;
        $cand = RhCandidatura::with([
            'curriculo.experiencias','curriculo.formacoes','curriculo.habilidades',
            'curriculo.tags','vaga.etapas','etapa',
            'historicos.user','comentarios.user',
        ])->find($this->pipeDrawerId);

        if ($cand) {
            // Carrega testes desta vaga (ou sem vaga vinculada) com o modelo do teste
            $cand->curriculo->setRelation('testes',
                RhCandidatoTeste::with('teste')
                    ->where('curriculo_id', $cand->curriculo_id)
                    ->where(fn($w) => $w->where('vaga_id', $cand->vaga_id)->orWhereNull('vaga_id'))
                    ->orderByDesc('created_at')
                    ->get()
            );
        }

        return $cand;
    }

    #[Computed]
    public function curriculosParaVincular()
    {
        if (!$this->pipelineVagaId) return collect();
        $jaIds = RhCandidatura::where('vaga_id', $this->pipelineVagaId)->pluck('curriculo_id');
        return RhCurriculo::whereNotIn('id', $jaIds)
            ->when($this->vincularSearch, fn($q) =>
                $q->where('nome', 'ilike', "%{$this->vincularSearch}%")
                  ->orWhere('email', 'ilike', "%{$this->vincularSearch}%")
            )->limit(20)->get();
    }

    public function moverCandidato(int $candId, int $etapaId): void
    {
        $this->requireRhOrAdmin();
        $cand = RhCandidatura::with(['curriculo', 'vaga'])->findOrFail($candId);
        $nova = RhVagaEtapa::findOrFail($etapaId);

        // Se a etapa destino é de aprovação → abre modal de contratação
        // Considera is_aprovado OU nome que contenha "aprovad" (fallback para etapas não configuradas)
        $ehAprovacao = $nova->is_aprovado
            || str_contains(mb_strtolower($nova->nome), 'aprovad');

        if ($ehAprovacao) {
            // Marca a flag no banco se ainda não estava marcada
            if (!$nova->is_aprovado) {
                $nova->update(['is_aprovado' => true]);
            }
            $curriculo = $cand->curriculo;

            // Verifica se já existe um usuário criado para este currículo
            if ($curriculo->user_id) {
                $this->alertError(
                    'Já contratado',
                    'Um usuário já foi criado para este candidato anteriormente.'
                );
                return;
            }

            $this->contratarCandId     = $candId;
            $this->contratarEtapaId    = $etapaId;
            $this->onbNome             = $curriculo->nome ?? '';
            $emailSugerido = ConfiguracaoEmpresa::gerarEmail($curriculo->nome ?? '');
            $this->onbEmail = $emailSugerido ?: ($curriculo->email ?? '');
            $this->onbCargo            = $cand->vaga?->cargo ?? '';
            $this->onbDepartamento     = '';
            $this->onbPerfil           = (string) (AccessProfile::where('slug','employee')->value('id') ?? '');
            $this->onbDataInicio       = now()->addDays(7)->toDateString();
            $this->onbObservacoes      = '';
            $this->contratarModal      = true;
            return;
        }

        // Para etapas normais (não aprovação), move direto
        $this->_executarMoverCandidato($cand, $nova, $etapaId);
    }

    public function confirmarContratacao(): void
    {
        $this->requireRhOrAdmin();

        $this->validate([
            'onbNome'         => 'required|string|max:255',
            'onbEmail'        => 'required|email|unique:users,email',
            'onbCargo'        => 'required|string|max:150',
            'onbDepartamento' => 'nullable|exists:departments,id',
            'onbPerfil'       => 'nullable|exists:access_profiles,id',
            'onbDataInicio'   => 'nullable|date',
        ], [
            'onbNome.required'   => 'O nome é obrigatório.',
            'onbEmail.required'  => 'O e-mail é obrigatório.',
            'onbEmail.email'     => 'Informe um e-mail válido.',
            'onbEmail.unique'    => 'Este e-mail já está cadastrado no sistema.',
            'onbCargo.required'  => 'O cargo é obrigatório.',
        ]);

        $cand = RhCandidatura::with(['curriculo', 'vaga'])->findOrFail($this->contratarCandId);
        $nova = RhVagaEtapa::findOrFail($this->contratarEtapaId);

        // 1. Criar o usuário
        $user = User::create([
            'name'              => trim($this->onbNome),
            'email'             => trim($this->onbEmail),
            'password'          => Hash::make('lu753951'),
            'position'          => trim($this->onbCargo),
            'department_id'     => $this->onbDepartamento ?: null,
            'access_profile_id' => $this->onbPerfil ?: null,
            'is_active'         => true,
        ]);

        // 2. Vincular user ao currículo
        $cand->curriculo->update(['user_id' => $user->id]);

        // 3. Mover candidato para etapa de aprovação
        $this->_executarMoverCandidato($cand, $nova, $this->contratarEtapaId);

        // 4. Criar registro de onboarding
        $onboarding = RhOnboarding::create([
            'candidatura_id' => $this->contratarCandId,
            'user_id'        => $user->id,
            'criado_by'      => Auth::id(),
            'department_id'  => $this->onbDepartamento ?: null,
            'cargo'          => trim($this->onbCargo),
            'data_inicio'    => $this->onbDataInicio ?: null,
            'status'         => 'em_andamento',
            'observacoes'    => $this->onbObservacoes ?: null,
        ]);

        // 5. Criar tarefas padrão
        foreach (RhOnboarding::tarefasPadrao() as $tarefa) {
            RhOnboardingTarefa::create([
                'onboarding_id' => $onboarding->id,
                'titulo'        => $tarefa['titulo'],
                'responsavel'   => $tarefa['responsavel'],
                'ordem'         => $tarefa['ordem'],
                'status'        => 'pendente',
            ]);
        }

        // 6. Notificar equipe RH
        $this->notificarRhAdmin(new CandidatoContratadoNotification($onboarding->load('user')));

        // 7. Fechar modal e resetar
        $this->contratarModal   = false;
        $this->contratarCandId  = null;
        $this->contratarEtapaId = null;

        $this->toastNotif(
            'Contratação confirmada!',
            "Usuário criado para {$this->onbNome}. Onboarding iniciado com 8 tarefas.",
            'user-check', 'green',
            route('rh.onboarding')
        );

        unset($this->kanban, $this->pipeDrawerCand);
    }

    private function _executarMoverCandidato(RhCandidatura $cand, RhVagaEtapa $nova, int $etapaId): void
    {
        $ant        = $cand->etapa?->nome ?? 'Sem etapa';
        $novoStatus = 'ativo';
        if ($nova->is_aprovado)            $novoStatus = 'aprovado';
        elseif ($nova->is_reprovado)       $novoStatus = 'reprovado';
        elseif ($nova->is_banco_talentos)  $novoStatus = 'banco_talentos';

        $cand->update([
            'etapa_id'    => $etapaId,
            'status'      => $novoStatus,
            'aprovado_at' => $novoStatus === 'aprovado' ? now() : null,
        ]);

        RhCandidaturaHistorico::create([
            'candidatura_id' => $cand->id,
            'user_id'        => Auth::id(),
            'etapa_anterior' => $ant,
            'etapa_nova'     => $nova->nome,
            'acao'           => 'mover_etapa',
            'descricao'      => "Movido de '{$ant}' para '{$nova->nome}'",
            'created_at'     => now(),
        ]);

        unset($this->kanban, $this->pipeDrawerCand);
    }

    public function addPipeComentario(): void
    {
        $this->requireRhOrAdmin();
        $txt = trim($this->pipeComentario);
        if (!$txt) return;
        RhCandidaturaComentario::create(['candidatura_id'=>$this->pipeDrawerId,'user_id'=>Auth::id(),'comentario'=>$this->sanitize($txt)]);
        RhCandidaturaHistorico::create(['candidatura_id'=>$this->pipeDrawerId,'user_id'=>Auth::id(),'acao'=>'comentario','descricao'=>'Comentário adicionado.','created_at'=>now()]);
        $this->pipeComentario = '';
        unset($this->pipeDrawerCand);
    }

    public function salvarNotaCand(int $candId): void
    {
        $nota = (float) $this->pipeNotaInput;
        if ($nota < 0 || $nota > 100) { $this->alertError('Nota inválida.'); return; }
        RhCandidatura::findOrFail($candId)->update(['nota_final'=>$nota]);
        $this->recalcularRanking();
        $this->pipeNotaInput = '';
        $this->pipeNotaCandId = null;
        unset($this->kanban, $this->pipeDrawerCand);
        $this->alertSuccess('Nota salva!');
    }

    private function recalcularRanking(): void
    {
        if (!$this->pipelineVagaId) return;
        RhCandidatura::where('vaga_id', $this->pipelineVagaId)->whereNotNull('nota_final')
            ->orderByDesc('nota_final')->get()
            ->each(fn($c, $i) => $c->update(['ranking_posicao'=>$i+1]));
    }

    public function openVincular(): void { $this->vincularModal = true; $this->vincularSearch = ''; unset($this->curriculosParaVincular); }

    public function vincularCandidato(int $currId): void
    {
        $this->requireRhOrAdmin();
        $primeiraEtapa = RhVagaEtapa::where('vaga_id', $this->pipelineVagaId)->orderBy('ordem')->first();

        $cand = RhCandidatura::firstOrCreate(
            ['curriculo_id' => $currId, 'vaga_id' => $this->pipelineVagaId],
            ['etapa_id' => $primeiraEtapa?->id, 'status' => 'ativo']
        );

        if ($cand->wasRecentlyCreated) {
            RhCandidaturaHistorico::create([
                'candidatura_id' => $cand->id,
                'user_id'        => Auth::id(),
                'acao'           => 'vinculado',
                'descricao'      => 'Candidato vinculado à vaga.',
                'created_at'     => now(),
            ]);
            $this->alertSuccess('Candidato vinculado!');
        } else {
            $this->alertError('Este candidato já está vinculado a esta vaga.');
        }

        $this->vincularModal = false;
        unset($this->kanban, $this->curriculosParaVincular);
    }

    public function openPipeDrawer(int $id): void { $this->pipeDrawerId = $id; $this->pipeDrawer = true; unset($this->pipeDrawerCand); }

    // ═══════════════════════════════════════════════════════════════════
    //  ABA: TESTES
    // ═══════════════════════════════════════════════════════════════════

    public string $testeSearch    = '';
    public string $testeAba       = 'lista';  // lista | resultados
    public bool   $testeModal     = false;
    #[Locked]
    public ?int   $testeEditId    = null;
    public string $tTitulo        = '';
    public string $tDescricao     = '';
    public string $tInstrucoes    = '';
    public int    $tTempo         = 60;
    public bool   $tRandomQ       = false;
    public bool   $tRandomO       = false;
    public int    $tNota          = 60;
    public bool   $tAtivo         = true;
    public ?int   $tVagaId        = null;
    public array  $questoes        = [];

    public bool   $testeDrawer    = false;
    #[Locked]
    public ?int   $testeDrawerId  = null;

    public bool   $enviarModal    = false;
    public string $enviarSearch   = '';
    #[Locked]
    public ?int   $enviarTesteId  = null;
    #[Locked]
    public ?int   $enviarVagaId   = null;


    public bool   $testeDeleteModal = false;
    #[Locked]
    public ?int   $testeDeleteId    = null;

    #[Computed]
    public function testes()
    {
        return RhTeste::with('vaga')->withCount(['questoes','tentativas'])
            ->when($this->testeSearch, fn($q) => $q->where('titulo','ilike',"%{$this->testeSearch}%"))
            ->latest()->paginate(12);
    }

    #[Computed]
    public function vagasParaTeste()
    {
        return RhVaga::orderBy('titulo')->get(['id','titulo','status']);
    }

    #[Computed]
    public function resultados()
    {
        return RhCandidatoTeste::with(['curriculo','teste','vaga'])
            ->where('status','concluido')->orderByDesc('concluido_at')->paginate(15);
    }

    #[Computed]
    public function testeDrawerData(): ?RhTeste
    {
        if (!$this->testeDrawerId) return null;
        return RhTeste::with(['questoes.opcoes','creator'])->find($this->testeDrawerId);
    }

    #[Computed]
    public function curriculosBuscaEnvio()
    {
        if (!$this->enviarSearch || strlen($this->enviarSearch) < 2) return collect();

        $curriculos = RhCurriculo::where('nome','ilike',"%{$this->enviarSearch}%")
            ->orWhere('email','ilike',"%{$this->enviarSearch}%")->limit(10)->get();

        if ($this->enviarTesteId && $curriculos->isNotEmpty()) {
            // Marca quais já têm tentativa não-expirada para este teste
            $jaEnviados = RhCandidatoTeste::where('teste_id', $this->enviarTesteId)
                ->whereIn('curriculo_id', $curriculos->pluck('id'))
                ->whereIn('status', ['pendente', 'em_andamento', 'concluido'])
                ->pluck('status', 'curriculo_id');

            $curriculos->each(fn($cv) => $cv->teste_status = $jaEnviados->get($cv->id));
        }

        return $curriculos;
    }

    public function openTesteCreate(): void { $this->resetTesteForm(); $this->testeEditId = null; $this->testeModal = true; }

    public function openTesteEdit(int $id): void
    {
        $t = RhTeste::with('questoes.opcoes')->findOrFail($id);
        $this->testeEditId  = $id;
        $this->tTitulo      = $t->titulo;
        $this->tDescricao   = $t->descricao ?? '';
        $this->tInstrucoes  = $t->instrucoes ?? '';
        $this->tTempo       = $t->tempo_limite_minutos;
        $this->tRandomQ     = $t->randomizar_questoes;
        $this->tRandomO     = $t->randomizar_opcoes;
        $this->tNota        = $t->nota_aprovacao;
        $this->tAtivo       = $t->ativo;
        $this->tVagaId      = $t->vaga_id;
        $this->questoes     = $t->questoes->map(fn($q) => [
            'enunciado'=>$q->enunciado,'tipo'=>$q->tipo,'peso'=>$q->peso,
            'opcoes'=>$q->opcoes->map(fn($o) => ['texto'=>$o->texto,'correta'=>$o->correta])->toArray(),
        ])->toArray();
        $this->testeModal = true;
    }

    public function saveTeste(): void
    {
        $this->requireRhOrAdmin();
        $this->validate(['tTitulo'=>'required|string|max:200','tTempo'=>'required|integer|min:1|max:480']);
        $data = [
            'created_by'=>Auth::id(),'vaga_id'=>$this->tVagaId ?: null,
            'titulo'=>$this->sanitize($this->tTitulo),
            'descricao'=>$this->sanitize($this->tDescricao),'instrucoes'=>$this->sanitize($this->tInstrucoes),
            'tempo_limite_minutos'=>$this->tTempo,'randomizar_questoes'=>$this->tRandomQ,
            'randomizar_opcoes'=>$this->tRandomO,'nota_aprovacao'=>$this->tNota,'ativo'=>$this->tAtivo,
        ];
        if ($this->testeEditId) {
            $teste = RhTeste::findOrFail($this->testeEditId);
            $teste->update($data);
            $teste->questoes()->each(fn($q) => $q->opcoes()->delete());
            $teste->questoes()->delete();
        } else { $teste = RhTeste::create($data); }

        foreach ($this->questoes as $ord => $qd) {
            $q = $teste->questoes()->create(['enunciado'=>$this->sanitize($qd['enunciado']),'tipo'=>$qd['tipo'],'peso'=>$qd['peso']??1,'ordem'=>$ord]);
            if ($qd['tipo']==='objetiva') {
                foreach ($qd['opcoes']??[] as $oi => $od) {
                    $q->opcoes()->create(['texto'=>$this->sanitize($od['texto']),'correta'=>(bool)($od['correta']??false),'ordem'=>$oi]);
                }
            }
        }
        $this->testeModal = false;
        unset($this->testes, $this->testeDrawerData, $this->vagasParaTeste);
        $this->alertSuccess($this->testeEditId ? 'Teste atualizado!' : 'Teste criado!');
    }

    public function addQuestao(string $tipo = 'objetiva'): void
    {
        $this->questoes[] = ['enunciado'=>'','tipo'=>$tipo,'peso'=>1,
            'opcoes'=>$tipo==='objetiva'?[['texto'=>'','correta'=>false],['texto'=>'','correta'=>false],['texto'=>'','correta'=>false],['texto'=>'','correta'=>false]]:[]];
    }
    public function removeQuestao(int $i): void { unset($this->questoes[$i]); $this->questoes=array_values($this->questoes); }
    public function addOpcao(int $qi): void { $this->questoes[$qi]['opcoes'][]=['texto'=>'','correta'=>false]; }
    public function removeOpcao(int $qi,int $oi): void { unset($this->questoes[$qi]['opcoes'][$oi]); $this->questoes[$qi]['opcoes']=array_values($this->questoes[$qi]['opcoes']); }
    public function setCorreta(int $qi,int $oi): void { foreach($this->questoes[$qi]['opcoes'] as $i=>$_){$this->questoes[$qi]['opcoes'][$i]['correta']=($i===$oi);} }

    private function resetTesteForm(): void
    {
        $this->tTitulo=$this->tDescricao=$this->tInstrucoes='';
        $this->tTempo=60; $this->tNota=60; $this->tVagaId=null;
        $this->tRandomQ=$this->tRandomO=false; $this->tAtivo=true; $this->questoes=[];
    }

    public function openEnviar(int $id, ?int $vagaId = null): void
    {
        $this->enviarTesteId = $id;
        $this->enviarVagaId  = $vagaId;
        $this->enviarSearch  = '';
        $this->enviarModal   = true;
    }

    public function enviarTesteCand(int $currId): void
    {
        // Bloqueia se já existe tentativa ativa ou concluída para este teste
        $jaExiste = RhCandidatoTeste::where('curriculo_id', $currId)
            ->where('teste_id', $this->enviarTesteId)
            ->whereIn('status', ['pendente', 'em_andamento', 'concluido'])
            ->exists();

        if ($jaExiste) {
            $this->alertError('Este candidato já possui uma tentativa ativa ou concluída para este teste. Use "Refazer" na aba Resultados para autorizar uma nova tentativa.');
            return;
        }

        $curriculo = RhCurriculo::findOrFail($currId);
        $token = RhCandidatoTeste::create([
            'curriculo_id' => $currId,
            'teste_id'     => $this->enviarTesteId,
            'vaga_id'      => $this->enviarVagaId,
            'status'       => 'pendente',
            'expira_at'    => now()->addDays(7),
        ]);
        $this->enviarModal = false;
        $this->dispatch('teste-enviado',
            link: url('/candidato/teste/' . $token->token),
            nome: $curriculo->nome,
        );
    }

    public function autorizarRefazer(int $tentId): void
    {
        $this->requireRhOrAdmin();
        $t = RhCandidatoTeste::findOrFail($tentId);

        $t->update([
            'status'       => 'pendente',
            'iniciado_at'  => null,
            'concluido_at' => null,
            'nota'         => null,
            'aprovado'     => null,
            'token'        => \Illuminate\Support\Str::random(64),
            'expira_at'    => now()->addDays(7),
        ]);

        // Apaga respostas anteriores
        $t->respostas()->delete();

        unset($this->resultados);

        $this->dispatch('teste-enviado',
            link: url('/candidato/teste/' . $t->fresh()->token),
            nome: $t->curriculo->nome,
        );
    }

    public function corrigirTeste(int $tentId): void
    {
        $t = RhCandidatoTeste::with(['respostas.questao','respostas.opcao','teste.questoes'])->findOrFail($tentId);
        $totalPeso = $t->teste->questoes->sum('peso');
        $acertos   = 0;
        foreach ($t->respostas as $r) {
            if ($r->questao->tipo==='objetiva' && $r->opcao?->correta) { $r->update(['correta'=>true,'nota_obtida'=>$r->questao->peso]); $acertos+=$r->questao->peso; }
            elseif ($r->questao->tipo==='objetiva') { $r->update(['correta'=>false,'nota_obtida'=>0]); }
        }
        $nota=$totalPeso>0?round($acertos/$totalPeso*100,2):0;
        $t->update(['nota'=>$nota,'aprovado'=>$nota>=$t->teste->nota_aprovacao]);
        unset($this->resultados);
        $this->alertSuccess('Corrigido!','Nota: '.$nota);
    }

    public function openTesteDrawer(int $id): void { $this->testeDrawerId=$id; $this->testeDrawer=true; unset($this->testeDrawerData); }
    public function confirmDeleteTeste(int $id): void { $this->testeDeleteId=$id; $this->testeDeleteModal=true; }
    public function deleteTeste(): void { $this->requireRhOrAdmin(); RhTeste::findOrFail($this->testeDeleteId)->delete(); $this->testeDeleteModal=false; unset($this->testes); $this->alertSuccess('Teste excluído.'); }

    // ═══════════════════════════════════════════════════════════════════
    //  OCR
    // ═══════════════════════════════════════════════════════════════════

    public function reprocessarOcr(int $id): void
    {
        $this->requireRhOrAdmin();
        $curriculo = RhCurriculo::findOrFail($id);

        if (!$curriculo->arquivo_path) {
            $this->alertError('Sem arquivo', 'Este currículo não possui arquivo para processar.');
            return;
        }

        // Roda o OCR de forma síncrona para o resultado aparecer imediatamente
        $ocr   = app(\App\Services\OcrService::class);
        $texto = $ocr->extrair($curriculo->arquivo_path, $curriculo->arquivo_mime);

        if ($texto) {
            $curriculo->updateQuietly([
                'texto_ocr'         => $texto,
                'ocr_processado_at' => now(),
            ]);
            // Força recarga do drawer e da lista
            unset($this->curriculos, $this->curriculoDrawer);
            $this->alertSuccess('OCR concluído!', 'Texto extraído com sucesso.');
        } else {
            $this->alertError('Falha no OCR', 'Não foi possível extrair texto deste arquivo. Verifique os logs do Laravel para mais detalhes.');
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ABA: RELATÓRIO DE CLIMA UNIFICADO
    // ═══════════════════════════════════════════════════════════════════

    public string $climaInicio       = '';
    public string $climaFim          = '';
    public string $climaDepartamento = '';

    public function climaAplicar(): void
    {
        unset($this->climaUnificado);
    }

    public function climaLimpar(): void
    {
        $this->climaInicio       = '';
        $this->climaFim          = '';
        $this->climaDepartamento = '';
        unset($this->climaUnificado);
    }

    #[Computed]
    public function climaUnificado(): array
    {
        $inicio = $this->climaInicio
            ? \Carbon\Carbon::parse($this->climaInicio)->startOfDay()
            : now()->subDays(30)->startOfDay();
        $fim = $this->climaFim
            ? \Carbon\Carbon::parse($this->climaFim)->endOfDay()
            : now()->endOfDay();

        $deptId = $this->climaDepartamento ?: null;

        // ── 1. HUMOR ──────────────────────────────────────────────────
        $moodQuery = MoodCheckin::whereBetween('checkin_date', [$inicio, $fim]);
        if ($deptId) {
            $moodQuery->whereHas('user', fn ($q) => $q->where('department_id', $deptId));
        }
        $checkins = $moodQuery->get();

        $moodScoreMap = ['otimo' => 4, 'bem' => 3, 'normal' => 2, 'pessimo' => 1];
        $totalCheckins = $checkins->count();
        $moodScore = $totalCheckins > 0
            ? round(($checkins->sum(fn ($c) => $moodScoreMap[$c->mood] ?? 0) / ($totalCheckins * 4)) * 100)
            : null;

        $moodDistrib = [];
        foreach (MoodCheckin::MOODS as $key => $cfg) {
            $cnt = $checkins->where('mood', $key)->count();
            $moodDistrib[$key] = [
                'label'  => $cfg['label'],
                'icon'   => $cfg['icon'],
                'hex'    => $cfg['hex'],
                'count'  => $cnt,
                'pct'    => $totalCheckins > 0 ? round(($cnt / $totalCheckins) * 100) : 0,
            ];
        }

        // Tendência diária de humor (últimos dias do período)
        $moodTrend = $checkins
            ->groupBy(fn ($c) => $c->checkin_date->format('d/m'))
            ->map(fn ($day) => [
                'score' => round(($day->sum(fn ($c) => $moodScoreMap[$c->mood] ?? 0) / ($day->count() * 4)) * 100),
                'total' => $day->count(),
            ])
            ->toArray();

        // ── 2. PESQUISAS / eNPS ───────────────────────────────────────
        $scaleQuestionIds = SurveyQuestion::where('type', 'escala')->pluck('id');

        $answersQuery = SurveyAnswer::whereIn('question_id', $scaleQuestionIds)
            ->whereNotNull('value_scale')
            ->whereBetween('created_at', [$inicio, $fim]);
        $scaleAnswers = $answersQuery->pluck('value_scale');

        $totalRespostas = $scaleAnswers->count();
        $pesquisaScoreRaw = $totalRespostas > 0 ? $scaleAnswers->avg() : null;
        // Normaliza 0-10 → 0-100
        $pesquisaScore = $pesquisaScoreRaw !== null ? round($pesquisaScoreRaw * 10) : null;

        // eNPS: promotores (9-10), neutros (7-8), detratores (0-6)
        $promotores = $scaleAnswers->filter(fn ($v) => $v >= 9)->count();
        $neutros    = $scaleAnswers->filter(fn ($v) => $v >= 7 && $v <= 8)->count();
        $detratores = $scaleAnswers->filter(fn ($v) => $v <= 6)->count();
        $eNPS = $totalRespostas > 0
            ? round((($promotores - $detratores) / $totalRespostas) * 100)
            : null;

        // Complemento eNPS: entrevistas de desligamento
        $entrevistasIds = RhEntrevistaDesligamento::whereBetween('respondido_at', [$inicio, $fim])
            ->whereNotNull('recomendaria_empresa')
            ->get();
        $exitTotal = $entrevistasIds->count();
        $exitRecomenda = $entrevistasIds->where('recomendaria_empresa', true)->count();

        // ── 3. FEEDBACKS ──────────────────────────────────────────────
        $fbQuery = Feedback::whereBetween('created_at', [$inicio, $fim]);
        if ($deptId) {
            $fbQuery->whereHas('employee', fn ($q) => $q->where('department_id', $deptId));
        }
        $feedbacks = $fbQuery->get();
        $totalFeedbacks  = $feedbacks->count();
        $reconhecimentos = $feedbacks->where('type', 'reconhecimento')->count();
        $sugestoes       = $feedbacks->where('type', 'sugestao')->count();
        $alertas         = $feedbacks->where('type', 'alerta')->count();
        $criticos        = $feedbacks->where('severity', 'critico')->count();
        $feedbackScore   = $totalFeedbacks > 0
            ? round((($reconhecimentos - $alertas) / $totalFeedbacks) * 50 + 50)
            : null;

        // ── 4. ÍNDICE DE CLIMA GERAL ──────────────────────────────────
        // Pesos: Humor 40%, Pesquisas 40%, Feedback 20%
        $scores  = [];
        $weights = [];
        if ($moodScore !== null)     { $scores[] = $moodScore * 0.4;     $weights[] = 0.4; }
        if ($pesquisaScore !== null) { $scores[] = $pesquisaScore * 0.4; $weights[] = 0.4; }
        if ($feedbackScore !== null) { $scores[] = $feedbackScore * 0.2; $weights[] = 0.2; }
        $pesoTotal  = array_sum($weights);
        $climaGeral = $pesoTotal > 0 ? round(array_sum($scores) / $pesoTotal) : null;

        // Classificação do clima
        $climaLabel = match(true) {
            $climaGeral === null    => '—',
            $climaGeral >= 80       => 'Excelente',
            $climaGeral >= 65       => 'Bom',
            $climaGeral >= 50       => 'Regular',
            $climaGeral >= 35       => 'Atenção',
            default                 => 'Crítico',
        };
        $climaCor = match(true) {
            $climaGeral === null    => 'slate',
            $climaGeral >= 80       => 'emerald',
            $climaGeral >= 65       => 'blue',
            $climaGeral >= 50       => 'amber',
            $climaGeral >= 35       => 'orange',
            default                 => 'rose',
        };

        return compact(
            'inicio', 'fim',
            'totalCheckins', 'moodScore', 'moodDistrib', 'moodTrend',
            'totalRespostas', 'pesquisaScore', 'pesquisaScoreRaw',
            'eNPS', 'promotores', 'neutros', 'detratores',
            'exitTotal', 'exitRecomenda',
            'totalFeedbacks', 'reconhecimentos', 'sugestoes', 'alertas', 'criticos', 'feedbackScore',
            'climaGeral', 'climaLabel', 'climaCor'
        );
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ABA: RELATÓRIO DE TURNOVER
    // ═══════════════════════════════════════════════════════════════════

    public string $tvInicio       = '';
    public string $tvFim          = '';
    public string $tvDepartamento = '';
    public string $tvTipo         = '';

    public function tvAplicar(): void
    {
        unset($this->turnoverRelatorio);
    }

    public function tvLimpar(): void
    {
        $this->tvInicio       = '';
        $this->tvFim          = '';
        $this->tvDepartamento = '';
        $this->tvTipo         = '';
        unset($this->turnoverRelatorio);
    }

    #[Computed]
    public function turnoverRelatorio(): array
    {
        $inicio = $this->tvInicio ? \Carbon\Carbon::parse($this->tvInicio)->startOfDay() : now()->subMonths(12)->startOfDay();
        $fim    = $this->tvFim    ? \Carbon\Carbon::parse($this->tvFim)->endOfDay()     : now()->addMonths(6)->endOfDay();

        $query = RhDesligamento::with(['funcionario.department', 'entrevista', 'verbas'])
            ->whereBetween('data_ultimo_dia', [$inicio, $fim]);

        if ($this->tvDepartamento) {
            $query->whereHas('funcionario', fn ($q) => $q->where('department_id', $this->tvDepartamento));
        }
        if ($this->tvTipo) {
            $query->where('tipo', $this->tvTipo);
        }

        $desligamentos = $query->get();
        $total = $desligamentos->count();

        // Média de funcionários ativos no período (headcount médio)
        $headcountMedio = User::where('is_active', true)->count() + intdiv($total, 2);
        $meses = max(1, (int) $inicio->diffInMonths($fim) + 1);
        $taxa = $headcountMedio > 0 ? round(($total / $headcountMedio) * 100, 2) : 0;
        $taxaMensal = $meses > 0 ? round($taxa / $meses, 2) : 0;

        // Custos  (verbas é HasOne — objeto único, não coleção)
        $custoRescisao = $desligamentos->sum(fn ($d) => (float) ($d->verbas?->total_liquido ?? 0));

        $salarios = $desligamentos->map(function ($d) {
            return $d->verbas?->salario_base ? (float) $d->verbas->salario_base : null;
        })->filter()->values();
        $salarioMedio = $salarios->count() > 0 ? $salarios->avg() : 0;
        $custoReposicao = round($salarioMedio * 12 * 1.5 * $total, 2);
        $custoTotal = round($custoRescisao + $custoReposicao, 2);

        // Por departamento
        $porDepto = $desligamentos
            ->groupBy(fn ($d) => $d->funcionario?->department?->name ?? 'Sem departamento')
            ->map(fn ($items, $nome) => [
                'nome'  => $nome,
                'total' => $items->count(),
                'custo' => round($items->sum(fn ($d) => (float) ($d->verbas?->total_liquido ?? 0)), 2),
            ])
            ->sortByDesc('total')
            ->values()
            ->toArray();

        // Por cargo
        $porCargo = $desligamentos
            ->groupBy(fn ($d) => $d->funcionario?->position ?? 'Sem cargo')
            ->map(fn ($items, $nome) => [
                'nome'  => $nome,
                'total' => $items->count(),
            ])
            ->sortByDesc('total')
            ->take(10)
            ->values()
            ->toArray();

        // Por tipo
        $tipoLabels = RhDesligamento::$tipoLabels ?? [];
        $porTipo = $desligamentos
            ->groupBy('tipo')
            ->map(fn ($items, $tipo) => [
                'tipo'   => $tipo,
                'label'  => $tipoLabels[$tipo] ?? $tipo,
                'total'  => $items->count(),
            ])
            ->sortByDesc('total')
            ->values()
            ->toArray();

        // Motivos da entrevista
        $motivosPrincipais = RhEntrevistaDesligamento::$motivosPrincipais ?? [];
        $entrevistasIds = $desligamentos->pluck('id');
        $motivos = RhEntrevistaDesligamento::whereIn('desligamento_id', $entrevistasIds)
            ->whereNotNull('motivo_principal')
            ->selectRaw('motivo_principal, count(*) as total')
            ->groupBy('motivo_principal')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($m) => [
                'motivo' => $m->motivo_principal,
                'label'  => $motivosPrincipais[$m->motivo_principal] ?? $m->motivo_principal,
                'total'  => $m->total,
            ])
            ->toArray();

        $entrevistasComSat = RhEntrevistaDesligamento::whereIn('desligamento_id', $entrevistasIds)
            ->where(function ($q) {
                $q->whereNotNull('satisfacao_gestao')
                  ->orWhereNotNull('satisfacao_cultura')
                  ->orWhereNotNull('satisfacao_remuneracao')
                  ->orWhereNotNull('satisfacao_crescimento')
                  ->orWhereNotNull('satisfacao_equilibrio');
            })
            ->get();
        $satisfacaoMedia = $entrevistasComSat->count() > 0
            ? round($entrevistasComSat->avg(fn ($e) => $e->nps_media), 1)
            : null;

        $recomendariaTotal = RhEntrevistaDesligamento::whereIn('desligamento_id', $entrevistasIds)
            ->whereNotNull('recomendaria_empresa')
            ->count();
        $recomendariaPos = RhEntrevistaDesligamento::whereIn('desligamento_id', $entrevistasIds)
            ->where('recomendaria_empresa', true)
            ->count();
        $recomendariaPct = $recomendariaTotal > 0 ? round(($recomendariaPos / $recomendariaTotal) * 100) : null;

        // Tendência mensal
        $tendencia = [];
        $cursor = $inicio->copy()->startOfMonth();
        while ($cursor->lte($fim)) {
            $mes = $cursor->copy();
            $count = $desligamentos->filter(
                fn ($d) => \Carbon\Carbon::parse($d->data_ultimo_dia)->format('Y-m') === $mes->format('Y-m')
            )->count();
            $tendencia[] = ['mes' => $mes->translatedFormat('M/y'), 'total' => $count];
            $cursor->addMonth();
        }

        return compact(
            'total', 'taxa', 'taxaMensal', 'meses',
            'custoRescisao', 'custoReposicao', 'custoTotal', 'salarioMedio',
            'porDepto', 'porCargo', 'porTipo', 'motivos', 'tendencia',
            'satisfacaoMedia', 'recomendariaPct',
            'inicio', 'fim'
        );
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ABA: DESLIGAMENTOS
    // ═══════════════════════════════════════════════════════════════════

    // Sub-abas
    public string $demSubAba = 'processos'; // processos | dashboard

    // Filtros
    public string $demSearch       = '';
    public string $demStatusFiltro = '';
    public string $demTipoFiltro   = '';

    // Modal criar/editar
    public bool   $demModal           = false;
    #[Locked]
    public ?int   $demEditId          = null;
    public ?int   $demUserId          = null;
    public string $demTipo            = 'sem_justa_causa';
    public string $demDataAviso       = '';
    public string $demDataUltimoDia   = '';
    public string $demAvisoPrevioTipo = 'trabalhado';
    public int    $demAvisoPrevioDias = 30;
    public string $demObservacoes     = '';

    // Drawer
    public bool   $demDrawer    = false;
    #[Locked]
    public ?int   $demDrawerId  = null;
    public string $demDrawerTab = 'processo';

    // Modal verbas
    public bool   $verbasModal          = false;
    #[Locked]
    public ?int   $verbasDesligId       = null;
    #[Locked]
    public string $vTipoDesligamento    = 'sem_justa_causa'; // derivado do desligamento
    public string $vSalarioBase         = '';
    public string $vDataAdmissao        = '';
    public string $vDataDemissao        = '';
    public string $vAvisoPrevioTipo     = 'indenizado';  // indenizado | trabalhado
    public int    $vAvisoPrevioDias     = 0;             // 0 = calcular proporcional
    public bool   $vTemFeriasVencidas   = false;
    public string $vSaldoFgts           = '';            // vazio = estimar automaticamente
    public int    $vNumeroDependentes   = 0;
    public string $vOutrosCreditos      = '';
    public string $vDescontos           = '';
    public string $vObservacoes         = '';
    public ?array $verbasCalculadas     = null;

    #[Computed]
    public function desligamentos()
    {
        return RhDesligamento::with(['funcionario.department', 'checklist', 'entrevista', 'verbas'])
            ->when($this->demSearch, fn($q) => $q->whereHas('funcionario', fn($u) =>
                $u->where('name', 'ilike', '%' . $this->demSearch . '%')))
            ->when($this->demStatusFiltro, fn($q) => $q->where('status', $this->demStatusFiltro))
            ->when($this->demTipoFiltro,   fn($q) => $q->where('tipo', $this->demTipoFiltro))
            ->latest()
            ->paginate(15, pageName: 'demPage');
    }

    #[Computed]
    public function demDrawerData(): ?RhDesligamento
    {
        if (!$this->demDrawerId) return null;
        return RhDesligamento::with([
            'funcionario.department', 'rhUser',
            'checklist.concluidoPor', 'entrevista', 'verbas',
        ])->find($this->demDrawerId);
    }

    #[Computed]
    public function demStats(): array
    {
        $emProcesso = RhDesligamento::where('status', 'em_processo')->count();
        $esteMes    = RhDesligamento::where('status', 'concluido')
            ->whereMonth('concluido_at', now()->month)
            ->whereYear('concluido_at', now()->year)->count();
        $totalFunc  = User::where('is_active', true)->count();
        $turnover   = $totalFunc > 0 ? round($esteMes / $totalFunc * 100, 1) : 0;

        $motivoTop = RhEntrevistaDesligamento::whereNotNull('respondido_at')
            ->whereNotNull('motivo_principal')
            ->select('motivo_principal', DB::raw('count(*) as total'))
            ->groupBy('motivo_principal')->orderByDesc('total')->first();

        $porTipo = RhDesligamento::select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')->pluck('total', 'tipo')->toArray();

        return compact('emProcesso', 'esteMes', 'turnover', 'motivoTop', 'porTipo');
    }

    #[Computed]
    public function funcionariosAtivos()
    {
        return User::where('is_active', true)->notAdmin()
            ->whereDoesntHave('desligamentos', fn($q) => $q->where('status', 'em_processo'))
            ->orderBy('name')->get(['id', 'name', 'email', 'position']);
    }

    #[Computed]
    public function demTurnoverData(): array
    {
        // Últimos 12 meses – desligamentos concluídos por mês
        $meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $dt = now()->subMonths($i);
            $meses[] = [
                'mes'   => $dt->translatedFormat('M/y'),
                'total' => RhDesligamento::where('status', 'concluido')
                    ->whereYear('concluido_at', $dt->year)
                    ->whereMonth('concluido_at', $dt->month)
                    ->count(),
            ];
        }

        // Por tipo (todos, não só concluídos)
        $porTipo = RhDesligamento::select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')->pluck('total', 'tipo')->toArray();

        // Motivos mais citados nas entrevistas
        $motivos = RhEntrevistaDesligamento::whereNotNull('respondido_at')
            ->whereNotNull('motivo_principal')
            ->select('motivo_principal', DB::raw('count(*) as total'))
            ->groupBy('motivo_principal')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn($r) => [
                'motivo' => \App\Models\RhEntrevistaDesligamento::$motivosPrincipais[$r->motivo_principal] ?? $r->motivo_principal,
                'total'  => $r->total,
            ])->toArray();

        // Taxas de recontratação
        $totalConc     = RhDesligamento::where('status', 'concluido')->count();
        $recontratavel = RhDesligamento::where('status', 'concluido')->where('recontratavel', true)->count();
        $naoRecontr    = RhDesligamento::where('status', 'concluido')->where('recontratavel', false)->count();

        return compact('meses', 'porTipo', 'motivos', 'totalConc', 'recontratavel', 'naoRecontr');
    }

    public function toggleAtivacaoFuncionario(int $userId): void
    {
        $this->requireRhOrAdmin();
        $user = User::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);
        unset($this->demDrawerData);
        $status = $user->is_active ? 'reativada' : 'desativada';
        $this->alertSuccess("Conta {$status}!", "Conta de {$user->name} foi {$status} com sucesso.");
    }

    public function openDemModal(?int $id = null): void
    {
        $this->demEditId = $id;
        if ($id) {
            $d = RhDesligamento::findOrFail($id);
            $this->demUserId          = $d->user_id;
            $this->demTipo            = $d->tipo;
            $this->demDataAviso       = $d->data_aviso?->format('Y-m-d') ?? '';
            $this->demDataUltimoDia   = $d->data_ultimo_dia?->format('Y-m-d') ?? '';
            $this->demAvisoPrevioTipo = $d->aviso_previo_tipo;
            $this->demAvisoPrevioDias = $d->aviso_previo_dias;
            $this->demObservacoes     = $d->observacoes ?? '';
        } else {
            $this->demUserId = null;
            $this->demTipo = 'sem_justa_causa';
            $this->demDataAviso = $this->demDataUltimoDia = $this->demObservacoes = '';
            $this->demAvisoPrevioTipo = 'trabalhado';
            $this->demAvisoPrevioDias = 30;
        }
        $this->demModal = true;
    }

    public function saveDem(): void
    {
        $this->requireRhOrAdmin();
        $this->validate([
            'demUserId'        => 'required|exists:users,id',
            'demTipo'          => 'required|in:voluntario,sem_justa_causa,com_justa_causa,acordo_mutuo,aposentadoria',
            'demDataUltimoDia' => 'required|date',
        ]);

        $data = [
            'user_id'           => $this->demUserId,
            'rh_user_id'        => Auth::id(),
            'tipo'              => $this->demTipo,
            'data_aviso'        => $this->demDataAviso ?: null,
            'data_ultimo_dia'   => $this->demDataUltimoDia,
            'aviso_previo_tipo' => $this->demAvisoPrevioTipo,
            'aviso_previo_dias' => $this->demAvisoPrevioDias,
            'observacoes'       => $this->demObservacoes ?: null,
        ];

        if ($this->demEditId) {
            RhDesligamento::where('id', $this->demEditId)->update($data);
            $msg = 'Desligamento atualizado com sucesso.';
        } else {
            RhDesligamento::create($data);
            $msg = 'Processo de desligamento iniciado.';
        }

        $this->demModal = false;
        $this->demEditId = null;
        unset($this->desligamentos, $this->demStats, $this->demTurnoverData);
        $this->alertSuccess('Desligamento', $msg);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ABA: SOLICITAÇÕES DE RH (Self-service)
    // ═══════════════════════════════════════════════════════════════════

    // ── Propriedades ──────────────────────────────────────────────────
    public string  $solStatus    = '';
    public string  $solTipo      = '';
    public string  $solBusca     = '';
    public string  $solInicio    = '';
    public string  $solFim       = '';

    public bool    $solModal     = false;
    public ?int    $solEditId    = null;
    public string  $solObsRh     = '';
    public string  $solPrazo     = '';
    public string  $solStatusEdit = 'pendente';
    public $solArquivo           = null;

    // ── Computed ──────────────────────────────────────────────────────
    #[Computed]
    public function solKpis(): array
    {
        $base = RhSolicitacao::query();
        return [
            'total'        => (clone $base)->count(),
            'pendentes'    => (clone $base)->where('status', 'pendente')->count(),
            'em_andamento' => (clone $base)->where('status', 'em_andamento')->count(),
            'concluidas'   => (clone $base)->where('status', 'concluida')->count(),
            'vencidas'     => (clone $base)->whereNotNull('prazo')
                ->where('prazo', '<', now()->toDateString())
                ->whereNotIn('status', ['concluida', 'cancelada'])->count(),
        ];
    }

    #[Computed]
    public function solicitacoes()
    {
        return RhSolicitacao::with(['user.department', 'rhUser'])
            ->when($this->solBusca,  fn($q) => $q->whereHas('user', fn($u) => $u->where('name', 'ilike', "%{$this->solBusca}%")))
            ->when($this->solTipo,   fn($q) => $q->where('tipo', $this->solTipo))
            ->when($this->solStatus, fn($q) => $q->where('status', $this->solStatus))
            ->when($this->solInicio, fn($q) => $q->whereDate('created_at', '>=', $this->solInicio))
            ->when($this->solFim,    fn($q) => $q->whereDate('created_at', '<=', $this->solFim))
            ->orderByRaw("CASE status WHEN 'pendente' THEN 0 WHEN 'em_andamento' THEN 1 WHEN 'concluida' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // ── Ações ─────────────────────────────────────────────────────────
    public function solAplicar(): void
    {
        unset($this->solicitacoes, $this->solKpis);
    }

    public function solLimpar(): void
    {
        $this->solStatus = $this->solTipo = $this->solBusca = $this->solInicio = $this->solFim = '';
        unset($this->solicitacoes, $this->solKpis);
    }

    public function temFiltrosSol(): bool
    {
        return (bool) ($this->solBusca || $this->solTipo || $this->solStatus || $this->solInicio || $this->solFim);
    }

    public function openSolModal(int $id): void
    {
        $this->solEditId    = $id;
        $sol                = RhSolicitacao::findOrFail($id);
        $this->solStatusEdit = $sol->status;
        $this->solPrazo     = $sol->prazo?->format('Y-m-d') ?? '';
        $this->solObsRh     = $sol->observacao_rh ?? '';
        $this->solArquivo   = null;
        $this->solModal     = true;
    }

    public function saveSol(): void
    {
        $this->requireRhOrAdmin();
        $this->validate([
            'solStatusEdit' => 'required|in:pendente,em_andamento,concluida,cancelada',
            'solPrazo'      => 'nullable|date',
            'solArquivo'    => 'nullable|file|max:10240',
        ]);

        $sol  = RhSolicitacao::findOrFail($this->solEditId);
        $data = [
            'status'        => $this->solStatusEdit,
            'prazo'         => $this->solPrazo ?: null,
            'observacao_rh' => $this->solObsRh ?: null,
            'rh_user_id'    => Auth::id(),
        ];

        if ($this->solStatusEdit === 'concluida' && !$sol->concluida_em) {
            $data['concluida_em'] = now();
        }

        if ($this->solArquivo) {
            $data['arquivo_url'] = $this->solArquivo->store('solicitacoes', 'public');
        }

        $sol->update($data);

        // Notifica o funcionário se o status mudou
        if ($sol->status !== $sol->getOriginal('status') || true) {
            $sol->refresh();
            $this->notificarUsuario(
                $sol->user,
                new \App\Notifications\RhSolicitacaoAtualizadaNotification($sol, Auth::user()->name)
            );
        }

        // Dispara evento para atualizar a tabela do funcionário em tempo real
        $this->dispatch('sol-status-atualizado')->to('pages.funcionario.solicitacoes');

        $this->solModal = false;
        unset($this->solicitacoes, $this->solKpis);
        $this->alertSuccess('Solicitação atualizada!', 'O funcionário foi notificado.');
    }

    public function solCancelar(int $id): void
    {
        $this->requireRhOrAdmin();
        $sol = RhSolicitacao::findOrFail($id);
        $sol->update(['status' => 'cancelada', 'rh_user_id' => Auth::id()]);

        $this->notificarUsuario(
            $sol->user,
            new \App\Notifications\RhSolicitacaoAtualizadaNotification($sol, Auth::user()->name)
        );

        $this->dispatch('sol-status-atualizado')->to('pages.funcionario.solicitacoes');

        unset($this->solicitacoes, $this->solKpis);
        $this->alertSuccess('Cancelada', 'A solicitação foi cancelada e o funcionário notificado.');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ABA: MAPA DE COMPETÊNCIAS (Skills Matrix)
    // ═══════════════════════════════════════════════════════════════════

    public string $mapaDept = '';
    public string $mapaAno  = '';

    public function mapaAplicar(): void
    {
        unset($this->mapaCompetencias);
    }

    #[Computed]
    public function mapaCompetencias(): array
    {
        $ano = $this->mapaAno ?: now()->year;

        // Planos aprovados/concluídos do ano, com goals e usuário
        $plans = DpiPlan::with(['user.department', 'goals'])
            ->whereIn('status', ['aprovado', 'concluido'])
            ->where('year', $ano)
            ->whereHas('user', fn($q) => $q->where('is_active', true)->notAdmin())
            ->when($this->mapaDept, fn($q) => $q->whereHas('user',
                fn($u) => $u->where('department_id', $this->mapaDept)
            ))
            ->get();

        // Competências únicas (normalizadas) de todos os planos
        $competencias = $plans
            ->flatMap(fn($p) => $p->goals->pluck('name'))
            ->map(fn($n) => trim($n))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Agrupa funcionários por departamento
        $depts = [];
        foreach ($plans as $plan) {
            $user = $plan->user;
            if (!$user || !$user->department) continue;

            $deptId   = $user->department_id;
            $deptNome = $user->department->name;

            if (!isset($depts[$deptId])) {
                $depts[$deptId] = [
                    'nome'         => $deptNome,
                    'id'           => $deptId,
                    'funcionarios' => [],
                ];
            }

            $niveis = [];
            foreach ($plan->goals as $goal) {
                $niveis[trim($goal->name)] = [
                    'atual' => $goal->nivel_atual,
                    'meta'  => $goal->nivel_meta,
                    'gap'   => max(0, $goal->nivel_meta - $goal->nivel_atual),
                ];
            }

            $depts[$deptId]['funcionarios'][] = [
                'user_id'  => $user->id,
                'nome'     => $user->name,
                'position' => $user->position ?? '',
                'plan_id'  => $plan->id,
                'niveis'   => $niveis,
            ];
        }

        // Ordena departamentos por nome
        uasort($depts, fn($a, $b) => strcmp($a['nome'], $b['nome']));

        // Calcula gap por competência (para o ranking lateral)
        $gapPorComp = [];
        foreach ($competencias as $c) {
            $gapTotal = 0;
            $afetados = 0;
            $total    = 0;
            foreach ($depts as $dept) {
                foreach ($dept['funcionarios'] as $func) {
                    if (isset($func['niveis'][$c])) {
                        $g = $func['niveis'][$c]['gap'];
                        $gapTotal += $g;
                        $total++;
                        if ($g > 0) $afetados++;
                    }
                }
            }
            if ($total > 0) {
                $gapPorComp[] = [
                    'name'      => $c,
                    'gap_total' => $gapTotal,
                    'afetados'  => $afetados,
                    'cobertura' => $total,
                    'media'     => round($gapTotal / $total, 1),
                ];
            }
        }
        usort($gapPorComp, fn($a, $b) => $b['gap_total'] <=> $a['gap_total']);

        // KPIs globais
        $totalFuncionarios = User::where('is_active', true)
            ->notAdmin()
            ->when($this->mapaDept, fn($q) => $q->where('department_id', $this->mapaDept))
            ->count();
        $comDpi    = $plans->count();
        $totalGaps = (int) array_sum(array_column($gapPorComp, 'gap_total'));
        $criticos  = count(array_filter($gapPorComp, fn($g) => $g['media'] >= 3));

        return [
            'competencias' => $competencias,
            'departamentos' => array_values($depts),
            'topGaps'      => array_slice($gapPorComp, 0, 8),
            'kpis'         => [
                'total_funcionarios' => $totalFuncionarios,
                'com_dpi'            => $comDpi,
                'cobertura_pct'      => $totalFuncionarios > 0
                    ? round($comDpi / $totalFuncionarios * 100) : 0,
                'total_gaps'         => $totalGaps,
                'criticos'           => $criticos,
            ],
            'ano'          => $ano,
        ];
    }


    // ── People Analytics ──────────────────────────────────────────────
    public string $paAno  = '';
    public string $paDept = '';

    public function paAplicar(): void { unset($this->peopleAnalytics); }
    public function paLimpar(): void  { $this->paAno = ''; $this->paDept = ''; unset($this->peopleAnalytics); }

    #[Computed]
    public function peopleAnalytics(): array
    {
        $dept   = $this->paDept ?: null;
        $inicio = now()->startOfMonth()->subMonths(11);
        $fim    = now()->endOfMonth();

        // Mapa de chaves numéricas → labels de exibição: '2025-06' → 'Jun/25'
        $meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->startOfMonth()->subMonths($i);
            $meses[$m->format('Y-m')] = $m->format('M/y');
        }
        $labels = array_values($meses); // para o JS

        // ── HEADCOUNT ─────────────────────────────────────────────────
        $currentHC = User::where('is_active', true)
            ->notAdmin()
            ->when($dept, fn($q) => $q->where('department_id', $dept))
            ->count();

        // Entradas por mês (contagem cumulativa)
        $usersPerMonth = User::notAdmin()
            ->when($dept, fn($q) => $q->where('department_id', $dept))
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as ym, count(*) as cnt")
            ->groupByRaw("TO_CHAR(created_at, 'YYYY-MM')")
            ->pluck('cnt', 'ym')
            ->toArray();

        // Desligamentos por mês (cumulativa e turnover mensal)
        $desligQb = DB::table('rh_desligamentos')
            ->whereNotNull('data_ultimo_dia');
        if ($dept) {
            $desligQb->whereExists(fn($sub) =>
                $sub->from('users')
                    ->whereColumn('users.id', 'rh_desligamentos.user_id')
                    ->where('users.department_id', $dept)
            );
        }
        $desligPerMonth = (clone $desligQb)
            ->selectRaw("TO_CHAR(data_ultimo_dia, 'YYYY-MM') as ym, count(*) as cnt")
            ->groupByRaw("TO_CHAR(data_ultimo_dia, 'YYYY-MM')")
            ->pluck('cnt', 'ym')
            ->toArray();

        // Headcount cumulativo por mês (2 queries acima substituem 24)
        $headcountByKey = [];
        foreach (array_keys($meses) as $key) {
            $cumUsers  = array_sum(array_filter($usersPerMonth,  fn($v, $k) => $k <= $key, ARRAY_FILTER_USE_BOTH));
            $cumDeslig = array_sum(array_filter($desligPerMonth, fn($v, $k) => $k <= $key, ARRAY_FILTER_USE_BOTH));
            $headcountByKey[$key] = max(0, $cumUsers - $cumDeslig);
        }
        $headcountTrend = array_combine($labels, array_values($headcountByKey));

        // ── TURNOVER ──────────────────────────────────────────────────
        $turnoverTrend  = [];
        $totalSaidas12m = 0;
        // Desligamentos no período dos 12 meses (subset do desligPerMonth)
        foreach ($meses as $key => $label) {
            $saidas = $desligPerMonth[$key] ?? 0;
            $hc = max(1, $headcountByKey[$key] ?? $currentHC);
            $turnoverTrend[$label] = round($saidas / $hc * 100, 1);
            $totalSaidas12m += $saidas;
        }
        $turnoverAnual = $currentHC > 0 ? round($totalSaidas12m / $currentHC * 100, 1) : 0;

        $turnoverByTipo = (clone $desligQb)
            ->whereBetween('data_ultimo_dia', [$inicio, $fim])
            ->selectRaw('tipo, count(*) as total')
            ->groupBy('tipo')
            ->pluck('total', 'tipo')
            ->toArray();

        // ── HUMOR (1 query com GROUP BY) ──────────────────────────────
        $moodPeso  = ['otimo' => 100, 'bem' => 75, 'normal' => 50, 'pessimo' => 25];
        $humorRaw  = MoodCheckin::when($dept, fn($q) => $q->whereHas('user', fn($u) => $u->where('department_id', $dept)))
            ->whereBetween('checkin_date', [$inicio, $fim])
            ->selectRaw("TO_CHAR(checkin_date, 'YYYY-MM') as ym, mood, count(*) as cnt")
            ->groupByRaw("TO_CHAR(checkin_date, 'YYYY-MM'), mood")
            ->get()
            ->groupBy('ym');

        $humorTrend = [];
        foreach ($meses as $key => $label) {
            $rows = $humorRaw->get($key, collect());
            $tot  = $rows->sum('cnt');
            if ($tot > 0) {
                $score = $rows->reduce(fn($c, $r) => $c + ($moodPeso[$r->mood] ?? 50) * $r->cnt, 0);
                $humorTrend[$label] = round($score / $tot);
            } else {
                $humorTrend[$label] = null;
            }
        }
        $humorAtual = collect($humorTrend)->whereNotNull()->last() ?? 0;

        // ── eNPS (1 query com GROUP BY) ───────────────────────────────
        $npsQb = DB::table('survey_answers as sa')
            ->join('survey_questions as sq', 'sq.id', '=', 'sa.question_id')
            ->join('survey_responses as sr', 'sr.id', '=', 'sa.response_id')
            ->where('sq.type', 'escala')
            ->whereNotNull('sa.value_scale')
            ->whereBetween('sa.value_scale', [0, 10])
            ->whereBetween('sr.created_at', [$inicio, $fim]);
        if ($dept) {
            $npsQb->join('users as u_nps', 'u_nps.id', '=', 'sr.user_id')
                  ->where('u_nps.department_id', $dept);
        }
        $npsRaw = $npsQb
            ->selectRaw("TO_CHAR(sr.created_at, 'YYYY-MM') as ym, sa.value_scale as val")
            ->get()
            ->groupBy('ym');

        $npsTrend = [];
        foreach ($meses as $key => $label) {
            $vals = $npsRaw->get($key);
            if (!$vals || $vals->isEmpty()) {
                $npsTrend[$label] = null;
            } else {
                $t = $vals->count();
                $p = $vals->filter(fn($r) => $r->val >= 9)->count();
                $d = $vals->filter(fn($r) => $r->val <= 6)->count();
                $npsTrend[$label] = $t > 0 ? round(($p - $d) / $t * 100) : null;
            }
        }
        $npsAtual = collect($npsTrend)->whereNotNull()->last();

        // ── PERFORMANCE DPI (1 query com GROUP BY) ────────────────────
        $perfRaw = DB::table('dpi_goals as g')
            ->join('dpi_plans as p', 'p.id', '=', 'g.dpi_plan_id')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->where('u.is_active', true)
            ->when($dept, fn($q) => $q->where('u.department_id', $dept))
            ->whereBetween('p.updated_at', [$inicio, $fim])
            ->where('g.nivel_meta', '>', 0)
            ->selectRaw("TO_CHAR(p.updated_at, 'YYYY-MM') as ym,
                AVG(CAST(g.nivel_atual AS FLOAT) / NULLIF(CAST(g.nivel_meta AS FLOAT), 0) * 100) as avg_perf")
            ->groupByRaw("TO_CHAR(p.updated_at, 'YYYY-MM')")
            ->pluck('avg_perf', 'ym')
            ->toArray();

        $perfTrend = [];
        foreach ($meses as $key => $label) {
            $perfTrend[$label] = isset($perfRaw[$key]) && $perfRaw[$key] !== null
                ? round((float) $perfRaw[$key], 1)
                : null;
        }
        $dpiScore = collect($perfTrend)->whereNotNull()->last() ?? 0;

        // ── OKR + AVALIAÇÃO ───────────────────────────────────────────
        $okrProgress = DB::table('okr_key_results as kr')
            ->join('okr_objectives as obj', 'obj.id', '=', 'kr.objective_id')
            ->join('okr_cycles as c', 'c.id', '=', 'obj.cycle_id')
            ->whereNull('kr.deleted_at')
            ->whereNull('obj.deleted_at')
            ->where('c.status', 'ativo')
            ->avg('kr.progress');

        $evalScore = ManagerEvaluation::where('status', 'concluida')
            ->whereNotNull('final_score')
            ->avg('final_score');

        $perfIndex = round(
            ($dpiScore * 0.5) +
            (($okrProgress ?: 0) * 0.3) +
            (($evalScore ? min((float) $evalScore / 5 * 100, 100) : 0) * 0.2),
            1
        );

        // ── DEPT BREAKDOWN (1 query) ──────────────────────────────────
        $deptBreakdown = DB::table('users as u')
            ->join('departments as d', 'd.id', '=', 'u.department_id')
            ->where('u.is_active', true)
            ->whereNotNull('u.department_id')
            ->selectRaw('d.id, d.name, count(*) as headcount')
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('headcount')
            ->get()
            ->map(fn($r) => ['id' => $r->id, 'name' => $r->name, 'headcount' => $r->headcount])
            ->toArray();

        // ── PROJEÇÃO headcount ────────────────────────────────────────
        $hcVals = array_values($headcountByKey);
        $projMonths = [];
        if (count($hcVals) >= 3) {
            $deltas = [];
            for ($i = max(0, count($hcVals) - 4); $i < count($hcVals) - 1; $i++) {
                $deltas[] = $hcVals[$i + 1] - $hcVals[$i];
            }
            $avgDelta = count($deltas) ? array_sum($deltas) / count($deltas) : 0;
            $last = end($hcVals);
            for ($i = 1; $i <= 3; $i++) {
                $projMonths[] = [
                    'label' => now()->startOfMonth()->addMonths($i)->format('M/y'),
                    'value' => max(0, round($last + $avgDelta * $i)),
                ];
            }
        }

        // ── INSIGHTS ──────────────────────────────────────────────────
        $insights = [];
        if ($turnoverAnual > 15) {
            $insights[] = ['type' => 'danger',  'icon' => 'trending-up',    'text' => "Turnover crítico de {$turnoverAnual}% nos últimos 12 meses — acima de 15%!"];
        } elseif ($turnoverAnual > 8) {
            $insights[] = ['type' => 'warning', 'icon' => 'alert-triangle', 'text' => "Turnover de {$turnoverAnual}% — acima da média saudável de 8%."];
        } else {
            $insights[] = ['type' => 'success', 'icon' => 'check-circle',   'text' => "Turnover de {$turnoverAnual}% — dentro da faixa saudável."];
        }
        if ($humorAtual < 50) {
            $insights[] = ['type' => 'danger',  'icon' => 'frown',          'text' => "Humor organizacional abaixo de 50/100 — atenção ao bem-estar."];
        } elseif ($humorAtual < 70) {
            $insights[] = ['type' => 'warning', 'icon' => 'meh',            'text' => "Humor em {$humorAtual}/100 — há espaço de melhora."];
        } else {
            $insights[] = ['type' => 'success', 'icon' => 'smile',          'text' => "Humor saudável em {$humorAtual}/100."];
        }
        if ($npsAtual !== null) {
            if ($npsAtual < 0) {
                $insights[] = ['type' => 'danger',  'icon' => 'trending-down', 'text' => "eNPS negativo ({$npsAtual}): mais detratores do que promotores."];
            } elseif ($npsAtual < 30) {
                $insights[] = ['type' => 'warning', 'icon' => 'bar-chart-2',   'text' => "eNPS de {$npsAtual} — engajamento a melhorar (meta ≥ 30)."];
            } else {
                $insights[] = ['type' => 'success', 'icon' => 'trending-up',   'text' => "eNPS de {$npsAtual} — bom nível de engajamento!"];
            }
        }

        return [
            'headcount'      => $currentHC,
            'headcountTrend' => $headcountTrend,
            'turnoverAnual'  => $turnoverAnual,
            'turnoverTrend'  => $turnoverTrend,
            'turnoverByTipo' => $turnoverByTipo,
            'totalSaidas'    => $totalSaidas12m,
            'humorAtual'     => $humorAtual,
            'humorTrend'     => $humorTrend,
            'npsAtual'       => $npsAtual,
            'npsTrend'       => $npsTrend,
            'dpiScore'       => $dpiScore,
            'okrProgress'    => $okrProgress !== null ? round((float) $okrProgress, 1) : null,
            'evalScore'      => $evalScore   !== null ? round((float) $evalScore, 2)   : null,
            'perfIndex'      => $perfIndex,
            'perfTrend'      => $perfTrend,
            'deptBreakdown'  => $deptBreakdown,
            'projMonths'     => $projMonths,
            'insights'       => $insights,
            'labels'         => $labels,
        ];
    }
    #[Computed]
    public function departamentos()
    {
        return Department::orderBy('name')->get(['id', 'name']);
    }
}
