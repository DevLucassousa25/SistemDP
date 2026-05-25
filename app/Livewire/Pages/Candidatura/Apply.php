<?php

namespace App\Livewire\Pages\Candidatura;

use App\Jobs\ExtrairTextoOcr;
use App\Livewire\Concerns\EnviaNotificacoes;
use App\Notifications\NovoCurriculoNotification;
use App\Models\RhCurriculo;
use App\Models\RhCandidatura;
use App\Models\RhVaga;
use App\Models\RhVagaEtapa;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.guest')]
#[Title('Candidatura — PeopleHub')]
class Apply extends Component
{
    use WithFileUploads, EnviaNotificacoes;

    // Passo atual: 1 = dados pessoais, 2 = perfil profissional, 3 = upload + vaga
    public int $step = 1;
    public bool $enviado = false;

    // ── Passo 1: Dados pessoais ───────────────────────────────────────
    public string $nome        = '';
    public string $email       = '';
    public string $telefone    = '';
    public string $cidade      = '';
    public string $estado      = '';
    public string $dataNasc    = '';

    // ── Passo 2: Perfil profissional ──────────────────────────────────
    public string $escolaridade      = '';
    public string $areaInteresse     = '';
    public string $pretensaoSalarial = '';
    public string $resumo            = '';

    // ── Passo 3: Vaga + CV ────────────────────────────────────────────
    public ?int $vagaId = null;
    public $arquivo = null;

    // Validações por passo
    protected function rules(): array
    {
        return match ($this->step) {
            1 => [
                'nome'     => 'required|string|min:3|max:200',
                'email'    => 'required|email|max:200',
                'telefone' => 'nullable|string|max:20',
                'cidade'   => 'nullable|string|max:100',
                'estado'   => 'nullable|string|max:2',
                'dataNasc' => 'nullable|date',
            ],
            2 => [
                'escolaridade'      => 'nullable|in:fundamental,medio,tecnico,graduacao,pos_graduacao,mestrado,doutorado',
                'areaInteresse'     => 'nullable|string|max:200',
                'pretensaoSalarial' => 'nullable|numeric|min:0',
                'resumo'            => 'nullable|string|max:3000',
            ],
            3 => [
                'vagaId'  => 'nullable|exists:rh_vagas,id',
                'arquivo' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ],
            default => [],
        };
    }

    protected function messages(): array
    {
        return [
            'nome.required'  => 'O nome é obrigatório.',
            'nome.min'       => 'Nome muito curto.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email'    => 'Informe um e-mail válido.',
            'arquivo.mimes'  => 'O arquivo deve ser PDF, DOC ou DOCX.',
            'arquivo.max'    => 'O arquivo não pode ter mais de 5 MB.',
        ];
    }

    #[Computed]
    public function vagasPublicadas()
    {
        return RhVaga::where('status', 'publicada')
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'cargo', 'modalidade', 'cidade', 'estado', 'salario_min', 'salario_max']);
    }

    #[Computed]
    public function vagaSelecionada(): ?RhVaga
    {
        if (!$this->vagaId) return null;
        return RhVaga::find($this->vagaId);
    }

    public function proximoPasso(): void
    {
        $this->validate();
        $this->step++;
        $this->resetErrorBag();
    }

    public function voltarPasso(): void
    {
        $this->step = max(1, $this->step - 1);
        $this->resetErrorBag();
    }

    public function enviar(): void
    {
        $this->validate();

        // Salvar arquivo
        $arquivoPath  = null;
        $arquivoOrig  = null;
        $arquivoMime  = null;

        if ($this->arquivo) {
            $arquivoOrig = $this->arquivo->getClientOriginalName();
            $arquivoMime = $this->arquivo->getMimeType();
            $arquivoPath = $this->arquivo->store('curriculos', 'public');
        }

        // Criar currículo
        $curriculo = RhCurriculo::create([
            'nome'                => $this->nome,
            'email'               => $this->email,
            'telefone'            => $this->telefone ?: null,
            'cidade'              => $this->cidade ?: null,
            'estado'              => $this->estado ?: null,
            'data_nascimento'     => $this->dataNasc ?: null,
            'escolaridade'        => $this->escolaridade ?: null,
            'area_interesse'      => $this->areaInteresse ?: null,
            'pretensao_salarial'  => $this->pretensaoSalarial ?: null,
            'resumo_profissional' => $this->resumo ?: null,
            'arquivo_path'        => $arquivoPath,
            'arquivo_original'    => $arquivoOrig,
            'arquivo_mime'        => $arquivoMime,
            'status'              => 'ativo',
        ]);

        // Vincular à vaga (se selecionada)
        if ($this->vagaId) {
            $primeiraEtapa = RhVagaEtapa::where('vaga_id', $this->vagaId)
                ->orderBy('ordem')->first();

            // Evitar candidatura duplicada
            RhCandidatura::firstOrCreate(
                ['curriculo_id' => $curriculo->id, 'vaga_id' => $this->vagaId],
                ['etapa_id' => $primeiraEtapa?->id, 'status' => 'ativo']
            );
        }

        // Extrai texto OCR do arquivo em background
        if ($curriculo->arquivo_path) {
            ExtrairTextoOcr::dispatch($curriculo->id);
        }

        // Notifica RH/Admin sobre novo currículo (sem toast — página pública)
        $this->notificarRhAdmin(new NovoCurriculoNotification($curriculo));

        $this->enviado = true;
    }

    public function render()
    {
        return view('livewire.pages.candidatura.apply');
    }
}
