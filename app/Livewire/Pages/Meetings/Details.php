<?php

namespace App\Livewire\Pages\Meetings;

use App\Livewire\SecureComponent;
use App\Models\Meeting;
use App\Models\MeetingAgendaItem;
use App\Models\MeetingNote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;

#[Title('Detalhes da Reunião')]
class Details extends SecureComponent
{
    #[Locked]
    public int $meetingId;

    public string $activeTab = 'detalhes';

    // ── Nova nota ──────────────────────────────────────────────────────
    public string $newNote = '';

    // ── Modal de cancelamento ─────────────────────────────────────────
    public bool $showCancelModal = false;

    // ── Agenda: adicionar item ────────────────────────────────────────
    public string $newAgendaTitle         = '';
    public ?int   $newAgendaResponsibleId = null;
    public int    $newAgendaDuration      = 15;

    // ──────────────────────────────────────────────────────────────────
    // LIFECYCLE
    // ──────────────────────────────────────────────────────────────────

    public function mount(int $id): void
    {
        $this->requireAuth();

        $meeting = Meeting::with('participants:id')->findOrFail($id);

        $user = Auth::user();
        $authId = (int) $user->id;

        $isOrganizer  = (int) $meeting->organizer_id === $authId;
        $isParticipant = $meeting->participants->contains('id', $authId);
        $isRhOrAdmin  = $user->isRhOuDp();

        if (! $isOrganizer && ! $isParticipant && ! $isRhOrAdmin) {
            abort(403, 'Você não tem acesso a esta reunião.');
        }

        $this->meetingId = $id;

        $this->dispatch('breadcrumb-set', items: [
            ['label' => 'Reuniões', 'icon' => 'calendar', 'url' => route('reunioes')],
            ['label' => $meeting->title, 'url' => null],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // COMPUTED
    // ──────────────────────────────────────────────────────────────────

    #[Computed]
    public function meeting(): Meeting
    {
        return Meeting::with([
                'organizer:id,name,position,department_id',
                'organizer.department:id,name',
                'room:id,name,location',
                'participants:id,name,position,department_id',
                'participants.department:id,name',
                'agendaItems.responsible:id,name,position',
                'meetingNotes.user:id,name,position',
            ])
            ->findOrFail($this->meetingId);
    }

    #[Computed]
    public function isOrganizer(): bool
    {
        return (int) $this->meeting->organizer_id === (int) Auth::id();
    }

    #[Computed]
    public function isParticipant(): bool
    {
        return $this->meeting->participants->contains('id', (int) Auth::id());
    }

    #[Computed]
    public function myRsvp(): ?string
    {
        $me = $this->meeting->participants->firstWhere('id', (int) Auth::id());
        return $me?->pivot?->rsvp;
    }

    /** Contadores de RSVP: confirmados / pendentes / recusou. */
    #[Computed]
    public function rsvpCounts(): array
    {
        $participants = $this->meeting->participants;

        return [
            'confirmados' => $participants->where('pivot.rsvp', 'aceito')->count(),
            'pendentes'   => $participants->where('pivot.rsvp', 'pendente')->count(),
            'recusou'     => $participants->where('pivot.rsvp', 'recusado')->count(),
            'total'       => $participants->count(),
        ];
    }

    /**
     * Estado "ao vivo" da reunião para badges e barra de progresso no hero.
     * Retorna: state (live|upcoming|past|cancelada), label, icon, progress (0–100).
     */
    #[Computed]
    public function liveStatus(): array
    {
        $m = $this->meeting;

        if ($m->status === 'cancelada') {
            return [
                'state'    => 'cancelada',
                'label'    => 'Reunião cancelada',
                'icon'     => 'circle-x',
                'progress' => 0,
            ];
        }

        $now   = Carbon::now();
        $start = $m->start_time;
        $end   = $m->end_time;

        // Acontecendo agora
        if ($now->between($start, $end)) {
            $totalSec = max(1, (int) abs($start->diffInSeconds($end)));
            $elapsed  = max(0, (int) abs($start->diffInSeconds($now)));
            $progress = (int) round(min(100, max(0, ($elapsed / $totalSec) * 100)));

            return [
                'state'    => 'live',
                'label'    => 'Acontecendo agora',
                'icon'     => 'radio',
                'progress' => $progress,
            ];
        }

        // Futuro
        if ($now->lt($start)) {
            $mins = (int) abs($now->diffInMinutes($start));
            if ($mins < 60) {
                $label = 'Começa em ' . max(1, $mins) . ' min';
            } elseif ($mins < 60 * 24) {
                $h  = intdiv($mins, 60);
                $mm = $mins % 60;
                $label = $mm > 0 ? "Começa em {$h}h {$mm}min" : "Começa em {$h}h";
            } elseif ($start->isTomorrow()) {
                $label = 'Começa amanhã às ' . $start->format('H:i');
            } else {
                $days  = (int) abs($now->copy()->startOfDay()->diffInDays($start->copy()->startOfDay()));
                $label = "Começa em {$days} dia" . ($days > 1 ? 's' : '');
            }

            return [
                'state'    => 'upcoming',
                'label'    => $label,
                'icon'     => 'clock',
                'progress' => 0,
            ];
        }

        // Passado
        $mins = (int) abs($end->diffInMinutes($now));
        if ($mins < 60) {
            $label = 'Encerrada há ' . max(1, $mins) . ' min';
        } elseif ($mins < 60 * 24) {
            $h = intdiv($mins, 60);
            $label = "Encerrada há {$h}h";
        } elseif ($end->isYesterday()) {
            $label = 'Encerrada ontem';
        } else {
            $days  = (int) abs($end->copy()->startOfDay()->diffInDays($now->copy()->startOfDay()));
            $label = "Encerrada há {$days} dia" . ($days > 1 ? 's' : '');
        }

        return [
            'state'    => 'past',
            'label'    => $label,
            'icon'     => 'circle-check-big',
            'progress' => 100,
        ];
    }

    // ──────────────────────────────────────────────────────────────────
    // TABS
    // ──────────────────────────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['detalhes', 'pauta', 'participantes', 'notas'], true)) {
            return;
        }
        $this->activeTab = $tab;
    }

    // ──────────────────────────────────────────────────────────────────
    // AÇÕES: EDITAR / CANCELAR REUNIÃO
    // ──────────────────────────────────────────────────────────────────

    public function goToEdit(): void
    {
        if (! $this->isOrganizer) {
            $this->alertError(
                'Ação não permitida',
                'Apenas o organizador pode editar esta reunião.'
            );
            return;
        }

        // Redireciona para o Index já sinalizando abertura do modal de edição
        $this->redirect(
            route('reunioes') . '?edit=' . $this->meetingId,
            navigate: true
        );
    }

    public function openCancelModal(): void
    {
        if (! $this->isOrganizer) {
            $this->alertError(
                'Ação não permitida',
                'Apenas o organizador pode cancelar esta reunião.'
            );
            return;
        }
        $this->showCancelModal = true;
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
    }

    public function cancelMeeting(): void
    {
        if (! $this->isOrganizer) {
            $this->alertError(
                'Ação não permitida',
                'Apenas o organizador pode cancelar esta reunião.'
            );
            $this->showCancelModal = false;
            return;
        }

        $meeting = Meeting::findOrFail($this->meetingId);
        $meeting->update(['status' => 'cancelada']);

        // Libera a sala na tela de Salas (remove a reserva vinculada, se existir).
        \App\Models\Reservations::where('meeting_id', $meeting->id)->delete();

        $this->showCancelModal = false;
        unset($this->meeting);

        $this->alertSuccess(
            'Reunião cancelada',
            'A reunião foi marcada como cancelada.'
        );
    }

    // ──────────────────────────────────────────────────────────────────
    // AÇÕES: RSVP
    // ──────────────────────────────────────────────────────────────────

    public function respondInvite(string $response): void
    {
        if (! in_array($response, ['aceito', 'recusado', 'pendente'], true)) {
            $this->alertError('Resposta inválida', 'Opção de RSVP não reconhecida.');
            return;
        }

        $meeting = Meeting::with('participants:id')->findOrFail($this->meetingId);
        $userId  = (int) Auth::id();

        if ((int) $meeting->organizer_id === $userId) {
            $this->alertError(
                'Ação não permitida',
                'O organizador não precisa responder ao próprio convite.'
            );
            return;
        }

        if (! $meeting->participants->contains('id', $userId)) {
            $this->alertError(
                'Ação não permitida',
                'Você não está na lista de convidados desta reunião.'
            );
            return;
        }

        $meeting->participants()->updateExistingPivot($userId, ['rsvp' => $response]);
        unset($this->meeting, $this->rsvpCounts, $this->myRsvp);

        $this->alertSuccess(match ($response) {
            'aceito'   => 'Convite aceito!',
            'recusado' => 'Convite recusado.',
            default    => 'Resposta atualizada.',
        });
    }

    // ──────────────────────────────────────────────────────────────────
    // AÇÕES: PAUTA
    // ──────────────────────────────────────────────────────────────────

    public function addAgendaItem(): void
    {
        if (! $this->isOrganizer) {
            $this->alertError(
                'Ação não permitida',
                'Apenas o organizador pode adicionar itens à pauta.'
            );
            return;
        }

        $this->validate([
            'newAgendaTitle'         => 'required|string|max:255',
            'newAgendaResponsibleId' => 'nullable|integer|exists:users,id',
            'newAgendaDuration'      => 'required|integer|min:1|max:600',
        ], [
            'newAgendaTitle.required'    => 'Informe o título do item.',
            'newAgendaDuration.required' => 'Informe a duração (em minutos).',
            'newAgendaDuration.min'      => 'A duração deve ser maior que zero.',
        ]);

        $order = MeetingAgendaItem::where('meeting_id', $this->meetingId)->max('sort_order') ?? 0;

        MeetingAgendaItem::create([
            'meeting_id'          => $this->meetingId,
            'responsible_user_id' => $this->newAgendaResponsibleId,
            'title'               => $this->sanitize($this->newAgendaTitle),
            'duration_minutes'    => (int) $this->newAgendaDuration,
            'is_completed'        => false,
            'sort_order'          => $order + 1,
        ]);

        $this->newAgendaTitle         = '';
        $this->newAgendaResponsibleId = null;
        $this->newAgendaDuration      = 15;

        unset($this->meeting);
        $this->alertSuccess('Item adicionado à pauta.');
    }

    public function toggleAgendaItem(int $itemId): void
    {
        $item = MeetingAgendaItem::where('meeting_id', $this->meetingId)
                                 ->findOrFail($itemId);

        $userId = (int) Auth::id();

        $canToggle = $this->isOrganizer
                    || ((int) $item->responsible_user_id === $userId);

        if (! $canToggle) {
            $this->alertError(
                'Ação não permitida',
                'Apenas o organizador ou o responsável pelo item podem marcá-lo.'
            );
            return;
        }

        $item->update(['is_completed' => ! $item->is_completed]);
        unset($this->meeting);
    }

    public function deleteAgendaItem(int $itemId): void
    {
        if (! $this->isOrganizer) {
            $this->alertError(
                'Ação não permitida',
                'Apenas o organizador pode remover itens.'
            );
            return;
        }

        MeetingAgendaItem::where('meeting_id', $this->meetingId)
                         ->where('id', $itemId)
                         ->delete();

        unset($this->meeting);
        $this->alertSuccess('Item removido.');
    }

    // ──────────────────────────────────────────────────────────────────
    // AÇÕES: NOTAS
    // ──────────────────────────────────────────────────────────────────

    public function addNote(): void
    {
        $this->validate([
            'newNote' => 'required|string|min:1|max:2000',
        ], [
            'newNote.required' => 'Digite uma nota antes de adicionar.',
            'newNote.max'      => 'A nota deve ter no máximo 2000 caracteres.',
        ]);

        // Apenas organizador ou participantes podem tomar notas
        if (! $this->isOrganizer && ! $this->isParticipant) {
            $this->alertError(
                'Ação não permitida',
                'Somente participantes da reunião podem adicionar notas.'
            );
            return;
        }

        MeetingNote::create([
            'meeting_id' => $this->meetingId,
            'user_id'    => Auth::id(),
            'content'    => $this->sanitize($this->newNote),
        ]);

        $this->newNote = '';
        unset($this->meeting);

        $this->alertSuccess('Nota adicionada.');
    }

    public function deleteNote(int $noteId): void
    {
        $note = MeetingNote::where('meeting_id', $this->meetingId)
                           ->findOrFail($noteId);

        // Apenas quem escreveu OU organizador podem apagar
        if ((int) $note->user_id !== (int) Auth::id() && ! $this->isOrganizer) {
            $this->alertError(
                'Ação não permitida',
                'Você só pode remover as suas próprias notas.'
            );
            return;
        }

        $note->delete();
        unset($this->meeting);
        $this->alertSuccess('Nota removida.');
    }

    // ──────────────────────────────────────────────────────────────────
    // AÇÕES: ENTRAR / COPIAR LINK
    // ──────────────────────────────────────────────────────────────────

    public function copyAccessLink(): void
    {
        $meeting = $this->meeting;
        $link = $meeting->online_link;

        if (empty($link)) {
            $this->alertError('Sem link', 'Esta reunião não possui um link de acesso.');
            return;
        }

        // Envia para o front executar clipboard
        $this->dispatch('copy-to-clipboard', text: $link);
        $this->alertSuccess('Link copiado!', 'O link de acesso foi copiado para a área de transferência.');
    }

    // ──────────────────────────────────────────────────────────────────
    // RENDER
    // ──────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.pages.meetings.details', [
            'meeting'        => $this->meeting,
            'isOrganizer'    => $this->isOrganizer,
            'isParticipant'  => $this->isParticipant,
            'myRsvp'         => $this->myRsvp,
            'rsvpCounts'     => $this->rsvpCounts,
            'liveStatus'     => $this->liveStatus,
            'allUsers'       => User::where('is_active', true)
                                    ->orderBy('name')
                                    ->get(['id', 'name', 'position']),
        ]);
    }
}
