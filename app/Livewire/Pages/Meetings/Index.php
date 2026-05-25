<?php

namespace App\Livewire\Pages\Meetings;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Livewire\SecureComponent;
use App\Notifications\ReuniaoAgendadaNotification;
use App\Models\Department;
use App\Models\Meeting;
use App\Models\MeetingAgendaItem;
use App\Models\Reservations;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;

#[Title('Reuniões')]
class Index extends SecureComponent
{
    use EnviaNotificacoes;
    // ── Calendário ────────────────────────────────────────────────────────────
    public int    $calYear;
    public int    $calMonth;
    public string $selectedDay; // YYYY-MM-DD

    // ── Modal Nova / Editar Reunião ───────────────────────────────────────────
    public bool   $showModal    = false;
    public bool   $isEditing    = false;

    #[Locked]
    public ?int   $editingId    = null;

    // Campos do formulário
    public string $title            = '';
    public string $description      = '';
    public string $startDate        = '';
    public string $startTime        = '';
    public string $endDate          = '';
    public string $endTime          = '';
    public string $locationType     = 'online';
    public string $onlinePlatform   = 'google_meet';
    public string $onlineLink       = '';
    public ?int   $roomId           = null;
    public array  $participantIds   = [];
    public string $notes            = '';
    public string $participantSearch = '';
    public ?int   $departmentFilter  = null;

    // ── Pauta (itens de agenda vinculados à reunião) ──────────────────────────
    /**
     * Itens da pauta no formato:
     *   ['id' => ?int, 'title' => string, 'responsible_user_id' => ?int, 'duration_minutes' => int]
     */
    public array  $agendaItems             = [];
    public string $newAgendaItemTitle      = '';
    public ?int   $newAgendaItemResponsibleId = null;
    public int    $newAgendaItemDuration   = 15;

    // ── Modal de confirmação de exclusão ─────────────────────────────────────
    public bool   $showDeleteModal  = false;
    #[Locked]
    public ?int   $deletingId       = null;

    // ──────────────────────────────────────────────────────────────────────────
    // LIFECYCLE
    // ──────────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $now               = Carbon::today();
        $this->calYear     = $now->year;
        $this->calMonth    = $now->month;
        $this->selectedDay = $now->toDateString();

