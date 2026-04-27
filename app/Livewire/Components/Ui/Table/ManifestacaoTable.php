<?php

namespace App\Livewire\Components\Ui\Table;

use App\Actions\AutoEncerrarOuvidoriasAction;
use App\Livewire\SecureComponent;
use App\Models\ConfiguracaoOuvidoria;
use App\Models\Manifestacao;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ManifestacaoTable extends SecureComponent
{
    use WithPagination;

    // #[Url] substitui $queryString: cada filtro é rastreado na URL
    // sem interferir com o "page" interno do WithPagination (Livewire 3).
    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $categoria = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $anonimo = ''; // '' = todos | '1' = anônimos | '0' = identificados

    #[Url(except: '')]
    public string $vencimento = ''; // '' | 'proximos' | 'atencao' | 'critico'

    public function mount(): void
    {
        // Verificação lazy: encerra ouvidorias vencidas sem precisar de cron.
        // Executa no máximo uma vez por hora; erros são silenciados para não
        // quebrar a listagem caso a tabela de config ainda não exista.
        try {
            (new AutoEncerrarOuvidoriasAction())->executeIfDue();
        } catch (\Throwable) {
            // silently ignore
        }
    }

    protected $listeners = [
        'manifestacaoCriada'     => 'refreshList',
        'manifestacaoAtualizada' => 'refreshList',
        'manifestacaoExcluida'   => 'refreshList',
    ];

    public function refreshList(): void      { $this->resetPage(); }
    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingCategoria(): void { $this->resetPage(); }
    public function updatingStatus(): void    { $this->resetPage(); }
    public function updatingAnonimo(): void   { $this->resetPage(); }
    public function updatingVencimento(): void { $this->resetPage(); }

    public function limparFiltros(): void
    {
        $this->search     = '';
        $this->categoria  = '';
        $this->status     = '';
        $this->anonimo    = '';
        $this->vencimento = '';
        $this->resetPage();
    }

    /**
     * Navega para a página de detalhes da manifestação.
     * IDOR: verifica acesso antes de redirecionar.
     */
    public function ver(string $id): void
    {
        $user  = Auth::user();
        $query = Manifestacao::where('id', $id);

        if (! $user->isRhOuDp()) {
            $query->where('user_id', $user->id);
        }

        $query->firstOrFail(); // 404 se não existir, 404 se não tiver acesso

        $this->redirect(route('ouvidoria.details', $id), navigate: true);
    }

    public function render()
    {
        $user         = Auth::user();
        $slug         = $user->accessProfile?->slug;
        $podeVerTudo  = in_array($slug, ['administrator', 'hr']);
        $configGlobal = ConfiguracaoOuvidoria::instancia();

        $query = Manifestacao::with([
                'user',
                'respostas:id,manifestacao_id,created_at', // para calcular urgência por linha
            ])
            ->when(! $podeVerTudo, fn ($q) => $q->where('user_id', $user->id))
            ->when($this->search, fn ($q) => $q->where(function ($sub) {
                $sub->where('protocolo', 'like', "%{$this->search}%")
                    ->orWhere('assunto',   'like', "%{$this->search}%")
                    ->orWhere('descricao', 'like', "%{$this->search}%");
            }))
            ->when($this->categoria,      fn ($q) => $q->where('categoria',  $this->categoria))
            ->when($this->status,         fn ($q) => $q->where('status',     $this->status))
            ->when($this->anonimo !== '', fn ($q) => $q->where('is_anonimo', (bool) $this->anonimo));

        // ── Filtro por vencimento (RH/Admin + auto-encerramento ativo) ──────────
        $filtrandoVencimento = $this->vencimento !== ''
            && $podeVerTudo
            && $configGlobal->auto_encerramento_ativo;

        if ($filtrandoVencimento) {
            $prazo = $configGlobal->prazo_horas;

            // Última atividade: maior entre updated_at e última resposta (PostgreSQL)
            $lastActivity = "GREATEST(
                manifestacoes.updated_at::timestamp,
                COALESCE((
                    SELECT MAX(created_at)::timestamp
                    FROM respostas_manifestacaos
                    WHERE manifestacao_id = manifestacoes.id
                ), manifestacoes.updated_at::timestamp)
            )";

            $prazoMins      = "COALESCE(prazo_personalizado_horas, {$prazo}) * 60";
            $minsDecorridos = "EXTRACT(EPOCH FROM (NOW() - ({$lastActivity}))) / 60";
            $percentual     = "(({$minsDecorridos}) / NULLIF({$prazoMins}, 0) * 100)";

            // Só ouvidorias abertas com auto-encerramento habilitado
            $query->whereIn('status', [
                    Manifestacao::STATUS_EM_ANALISE,
                    Manifestacao::STATUS_EM_ANDAMENTO,
                ])
                ->where('auto_encerramento_desativado', false);

            if ($this->vencimento === 'critico') {
                $query->whereRaw("{$percentual} >= 80");
            } elseif ($this->vencimento === 'atencao') {
                $query->whereRaw("{$percentual} >= 50 AND {$percentual} < 80");
            }
            // 'proximos': sem filtro de %, apenas ordena por deadline mais próxima

            // Ordena pela deadline mais próxima primeiro
            $query->orderByRaw("
                ({$lastActivity}) + COALESCE(prazo_personalizado_horas, {$prazo}) * INTERVAL '1 hour' ASC
            ");
        } else {
            $query->latest();
        }

        $manifestacoes = $query->paginate(10);

        return view('livewire.components.ui.table.manifestacao-table', [
            'manifestacoes' => $manifestacoes,
            'podeVerTudo'   => $podeVerTudo,
            'configGlobal'  => $configGlobal,
        ]);
    }
}
