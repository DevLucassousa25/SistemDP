<?php

namespace App\Livewire\Pages\Calendario;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Livewire\SecureComponent;
use App\Models\CalendarioEvento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('Calendário Corporativo')]
class Index extends SecureComponent
{
    use EnviaNotificacoes;

    // ── Navegação do calendário ────────────────────────────────────────
    #[Url]
    public int $ano = 0;
    #[Url]
    public int $mes = 0;

    public string $diaSelecionado = '';

    // ── Modal de evento ────────────────────────────────────────────────
    public bool $showModal  = false;
    public bool $isEditing  = false;

    #[Locked]
    public ?int $editingId  = null;

    // Campos do formulário
    public string  $formData       = '';
    public string  $formTitulo     = '';
    public string  $formTipo       = 'evento_empresa';
    public string  $formDescricao  = '';
    public string  $formCor        = '#3B82F6';
    public bool    $formRecorrente = false;

    // ── Boot ──────────────────────────────────────────────────────────

    public function mount(): void
    {
        $hoje = Carbon::today();
        $this->ano = $this->ano ?: $hoje->year;
        $this->mes = $this->mes ?: $hoje->month;
        $this->diaSelecionado = $hoje->toDateString();
    }

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function isRh(): bool
    {
        return Auth::user()?->isRhOuDp() ?? false;
    }

    #[Computed]
    public function primeiroDiaMes(): Carbon
    {
        return Carbon::create($this->ano, $this->mes, 1);
    }

    #[Computed]
    public function totalDiasMes(): int
    {
        return $this->primeiroDiaMes->daysInMonth;
    }

    /**
     * Dia da semana do 1º dia (0=dom … 6=sáb, ajustado para seg=0).
     */
    #[Computed]
    public function offsetInicio(): int
    {
        // dayOfWeek: 0=dom,1=seg,...6=sáb → queremos seg=0
        $dow = $this->primeiroDiaMes->dayOfWeek; // domingo=0
        return ($dow + 6) % 7; // seg=0, ter=1, ... dom=6
    }

    #[Computed]
    public function mapaEventos(): array
    {
        return CalendarioEvento::mapDoMes($this->ano, $this->mes);
    }

    #[Computed]
    public function eventosDiaSelecionado(): \Illuminate\Database\Eloquent\Collection
    {
        if (!$this->diaSelecionado) {
            return collect();
        }
        return CalendarioEvento::naData($this->diaSelecionado)->orderBy('tipo')->get();
    }

    #[Computed]
    public function labelMes(): string
    {
        return ucfirst(
            Carbon::create($this->ano, $this->mes, 1)
                ->locale('pt_BR')
                ->isoFormat('MMMM [de] YYYY')
        );
    }

    // ── Navegação ─────────────────────────────────────────────────────

    public function mesAnterior(): void
    {
        $d = Carbon::create($this->ano, $this->mes, 1)->subMonth();
        $this->ano = $d->year;
        $this->mes = $d->month;
        unset($this->mapaEventos, $this->primeiroDiaMes, $this->totalDiasMes, $this->offsetInicio, $this->labelMes);
    }

    public function mesProximo(): void
    {
        $d = Carbon::create($this->ano, $this->mes, 1)->addMonth();
        $this->ano = $d->year;
        $this->mes = $d->month;
        unset($this->mapaEventos, $this->primeiroDiaMes, $this->totalDiasMes, $this->offsetInicio, $this->labelMes);
    }

    public function irHoje(): void
    {
        $hoje = Carbon::today();
        $this->ano = $hoje->year;
        $this->mes = $hoje->month;
        $this->diaSelecionado = $hoje->toDateString();
        unset($this->mapaEventos, $this->primeiroDiaMes, $this->totalDiasMes, $this->offsetInicio, $this->labelMes, $this->eventosDiaSelecionado);
    }

    public function selecionarDia(string $data): void
    {
        $this->diaSelecionado = $data;
        unset($this->eventosDiaSelecionado);
    }

    // ── CRUD de Eventos ───────────────────────────────────────────────

    public function abrirModal(?string $data = null): void
    {
        $this->requireRhOrAdmin();

        $this->resetModal();
        $this->formData = $data ?? $this->diaSelecionado ?? Carbon::today()->toDateString();
        $this->showModal = true;
    }

    public function editarEvento(int $id): void
    {
        $this->requireRhOrAdmin();

        $evento = CalendarioEvento::findOrFail($id);
        $this->editingId      = $id;
        $this->isEditing      = true;
        $this->formData       = $evento->data->toDateString();
        $this->formTitulo     = $evento->titulo;
        $this->formTipo       = $evento->tipo;
        $this->formDescricao  = $evento->descricao ?? '';
        $this->formCor        = $evento->cor;
        $this->formRecorrente = $evento->recorrente_anual;
        $this->showModal      = true;
    }

    public function salvarEvento(): void
    {
        $this->requireRhOrAdmin();

        $dados = $this->validate([
            'formData'       => 'required|date',
            'formTitulo'     => 'required|string|max:120',
            'formTipo'       => 'required|in:feriado_nacional,data_comemorativa,evento_empresa',
            'formDescricao'  => 'nullable|string|max:500',
            'formCor'        => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'formRecorrente' => 'boolean',
        ]);

        $payload = [
            'data'             => $dados['formData'],
            'titulo'           => $this->sanitize($dados['formTitulo']),
            'tipo'             => $dados['formTipo'],
            'descricao'        => $this->sanitize($dados['formDescricao'] ?? ''),
            'cor'              => $dados['formCor'],
            'recorrente_anual' => $dados['formRecorrente'],
            'created_by'       => Auth::id(),
        ];

        if ($this->isEditing && $this->editingId) {
            CalendarioEvento::findOrFail($this->editingId)->update($payload);
            $this->toastNotif('Evento atualizado', 'O evento foi atualizado com sucesso.', 'calendar-check', 'blue');
        } else {
            CalendarioEvento::create($payload);
            $this->toastNotif('Evento criado', 'O evento foi criado com sucesso.', 'calendar-plus', 'blue');
        }

        $this->diaSelecionado = $dados['formData'];
        $this->ano = (int) substr($dados['formData'], 0, 4);
        $this->mes = (int) substr($dados['formData'], 5, 2);

        $this->resetModal();
        unset($this->mapaEventos, $this->eventosDiaSelecionado);
    }

    public function excluirEvento(int $id): void
    {
        $this->requireRhOrAdmin();
        CalendarioEvento::findOrFail($id)->delete();
        $this->toastNotif('Evento removido', 'O evento foi excluído com sucesso.', 'trash-2', 'red');
        unset($this->mapaEventos, $this->eventosDiaSelecionado);
    }

    public function fecharModal(): void
    {
        $this->resetModal();
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function resetModal(): void
    {
        $this->showModal      = false;
        $this->isEditing      = false;
        $this->editingId      = null;
        $this->formData       = '';
        $this->formTitulo     = '';
        $this->formTipo       = 'evento_empresa';
        $this->formDescricao  = '';
        $this->formCor        = '#3B82F6';
        $this->formRecorrente = false;
        $this->resetValidation();
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.pages.calendario.index');
    }
}
