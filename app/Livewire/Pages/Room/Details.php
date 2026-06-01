<?php

namespace App\Livewire\Pages\Room;
use App\Livewire\SecureComponent;

use App\Models\Department;
use App\Models\Reservations;
use App\Models\room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class Details extends SecureComponent
{
    public room $sala;
    public int $reservasHoje = 0;
    public ?string $proximoHorario = null;

    public bool $modalReserva = false;
    public string $titulo = '';
    public string $descricao = '';
    public string $dataReserva = '';
    public string $horaInicio = '';
    public string $horaFim = '';

    /** IDs dos participantes selecionados (entangled com Alpine). */
    public array $participantesSelecionados = [];

    /** Itens da pauta da reunião (setados via Alpine antes de salvar). */
    public array $pautaItems = [];

    public bool $emUsoAgora = false;

    // ── Manutenção ──────────────────────────────────────────────
    public bool   $modalManutencao   = false;
    public string $manutencaoInicio  = '';
    public string $manutencaoFim     = '';

    // ── Cancelamento de reserva ──────────────────────────────────
    public bool  $modalCancelar        = false;
    public array $cancelarReservaDados = [];

    // #[Locked] impede que o cliente manipule o ID da reserva via JS/wire
    #[Locked]
    public ?int $cancelarReservaId = null;

    public function mount(int $id): void
    {
        $this->sala = Room::with(['images' => fn($q) => $q->orderBy('order')])
            ->findOrFail($id);

        // Só sincroniza automaticamente se a sala não estiver em manutenção ativa
        $emManutencaoAtiva = $this->sala->status === 'manutencao'
            && $this->sala->maintenance_end
            && $this->sala->maintenance_end->isFuture();

        if (! $emManutencaoAtiva) {
            $novoStatus = $this->computarStatusSala();

            if ($this->sala->status !== $novoStatus) {
                Room::where('id', $this->sala->id)->update(['status' => $novoStatus]);
                $this->sala->refresh();
            }
        }

        $this->carregarEstatisticas();

        $this->dataReserva = today()->format('Y-m-d');

        $this->dispatch('breadcrumb-set', items: [
            ['label' => 'Salas', 'icon' => 'door-open', 'url' => route('rooms')],
            ['label' => $this->sala->name, 'url' => null],
        ]);
    }

    #[On('room-updated')]
    public function recarregarSala(int $salaId): void
    {
        if ($this->sala->id !== $salaId) return;

        $this->sala = room::with(['images' => fn($q) => $q->orderBy('order')])->findOrFail($salaId);
        $this->carregarEstatisticas();
    }

    public function abrirModalReserva(): void
    {
        $this->resetForm();
        // Pré-seleciona o próprio usuário como participante
        $this->participantesSelecionados = [Auth::id()];
        $this->modalReserva = true;
    }

    public function fecharModalReserva(): void
    {
        $this->modalReserva = false;
        $this->resetForm();
    }


    public function alterarStatus(string $status): void
    {
        // Apenas RH/Admin pode alterar o status da sala
        if (! Auth::user()->isRhOuDp()) {
            abort(403, 'Sem permissão para alterar o status da sala.');
        }

        if ($status === 'manutencao') {
            $this->abrirModalManutencao();
            return;
        }

        // Cancela reservas de manutenção futuras ao sair do status
        if ($this->sala->status === 'manutencao') {
            $this->sala->reservations()
                ->where('is_maintenance', true)
                ->where('status', 'confirmada')
                ->where('end_time', '>', now())
                ->update(['status' => 'cancelada']);
        }

        $this->sala->update([
            'status'             => $status,
            'maintenance_start'  => null,
            'maintenance_end'    => null,
        ]);
        $this->sala->refresh();
        $this->dispatch('room-updated', salaId: $this->sala->id);
    }

    // ── Manutenção ──────────────────────────────────────────────

    public function abrirModalManutencao(): void
    {
        // Bloqueia se há reunião em andamento agora
        $emAndamento = $this->sala->reservations()
            ->where('status', 'confirmada')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->exists();

        if ($emAndamento) {
            $this->dispatch('openAlert',
                title: 'Ação bloqueada',
                description: 'Existe uma reunião em andamento agora. Aguarde o término para colocar a sala em manutenção.',
                type: 'error'
            );
            return;
        }

        // Bloqueia se há reuniões futuras agendadas
        $temFuturas = $this->sala->reservations()
            ->where('status', 'confirmada')
            ->where('start_time', '>', now())
            ->exists();

        if ($temFuturas) {
            $this->dispatch('openAlert',
                title: 'Ação bloqueada',
                description: 'Existem reuniões agendadas para esta sala. Cancele-as antes de definir a manutenção.',
                type: 'error'
            );
            return;
        }

        $this->manutencaoInicio = now()->format('Y-m-d\TH:i');
        $this->manutencaoFim    = '';
        $this->modalManutencao  = true;
    }

    public function fecharModalManutencao(): void
    {
        $this->modalManutencao  = false;
        $this->manutencaoInicio = '';
        $this->manutencaoFim    = '';
        $this->resetValidation(['manutencaoInicio', 'manutencaoFim']);
    }

    public function definirManutencao(): void
    {
        $this->validate([
            'manutencaoInicio' => 'required|date',
            'manutencaoFim'    => 'required|date|after:manutencaoInicio',
        ], [
            'manutencaoInicio.required' => 'Informe a data e hora de início.',
            'manutencaoInicio.date'     => 'Data de início inválida.',
            'manutencaoFim.required'    => 'Informe a data e hora de término.',
            'manutencaoFim.date'        => 'Data de término inválida.',
            'manutencaoFim.after'       => 'O término deve ser após o início.',
        ]);

        $this->sala->update([
            'status'            => 'manutencao',
            'maintenance_start' => $this->manutencaoInicio,
            'maintenance_end'   => $this->manutencaoFim,
        ]);

        // Cria reserva de manutenção para bloquear o período na agenda
        Reservations::create([
            'room_id'         => $this->sala->id,
            'user_id'         => Auth::id(),
            'title'           => 'Manutenção',
            'description'     => 'Sala indisponível para manutenção programada.',
            'start_time'      => $this->manutencaoInicio,
            'end_time'        => $this->manutencaoFim,
            'attendees_count' => 1,
            'status'          => 'confirmada',
            'is_maintenance'  => true,
        ]);

        $this->sala->refresh();
        $this->fecharModalManutencao();
        $this->dispatch('reservaCriada');
        $this->dispatch('room-updated', salaId: $this->sala->id);
        $this->dispatch('openAlert',
            title: 'Manutenção agendada',
            description: 'A sala foi marcada como em manutenção com sucesso.',
            type: 'success'
        );
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    protected function rules(): array
    {
        return [
            'titulo'                    => 'required|string|min:2|max:100',
            'descricao'                 => 'nullable|string|max:500',
            'dataReserva'               => 'required|date|after_or_equal:today',
            'horaInicio'                => 'required|date_format:H:i',
            'horaFim'                   => 'required|date_format:H:i|after:horaInicio',
            'participantesSelecionados' => 'required|array|min:1|max:' . $this->sala->capacity,
        ];
    }

    protected array $messages = [
        'titulo.required'                    => 'Informe o título da reunião.',
        'dataReserva.required'               => 'Informe a data da reserva.',
        'dataReserva.after_or_equal'         => 'A data não pode ser no passado.',
        'horaInicio.required'                => 'Informe o horário de início.',
        'horaFim.required'                   => 'Informe o horário de término.',
        'horaFim.after'                      => 'O término deve ser após o início.',
        'participantesSelecionados.required' => 'Selecione ao menos um participante.',
        'participantesSelecionados.min'      => 'Selecione ao menos um participante.',
        'participantesSelecionados.max'      => 'Excede a capacidade da sala (:max pessoas).',
    ];


    public function sincronizarStatus(): void
    {
        // Não sobrescreve manutenção ainda ativa
        if ($this->sala->status === 'manutencao') {
            if ($this->sala->maintenance_end && $this->sala->maintenance_end->isFuture()) {
                return;
            }

            // Manutenção expirou — limpa os campos e segue para sincronização normal
            $this->sala->update([
                'maintenance_start' => null,
                'maintenance_end'   => null,
            ]);
            $this->sala->refresh();
        }

        $novoStatus = $this->computarStatusSala();

        if ($this->sala->status !== $novoStatus) {
            $this->sala->update(['status' => $novoStatus]);
            $this->sala->refresh();
        }
    }

    /**
     * Calcula o status dinâmico da sala atual a partir das reservas confirmadas:
     *  - "ocupada"    → existe reserva acontecendo agora.
     *  - "reservada"  → existe reserva futura.
     *  - "disponivel" → nenhuma das anteriores.
     * "manutencao" não é retornado aqui; é tratado fora.
     */
    private function computarStatusSala(): string
    {
        $agora = now();

        $emUso = $this->sala->reservations()
            ->where('status', 'confirmada')
            ->where('start_time', '<=', $agora)
            ->where('end_time',   '>=', $agora)
            ->exists();

        if ($emUso) {
            return 'ocupada';
        }

        $temFutura = $this->sala->reservations()
            ->where('status', 'confirmada')
            ->where('start_time', '>', $agora)
            ->exists();

        return $temFutura ? 'reservada' : 'disponivel';
    }

    public function salvarReserva(): void
    {
        if ($this->sala->status === 'manutencao') {
            $this->fecharModalReserva();
            $this->dispatch('openAlert',
                title: 'Sala indisponível',
                description: 'Não é possível criar reservas enquanto a sala estiver em manutenção.',
                type: 'error'
            );
            return;
        }

        $this->validate();

        $start = Carbon::parse("{$this->dataReserva} {$this->horaInicio}");
        $end   = Carbon::parse("{$this->dataReserva} {$this->horaFim}");

        if (Reservations::temConflito($this->sala->id, $start, $end)) {
            $this->addError('horaInicio', 'Já existe uma reserva neste horário.');
            return;
        }

        Reservations::create([
            'room_id'         => $this->sala->id,
            'user_id'         => Auth::id(),
            'title'           => $this->titulo,
            'description'     => $this->descricao,
            'start_time'      => $start,
            'end_time'        => $end,
            'attendees_count' => count($this->participantesSelecionados),
            'status'          => 'confirmada',
        ]);

        // Recalcula o status (pode virar 'ocupada' ou 'reservada' dependendo do horário)
        $this->sala->refresh();
        $this->sala->update(['status' => $this->computarStatusSala()]);

        $this->fecharModalReserva();
        $this->carregarEstatisticas();
        $this->sala->refresh();
        $this->dispatch('reservaCriada');

        $this->dispatch(
            'openAlert',
            title: 'Reserva criada',
            description: 'Reserva criada com sucesso!',
            type: 'success'
        );
    }


    // Abre o dialog de confirmação (já faz todas as validações antes)
    public function confirmarCancelamento(int $reservationId): void
    {
        $reserva = Reservations::where('id', $reservationId)
            ->where('room_id', $this->sala->id)
            ->with('user')
            ->firstOrFail();

        if ($reserva->is_maintenance) {
            $this->dispatch('openAlert',
                title: 'Ação não permitida',
                description: 'Reservas de manutenção só podem ser removidas alterando o status da sala.',
                type: 'error'
            );
            return;
        }

        if ($reserva->end_time->isPast()) {
            $this->dispatch('openAlert',
                title: 'Ação não permitida',
                description: 'Não é possível cancelar reservas que já foram realizadas.',
                type: 'error'
            );
            return;
        }

        $usuario = Auth::user();
        if ($reserva->user_id !== $usuario->id && ! $usuario->isAdmin()) {
            $this->dispatch('openAlert',
                title: 'Sem permissão',
                description: 'Você só pode cancelar as suas próprias reservas.',
                type: 'error'
            );
            return;
        }

        $this->cancelarReservaId    = $reservationId;
        $this->cancelarReservaDados = [
            'titulo'      => $reserva->title,
            'data'        => $reserva->start_time->format('d/m/Y'),
            'horario'     => $reserva->horario,
            'responsavel' => $reserva->user->name,
            'participantes' => $reserva->attendees_count,
        ];
        $this->modalCancelar = true;
    }

    public function fecharModalCancelar(): void
    {
        $this->modalCancelar        = false;
        $this->cancelarReservaId    = null;
        $this->cancelarReservaDados = [];
    }

    // Executa o cancelamento após confirmação no dialog
    public function cancelarReserva(): void
    {
        if (! $this->cancelarReservaId) return;

        $reserva = Reservations::where('id', $this->cancelarReservaId)
            ->where('room_id', $this->sala->id)
            ->firstOrFail();

        // Re-valida no servidor (defesa em profundidade)
        if ($reserva->is_maintenance || $reserva->end_time->isPast()) {
            $this->fecharModalCancelar();
            return;
        }

        $usuario = Auth::user();
        if ($reserva->user_id !== $usuario->id && ! $usuario->isAdmin()) {
            $this->fecharModalCancelar();
            return;
        }

        $reserva->update(['status' => 'cancelada']);

        // Recalcula: pode continuar reservada por outra reunião futura ou ocupada pela atual
        $this->sala->refresh();
        if ($this->sala->status !== 'manutencao') {
            $this->sala->update(['status' => $this->computarStatusSala()]);
        }

        $this->fecharModalCancelar();
        $this->carregarEstatisticas();
        $this->sala->refresh();
        $this->dispatch('reservaCriada');
        $this->dispatch('openAlert',
            title: 'Reserva cancelada',
            description: 'A reserva foi cancelada com sucesso.',
            type: 'success'
        );
    }

    public function carregarEstatisticas(): void
    {
        $this->sincronizarStatus();

        $this->reservasHoje = $this->sala->reservations()
            ->whereDate('start_time', today())
            ->where('status', 'confirmada')
            ->count();

        $this->proximoHorario = $this->sala->proximoHorarioLivre();
    }

    private function resetForm(): void
    {
        $this->titulo                    = '';
        $this->descricao                 = '';
        $this->dataReserva               = today()->format('Y-m-d');
        $this->horaInicio                = '';
        $this->horaFim                   = '';
        $this->participantesSelecionados = [];
        $this->resetValidation();
    }

    public function render()
    {
        $reservasHojeList = $this->sala->reservations()
            ->with('user')
            ->whereDate('start_time', today())
            ->where('status', 'confirmada')
            ->orderBy('start_time')
            ->get();

        $historico = $this->sala->reservations()
            ->with('user')
            ->where('start_time', '<', now())
            ->orderByDesc('start_time')
            ->limit(20)
            ->get();

        // Dados para o picker de participantes
        $usuariosDisponiveis = User::where('is_active', true)
            ->with('department')
            ->orderBy('name')
            ->get()
            ->map(fn ($u) => [
                'id'              => $u->id,
                'name'            => $u->name,
                'initials'        => $u->initials(),
                'cor'             => $u->avatarColor(),
                'avatarUrl'       => $u->avatarUrl(),
                'department_id'   => $u->department_id,
                'department_name' => $u->department?->name ?? 'Sem departamento',
                'position'        => $u->position ?? 'Sem cargo',
            ])
            ->values()
            ->toArray();

        $departamentos = Department::has('users')
            ->orderBy('name')
            ->get()
            ->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])
            ->values()
            ->toArray();

        return view('livewire.pages.room.details', [
            'sala'                => $this->sala,
            'imagens'             => $this->sala->images,
            'imagemCapa'          => $this->sala->images->first(),
            'imagensGaleria'      => $this->sala->images->skip(1),
            'reservasHojeList'    => $reservasHojeList,
            'historico'           => $historico,
            'usuariosDisponiveis' => $usuariosDisponiveis,
            'departamentos'       => $departamentos,
            'recursos' => [
                ['key' => 'has_tv',               'label' => 'TV',               'icon' => 'tv-2'],
                ['key' => 'has_wifi',             'label' => 'Wi-Fi',            'icon' => 'wifi'],
                ['key' => 'has_video_conference', 'label' => 'Videoconferência', 'icon' => 'monitor'],
                ['key' => 'has_projector',        'label' => 'Projetor',         'icon' => 'projector'],
                ['key' => 'has_coffee',           'label' => 'Café',             'icon' => 'coffee'],
                ['key' => 'has_whiteboard',       'label' => 'Quadro Branco',    'icon' => 'presentation'],
            ],
        ]);
    }
}