        // Se vier da tela de detalhes com ?edit=<id>, abre direto o modal de edição
        $editId = request()->query('edit');
        if ($editId && is_numeric($editId)) {
            $meeting = Meeting::find((int) $editId);
            if ($meeting) {
                $this->selectedDay = $meeting->start_time->toDateString();
                $this->calYear     = $meeting->start_time->year;
                $this->calMonth    = $meeting->start_time->month;
                $this->openEditModal((int) $editId);
            }
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // COMPUTED
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Retorna todos os eventos do dia selecionado:
     * reuniões criadas aqui + reservas de salas — normalizados para um shape comum.
     *
     * Shape de cada item:
     *   source          'meeting' | 'reservation'
     *   id              int (id original)
     *   title           string
     *   start_time      Carbon
     *   end_time        Carbon
     *   location_type   'online' | 'presencial'
     *   online_platform string|null
     *   location_label  string
     *   color_key       'google_meet'|'zoom'|'teams'|'presencial'
     *   organizer_name  string
     *   participants    Collection<User>  (vazia para reservas)
     */
    #[Computed]
    public function meetingsOnSelectedDay(): \Illuminate\Support\Collection
    {
        $authId = Auth::id();

        // ── 1. Reuniões criadas na tela de Reuniões ───────────────────────────
        $meetings = Meeting::onDay($this->selectedDay)
            ->with(['organizer:id,name', 'participants:id,name', 'room:id,name'])
            ->orderBy('start_time')
            ->get()
            ->map(function (Meeting $m) use ($authId) {
                $isOrganizer = (int) $m->organizer_id === (int) $authId;

                $myParticipation = $m->participants->firstWhere('id', $authId);
                $myRsvp          = $myParticipation?->pivot?->rsvp;
                $isParticipant   = $myParticipation !== null;

                return (object) [
                    'source'          => 'meeting',
                    'id'              => $m->id,
                    'title'           => $m->title,
                    'start_time'      => $m->start_time,
                    'end_time'        => $m->end_time,
                    'location_type'   => $m->location_type,
                    'online_platform' => $m->online_platform,
                    'location_label'  => $m->locationLabel(),
                    'color_key'       => $m->location_type === 'presencial'
                                            ? 'presencial'
                                            : ($m->online_platform ?? 'google_meet'),
                    'organizer_id'    => $m->organizer_id,
                    'organizer_name'  => $m->organizer?->name ?? '—',
                    'participants'    => $m->participants,
                    'is_organizer'    => $isOrganizer,
                    'is_participant'  => $isParticipant,
                    'my_rsvp'         => $myRsvp,
                ];
            });

        // ── 2. Reservas de salas criadas na tela de Salas ─────────────────────
        $reservations = Reservations::whereDate('start_time', $this->selectedDay)
            ->where('status', '!=', 'cancelada')
            ->with(['user:id,name', 'room:id,name'])
            ->orderBy('start_time')
            ->get()
            ->map(function (Reservations $r) {
                return (object) [
                    'source'          => 'reservation',
                    'id'              => $r->id,
                    'title'           => $r->title,
                    'start_time'      => $r->start_time,
                    'end_time'        => $r->end_time,
                    'location_type'   => 'presencial',
                    'online_platform' => null,
                    'location_label'  => $r->room?->name ?? 'Sala presencial',
                    'color_key'       => 'presencial',
                    'organizer_name'  => $r->user?->name ?? '—',
                    'participants'    => collect(),
                ];
            });

        return $meetings->concat($reservations)->sortBy('start_time')->values();
    }

    #[Computed]
    public function daysWithMeetings(): array
    {
        $meetingDays = Meeting::query()
            ->selectRaw('DISTINCT DATE(start_time) as day')
            ->whereYear('start_time', $this->calYear)
            ->whereMonth('start_time', $this->calMonth)
            ->pluck('day')
            ->map(fn ($d) => (string) $d)
            ->toArray();

        $reservationDays = Reservations::query()
            ->selectRaw('DISTINCT DATE(start_time) as day')
            ->whereYear('start_time', $this->calYear)
            ->whereMonth('start_time', $this->calMonth)
            ->where('status', '!=', 'cancelada')
            ->pluck('day')
            ->map(fn ($d) => (string) $d)
            ->toArray();

        return array_values(array_unique(array_merge($meetingDays, $reservationDays)));
    }

    /**
     * Mapa dia => [cores] para pintar os pontos no calendário.
     * Reuniões online => azul/índigo/roxo; presenciais/reservas => verde.
     */
    #[Computed]
    public function eventColorsByDay(): array
    {
        $map = [];

        // Reuniões
        $rows = Meeting::query()
            ->selectRaw('DATE(start_time) as day, location_type, online_platform')
            ->whereYear('start_time', $this->calYear)
            ->whereMonth('start_time', $this->calMonth)
            ->get();

        foreach ($rows as $r) {
            $day = (string) $r->day;
            $color = match (true) {
                $r->location_type === 'presencial'      => 'bg-emerald-400',
                $r->online_platform === 'zoom'          => 'bg-indigo-400',
                $r->online_platform === 'teams'         => 'bg-purple-400',
                default                                 => 'bg-blue-400',
            };
            if (! isset($map[$day])) $map[$day] = [];
            if (! in_array($color, $map[$day])) $map[$day][] = $color;
        }

        // Reservas de salas
        $resDays = Reservations::query()
            ->selectRaw('DISTINCT DATE(start_time) as day')
            ->whereYear('start_time', $this->calYear)
            ->whereMonth('start_time', $this->calMonth)
            ->where('status', '!=', 'cancelada')
            ->pluck('day');

        foreach ($resDays as $d) {
            $day = (string) $d;
            if (! isset($map[$day])) $map[$day] = [];
            if (! in_array('bg-emerald-400', $map[$day])) $map[$day][] = 'bg-emerald-400';
        }

        return $map;
    }

    #[Computed]
    public function rooms(): \Illuminate\Database\Eloquent\Collection
    {
        return Room::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function allUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('is_active', true)
            ->where('id', '!=', Auth::id()) // organizador não aparece na lista de convidados
            ->when(
                trim($this->participantSearch) !== '',
                fn ($q) => $q->where(function ($sub) {
                    $term = '%' . trim($this->participantSearch) . '%';
                    $sub->where('name',     'ilike', $term)
                        ->orWhere('position', 'ilike', $term);
                })
            )
            ->when(
                $this->departmentFilter !== null,
                fn ($q) => $q->where('department_id', $this->departmentFilter)
            )
            ->orderBy('name')
            ->get(['id', 'name', 'position', 'department_id']);
    }

    #[Computed]
    public function allDepartments(): \Illuminate\Database\Eloquent\Collection
    {
        return Department::has('users')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function setDepartmentFilter(?int $deptId): void
    {
        $this->departmentFilter = ($this->departmentFilter === $deptId) ? null : $deptId;
        unset($this->allUsers);
    }

    public function toggleDepartmentParticipants(int $deptId): void
    {
        $deptUserIds = User::where('is_active', true)
            ->where('id', '!=', Auth::id())
            ->where('department_id', $deptId)
            ->pluck('id')
            ->map(fn ($v) => (string) $v)
            ->toArray();

        // Se todos já estão selecionados → desmarcar todos do depto; senão → marcar todos
        $alreadyAll = count(array_diff($deptUserIds, array_map('strval', $this->participantIds))) === 0;

        if ($alreadyAll) {
            $this->participantIds = array_values(
                array_filter($this->participantIds, fn ($id) => ! in_array((string) $id, $deptUserIds))
            );
        } else {
            $merged = array_unique(array_merge(array_map('strval', $this->participantIds), $deptUserIds));
            $this->participantIds = array_values($merged);
        }
    }

    /** Array de semanas para renderizar o grid do calendário. Semana começa no domingo. */
    #[Computed]
    public function calendarWeeks(): array
    {
        $firstDay = Carbon::create($this->calYear, $this->calMonth, 1);
        // Carbon: 0=dom, 1=seg … 6=sáb — semana começa no domingo
        $startOffset = $firstDay->dayOfWeek; // 0=dom … 6=sáb
        $daysInMonth = $firstDay->daysInMonth;

        $weeks = [];
        $week  = [];

        // Dias do mês anterior (padding)
        for ($i = 0; $i < $startOffset; $i++) {
            $week[] = null;
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $week[] = Carbon::create($this->calYear, $this->calMonth, $day);

            if (count($week) === 7) {
                $weeks[] = $week;
                $week    = [];
            }
        }

        // Padding no final
        if (! empty($week)) {
            while (count($week) < 7) {
                $week[] = null;
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // NAVEGAÇÃO DO CALENDÁRIO
    // ──────────────────────────────────────────────────────────────────────────

    public function previousMonth(): void
    {
        $date = Carbon::create($this->calYear, $this->calMonth, 1)->subMonth();
        $this->calYear  = $date->year;
        $this->calMonth = $date->month;
        unset($this->daysWithMeetings);
        unset($this->eventColorsByDay);
        unset($this->calendarWeeks);
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->calYear, $this->calMonth, 1)->addMonth();
        $this->calYear  = $date->year;
        $this->calMonth = $date->month;
        unset($this->daysWithMeetings);
        unset($this->eventColorsByDay);
        unset($this->calendarWeeks);
    }

    public function goToToday(): void
    {
        $now = Carbon::today();
        $this->calYear     = $now->year;
        $this->calMonth    = $now->month;
        $this->selectedDay = $now->toDateString();
        unset($this->daysWithMeetings);
        unset($this->eventColorsByDay);
        unset($this->calendarWeeks);
        unset($this->meetingsOnSelectedDay);
    }

    public function selectDay(string $date): void
    {
        $this->selectedDay = $date;
        unset($this->meetingsOnSelectedDay);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // MODAL – NOVA REUNIÃO
    // ──────────────────────────────────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->resetForm();

        $start = Carbon::now()->addMinutes(5);
        $end   = (clone $start)->addHour();

        $this->startDate  = $start->toDateString();
        $this->startTime  = $start->format('H:i');
        $this->endDate    = $end->toDateString();
        $this->endTime    = $end->format('H:i');
        $this->isEditing  = false;
        $this->editingId  = null;
        $this->showModal  = true;
        $this->resetErrorBag();
    }

    public function openEditModal(int $id): void
    {
        $meeting = Meeting::with(['participants', 'agendaItems'])->findOrFail($id);

        if ((int) $meeting->organizer_id !== (int) Auth::id()) {
            $this->alertError(
                'Ação não permitida',
                'Apenas quem criou a reunião pode editá-la.'
            );
            return;
        }

        $this->editingId        = $id;
        $this->isEditing        = true;
        $this->title            = $meeting->title;
        $this->description      = $meeting->description ?? '';
        $this->startDate        = $meeting->start_time->toDateString();
        $this->startTime        = $meeting->start_time->format('H:i');
        $this->endDate          = $meeting->end_time->toDateString();
        $this->endTime          = $meeting->end_time->format('H:i');
        $this->locationType     = $meeting->location_type;
        $this->onlinePlatform   = $meeting->online_platform ?? 'google_meet';
        $this->onlineLink       = $meeting->online_link ?? '';
        $this->roomId           = $meeting->room_id;
        $this->participantIds   = $meeting->participants->pluck('id')->map(fn ($v) => (string) $v)->toArray();
        $this->notes            = $meeting->notes ?? '';

        // Carrega a pauta existente (preservando ids para preservar is_completed ao salvar)
        $this->agendaItems = $meeting->agendaItems
            ->map(fn (MeetingAgendaItem $i) => [
                'id'                  => $i->id,
                'title'               => $i->title,
                'responsible_user_id' => $i->responsible_user_id,
                'duration_minutes'    => (int) $i->duration_minutes,
            ])
            ->values()
            ->toArray();

        $this->showModal        = true;
        $this->resetErrorBag();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PAUTA (dentro do formulário)
    // ──────────────────────────────────────────────────────────────────────────

    public function addAgendaItemToForm(): void
    {
        $this->validate([
            'newAgendaItemTitle'       => 'required|string|max:255',
            'newAgendaItemResponsibleId' => 'nullable|integer|exists:users,id',
            'newAgendaItemDuration'    => 'required|integer|min:1|max:600',
        ], [
            'newAgendaItemTitle.required'    => 'Informe o título do item.',
            'newAgendaItemDuration.required' => 'Informe a duração (min).',
            'newAgendaItemDuration.min'      => 'A duração deve ser maior que zero.',
        ]);

        $this->agendaItems[] = [
            'id'                  => null,
            'title'               => $this->sanitize($this->newAgendaItemTitle),
            'responsible_user_id' => $this->newAgendaItemResponsibleId,
            'duration_minutes'    => (int) $this->newAgendaItemDuration,
        ];

        $this->newAgendaItemTitle        = '';
        $this->newAgendaItemResponsibleId = null;
        $this->newAgendaItemDuration     = 15;
    }

    public function removeAgendaItemFromForm(int $index): void
    {
        if (! isset($this->agendaItems[$index])) return;
        unset($this->agendaItems[$index]);
        $this->agendaItems = array_values($this->agendaItems);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function saveReuniao(): void
    {
        $this->validate([
            'title'          => 'required|string|max:255',
            'startDate'      => 'required|date',
            'startTime'      => 'required',
            'endDate'        => 'required|date',
            'endTime'        => 'required',
            'locationType'   => 'required|in:online,presencial',
            'onlinePlatform' => 'required_if:locationType,online',
            'onlineLink'     => 'nullable|url|max:500',
            'roomId'         => 'required_if:locationType,presencial|nullable|exists:rooms,id',
        ], [
            'title.required'             => 'O título é obrigatório.',
            'startDate.required'         => 'Informe a data de início.',
            'endDate.required'           => 'Informe a data de término.',
            'startTime.required'         => 'Informe o horário de início.',
            'endTime.required'           => 'Informe o horário de término.',
            'locationType.required'      => 'Selecione o tipo de local.',
            'onlinePlatform.required_if' => 'Selecione a plataforma online.',
            'onlineLink.url'             => 'O link deve ser uma URL válida.',
            'roomId.required_if'         => 'Para reuniões presenciais é obrigatório escolher uma sala.',
            'roomId.exists'              => 'A sala selecionada é inválida.',
        ]);

        $startDt = Carbon::parse("{$this->startDate} {$this->startTime}");
        $endDt   = Carbon::parse("{$this->endDate} {$this->endTime}");
        $now     = Carbon::now();

        // Bloqueia datas/horários no passado
        if ($startDt->lt($now)) {
            $this->addError('startTime', 'Não é possível agendar uma reunião em data/horário já passado.');
            return;
        }

        if ($endDt->lte($startDt)) {
            $this->addError('endTime', 'O término deve ser após o início.');
            return;
        }

        // ── Verificação de conflitos ──────────────────────────────────────────
        if ($this->hasConflicts($startDt, $endDt)) {
            return; // erros já adicionados dentro de hasConflicts()
        }

        $data = [
            'title'           => $this->sanitize($this->title),
            'description'     => $this->sanitize($this->description),
            'start_time'      => $startDt,
            'end_time'        => $endDt,
            'location_type'   => $this->locationType,
            'online_platform' => $this->locationType === 'online'  ? $this->onlinePlatform : null,
            'online_link'     => $this->locationType === 'online'  ? $this->sanitize($this->onlineLink) : null,
            'room_id'         => $this->locationType === 'presencial' ? $this->roomId : null,
            'notes'           => $this->sanitize($this->notes),
        ];

        if ($this->isEditing && $this->editingId) {
            $meeting = Meeting::with('participants')->findOrFail($this->editingId);

            // Só o organizador pode editar
            if ((int) $meeting->organizer_id !== (int) Auth::id()) {
                $this->alertError(
                    'Ação não permitida',
                    'Apenas quem criou a reunião pode editá-la.'
                );
                return;
            }

            $meeting->update($data);

            // Preserva o rsvp dos participantes já existentes
            $existingRsvp = $meeting->participants
                ->mapWithKeys(fn ($u) => [(int) $u->id => $u->pivot->rsvp ?? 'pendente'])
                ->toArray();

            $syncData = collect($this->participantIds)
                ->mapWithKeys(fn ($id) => [
                    (int) $id => ['rsvp' => $existingRsvp[(int) $id] ?? 'pendente'],
                ])
                ->toArray();

            $meeting->participants()->sync($syncData);
        } else {
            $meeting = Meeting::create([
                ...$data,
                'organizer_id' => Auth::id(),
                'status'       => 'pendente',
            ]);

            // Novos participantes começam como pendentes
            $syncData = collect($this->participantIds)
                ->mapWithKeys(fn ($id) => [(int) $id => ['rsvp' => 'pendente']])
                ->toArray();

            $meeting->participants()->sync($syncData);

            // Notifica participantes sobre nova reunião agendada (exceto o criador)
            $participantes = $meeting->participants()->get();
            $notificados   = $this->notificarLista($participantes, new ReuniaoAgendadaNotification($meeting, $participantes));
            if ($notificados > 0) {
                $this->toastNotif(
                    'Reunião agendada!',
                    "{$notificados} participante(s) foram notificados.",
                    'calendar', 'indigo',
                    route('reunioes')
                );
            }
        }

        // ── Sincroniza itens da pauta ─────────────────────────────────────────
        $this->syncAgendaItems($meeting->id);

        // ── Sincroniza reserva de sala na tela de Salas ───────────────────────
        $this->syncReservationForMeeting($meeting);

        $this->showModal = false;
        $this->resetForm();

        // Refresca o dia exibido
        $this->selectedDay = $startDt->toDateString();

        unset($this->meetingsOnSelectedDay);
        unset($this->daysWithMeetings);

        $this->alertSuccess(
            $this->isEditing ? 'Reunião atualizada!' : 'Reunião criada!',
            $this->isEditing ? 'As alterações foram salvas.' : 'A reunião foi agendada com sucesso.'
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // EXCLUSÃO
    // ──────────────────────────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $meeting = Meeting::findOrFail($id);

        if ((int) $meeting->organizer_id !== (int) Auth::id()) {
            $this->alertError(
                'Ação não permitida',
                'Apenas quem criou a reunião pode excluí-la.'
            );
            return;
        }

        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function deleteReuniao(): void
    {
        if (! $this->deletingId) return;

        $meeting = Meeting::findOrFail($this->deletingId);

        // Dupla checagem: só o organizador pode excluir
        if ((int) $meeting->organizer_id !== (int) Auth::id()) {
            $this->deletingId      = null;
            $this->showDeleteModal = false;
            $this->alertError(
                'Ação não permitida',
                'Apenas quem criou a reunião pode excluí-la.'
            );
            return;
        }

        $meeting->delete();

        $this->deletingId      = null;
        $this->showDeleteModal = false;

        unset($this->meetingsOnSelectedDay);
        unset($this->daysWithMeetings);

        $this->alertSuccess('Reunião excluída!', 'A reunião foi removida do calendário.');
    }

    public function cancelDelete(): void
    {
        $this->deletingId      = null;
        $this->showDeleteModal = false;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // RSVP — CONVITES DE PARTICIPANTES
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Permite ao participante responder a um convite.
     * $response: 'aceito' | 'recusado' | 'pendente'
     */
    public function respondInvite(int $meetingId, string $response): void
    {
        $this->requireAuth();

        if (! in_array($response, ['aceito', 'recusado', 'pendente'], true)) {
            $this->alertError('Resposta inválida', 'Opção de RSVP não reconhecida.');
            return;
        }

        $meeting = Meeting::with('participants')->findOrFail($meetingId);
        $userId  = (int) Auth::id();

        // Organizador não responde convite (já é dono)
        if ((int) $meeting->organizer_id === $userId) {
            $this->alertError(
                'Ação não permitida',
                'O organizador não precisa responder ao próprio convite.'
            );
            return;
        }

        // Só quem foi convidado pode responder
        if (! $meeting->participants->contains('id', $userId)) {
            $this->alertError(
                'Ação não permitida',
                'Você não está na lista de convidados desta reunião.'
            );
            return;
        }

        $meeting->participants()->updateExistingPivot($userId, ['rsvp' => $response]);

        unset($this->meetingsOnSelectedDay);

        $msg = match ($response) {
            'aceito'   => 'Convite aceito!',
            'recusado' => 'Convite recusado.',
            default    => 'Resposta atualizada.',
        };

        $this->alertSuccess($msg);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Verifica conflitos de sala e de participantes para o intervalo informado.
     * Adiciona os erros de validação e retorna true se houver qualquer conflito.
     */
    private function hasConflicts(Carbon $startDt, Carbon $endDt): bool
    {
        $excludeId = $this->isEditing ? $this->editingId : null;
        $hasError  = false;

        // ── 1. Conflito de sala ───────────────────────────────────────────────
        if ($this->locationType === 'presencial' && $this->roomId) {
            // Verifica conflito em meetings
            $roomConflict = Meeting::where('room_id', $this->roomId)
                ->where('status', '!=', 'cancelada')
                ->where('start_time', '<', $endDt)
                ->where('end_time',   '>', $startDt)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->with('room:id,name')
                ->first();

            // Verifica conflito em reservations (tela de salas)
            if (! $roomConflict) {
                $roomConflict = Reservations::where('room_id', $this->roomId)
                    ->where('status', '!=', 'cancelada')
                    ->where('start_time', '<', $endDt)
                    ->where('end_time',   '>', $startDt)
                    ->with('room:id,name')
                    ->first();
            }

            if ($roomConflict) {
                $roomName = $roomConflict->room?->name ?? 'esta sala';
                $this->addError('roomId',
                    "A sala «{$roomName}» já está reservada das "
                    . $roomConflict->start_time->format('H:i')
                    . ' às '
                    . $roomConflict->end_time->format('H:i')
                    . ' neste dia. Escolha outra sala ou horário.'
                );
                $hasError = true;
            }
        }

        // ── 2. Conflito de participantes ──────────────────────────────────────
        if (! empty($this->participantIds)) {
            // IDs dos participantes que já têm reunião nesse intervalo
            $busyUserIds = \DB::table('meeting_participants as mp')
                ->join('meetings as m', 'm.id', '=', 'mp.meeting_id')
                ->whereIn('mp.user_id', $this->participantIds)
                ->where('m.status', '!=', 'cancelada')
                ->where('m.start_time', '<', $endDt)
                ->where('m.end_time',   '>', $startDt)
                ->when($excludeId, fn ($q) => $q->where('m.id', '!=', $excludeId))
                ->pluck('mp.user_id')
                ->unique()
                ->values()
                ->toArray();

            if (! empty($busyUserIds)) {
                $busyNames = User::whereIn('id', $busyUserIds)
                    ->pluck('name')
                    ->implode(', ');

                $this->addError('participantIds',
                    "Os seguintes participantes já têm reunião neste horário: {$busyNames}. "
                    . 'Remova-os ou escolha outro horário.'
                );
                $hasError = true;
            }
        }

        return $hasError;
    }

    private function resetForm(): void
    {
        $this->title          = '';
        $this->description    = '';
        $this->startDate      = '';
        $this->startTime      = '';
        $this->endDate        = '';
        $this->endTime        = '';
        $this->locationType   = 'online';
        $this->onlinePlatform = 'google_meet';
        $this->onlineLink     = '';
        $this->roomId         = null;
        $this->participantIds    = [];
        $this->notes             = '';
        $this->participantSearch = '';
        $this->departmentFilter  = null;
        $this->isEditing         = false;
        $this->editingId         = null;

        // Pauta
        $this->agendaItems                = [];
        $this->newAgendaItemTitle         = '';
        $this->newAgendaItemResponsibleId = null;
        $this->newAgendaItemDuration      = 15;
    }

    /**
     * Cria / atualiza / remove os itens de pauta da reunião com base em $agendaItems.
     * Preserva is_completed dos itens existentes (via id).
     */
    private function syncAgendaItems(int $meetingId): void
    {
        $existingIds = MeetingAgendaItem::where('meeting_id', $meetingId)
            ->pluck('id')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        $keptIds = [];

        foreach ($this->agendaItems as $idx => $item) {
            $payload = [
                'responsible_user_id' => $item['responsible_user_id'] ?: null,
                'title'               => (string) ($item['title'] ?? ''),
                'duration_minutes'    => max(1, (int) ($item['duration_minutes'] ?? 15)),
                'sort_order'          => $idx + 1,
            ];

            if (! empty($item['id']) && in_array((int) $item['id'], $existingIds, true)) {
                // Update existente — preserva is_completed
                MeetingAgendaItem::where('id', (int) $item['id'])
                    ->where('meeting_id', $meetingId)
                    ->update($payload);
                $keptIds[] = (int) $item['id'];
            } else {
                // Novo item
                MeetingAgendaItem::create([
                    'meeting_id'   => $meetingId,
                    'is_completed' => false,
                    ...$payload,
                ]);
            }
        }

        // Remove os que foram tirados do formulário (só faz sentido em edição)
        $toDelete = array_values(array_diff($existingIds, $keptIds));
        if (! empty($toDelete)) {
            MeetingAgendaItem::whereIn('id', $toDelete)
                ->where('meeting_id', $meetingId)
                ->delete();
        }
    }

    /**
     * Mantém a tabela `reservations` em sincronia com a reunião.
     *
     * Regras:
     * - Reunião presencial + sala escolhida + status != cancelada
     *      → cria/atualiza uma reserva vinculada (meeting_id) com status 'confirmada'.
     * - Reunião online OU sem sala OU cancelada
     *      → remove a reserva vinculada (se existir).
     *
     * Assim a tela de Salas reflete automaticamente as reuniões presenciais agendadas.
     */
    private function syncReservationForMeeting(Meeting $meeting): void
    {
        $shouldHaveReservation =
            $meeting->location_type === 'presencial'
            && ! empty($meeting->room_id)
            && $meeting->status !== 'cancelada';

        $existing = Reservations::where('meeting_id', $meeting->id)->first();

        if (! $shouldHaveReservation) {
            // Se a reunião virou online / perdeu a sala / foi cancelada, remove a reserva vinculada.
            if ($existing) {
                $existing->delete();
            }
            return;
        }

        $attendeesCount = 1 + (int) $meeting->participants()->count();

        $payload = [
            'room_id'          => (int) $meeting->room_id,
            'user_id'          => (int) $meeting->organizer_id,
            'title'            => (string) $meeting->title,
            'description'      => (string) ($meeting->description ?? ''),
            'start_time'       => $meeting->start_time,
            'end_time'         => $meeting->end_time,
            'attendees_count'  => max(1, $attendeesCount),
            'status'           => 'confirmada',
            'is_maintenance'   => false,
        ];

        if ($existing) {
            $existing->update($payload);
        } else {
            Reservations::create([
                'meeting_id' => $meeting->id,
                ...$payload,
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // RENDER
    // ──────────────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.pages.meetings.index');
    }
}
