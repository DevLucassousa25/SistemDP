<?php

namespace App\Livewire\Pages\Okrs;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Livewire\SecureComponent;
use App\Models\Department;
use App\Models\OkrCheckin;
use App\Models\OkrCycle;
use App\Models\OkrKeyResult;
use App\Models\OkrObjective;
use App\Models\User;
use App\Notifications\OkrCheckinRealizadoNotification;
use App\Notifications\OkrKrAtribuidoNotification;
use App\Notifications\OkrObjetivoConcluidoNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class Index extends SecureComponent
{
    use EnviaNotificacoes;

    // ── Navegação ─────────────────────────────────────────────────────
    public string $activeTab    = 'dashboard';
    public ?int   $activeCycleId = null;

    // ── Filtros ───────────────────────────────────────────────────────
    public string $filterLevel  = '';
    public string $filterStatus = '';
    public string $search       = '';

    // ── Expansão de objetivo ──────────────────────────────────────────
    public array $expandedObjectives = [];

    // ── Modal: Objetivo ───────────────────────────────────────────────
    public bool   $objectiveModal = false;
    public string $objectiveMode  = 'create';
    #[Locked]
    public ?int   $editObjectiveId = null;

    public string  $objTitle      = '';
    public string  $objDescription = '';
    public string  $objLevel      = 'individual';
    public ?int    $objOwnerId    = null;
    public ?int    $objDepartmentId = null;
    public ?int    $objParentId   = null;
    public string  $objStatus     = 'not_started';

    // ── Modal: Key Result ─────────────────────────────────────────────
    public bool   $krModal     = false;
    public string $krMode      = 'create';
    #[Locked]
    public ?int   $editKrId    = null;
    #[Locked]
    public ?int   $krObjectiveId = null;

    public string  $krTitle       = '';
    public string  $krDescription = '';
    public string  $krType        = 'numeric';
    public string  $krUnit        = '';
    public float   $krInitial     = 0;
    public float   $krTarget      = 100;
    public float   $krCurrent     = 0;
    public ?int    $krOwnerId     = null;
    public string  $krDueDate     = '';

    // ── Modal: Check-in ───────────────────────────────────────────────
    public bool  $checkinModal = false;
    #[Locked]
    public ?int  $checkinKrId  = null;
    public float $checkinValue = 0;
    public int   $checkinConfidence = 7;
    public string $checkinComment  = '';

    // ── Modal: Ciclo ─────────────────────────────────────────────────
    public bool   $cycleModal  = false;
    public string $cycleMode   = 'create';
    #[Locked]
    public ?int   $editCycleId = null;

    public string $cycleName      = '';
    public string $cycleStart     = '';
    public string $cycleEnd       = '';
    public string $cycleStatus    = 'planning';
    public string $cycleDescription = '';

    // ── Modal: Histórico check-ins ────────────────────────────────────
    public bool $historyModal = false;
    #[Locked]
    public ?int $historyKrId  = null;

    // ── Ciclo de vida ─────────────────────────────────────────────────

    public function mount(): void
    {
        $this->requireAuth();

        // Seleciona o ciclo ativo automaticamente
        $active = OkrCycle::active()->latest()->first();
        $this->activeCycleId = $active?->id ?? OkrCycle::latest()->first()?->id;

        // Defaults do modal de objetivo
        $this->objOwnerId = Auth::id();
    }

    // ── Computeds ─────────────────────────────────────────────────────

    #[Computed]
    public function cycles(): \Illuminate\Support\Collection
    {
        return OkrCycle::orderByDesc('start_date')->get();
    }

    #[Computed]
    public function activeCycle(): ?OkrCycle
    {
        return $this->activeCycleId ? OkrCycle::find($this->activeCycleId) : null;
    }

    #[Computed]
    public function objectives(): \Illuminate\Support\Collection
    {
        if (! $this->activeCycleId) return collect();

        $user      = Auth::user();
        $slug      = $user->accessProfile?->slug ?? '';
        $isRhAdmin = in_array($slug, ['administrator', 'hr']);
        $isManager = $slug === 'manager';

        return OkrObjective::with(['owner', 'department', 'keyResults.owner', 'keyResults.checkins' => fn ($q) => $q->latest()->limit(1)])
            ->where('cycle_id', $this->activeCycleId)
            ->when(! $isRhAdmin, function ($q) use ($isManager, $user) {
                $q->where(function ($inner) use ($isManager, $user) {
                    // Sempre vê os próprios
                    $inner->where('owner_id', $user->id);
                    // Vê os de empresa
                    $inner->orWhere('level', 'company');
                    // Vê os de departamento (próprio depto)
                    if ($user->department_id) {
                        $inner->orWhere(fn ($q2) =>
                            $q2->where('level', 'department')
                               ->where('department_id', $user->department_id)
                        );
                    }
                    // Gestor vê todos do seu departamento
                    if ($isManager && $user->department_id) {
                        $inner->orWhere('department_id', $user->department_id);
                    }
                });
            })
            ->when($this->filterLevel,  fn ($q) => $q->where('level', $this->filterLevel))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->search,       fn ($q) => $q->where('title', 'ilike', '%' . $this->search . '%'))
            ->orderByRaw("CASE level WHEN 'company' THEN 1 WHEN 'department' THEN 2 ELSE 3 END")
            ->orderByDesc('progress')
            ->get();
    }

    #[Computed]
    public function dashboardStats(): array
    {
        if (! $this->activeCycleId) {
            return ['total' => 0, 'on_track' => 0, 'at_risk' => 0, 'behind' => 0, 'completed' => 0, 'progress' => 0];
        }

        $objs = $this->objectives;

        return [
            'total'     => $objs->count(),
            'on_track'  => $objs->where('status', 'on_track')->count(),
            'at_risk'   => $objs->where('status', 'at_risk')->count(),
            'behind'    => $objs->where('status', 'behind')->count(),
            'completed' => $objs->where('status', 'completed')->count(),
            'progress'  => $objs->isNotEmpty() ? round($objs->avg('progress'), 1) : 0,
        ];
    }

    #[Computed]
    public function users(): \Illuminate\Support\Collection
    {
        return User::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'access_profile_id']);
    }

    #[Computed]
    public function departments(): \Illuminate\Support\Collection
    {
        return Department::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function parentObjectives(): \Illuminate\Support\Collection
    {
        if (! $this->activeCycleId) return collect();

        return OkrObjective::where('cycle_id', $this->activeCycleId)
            ->where('level', '!=', 'individual')
            ->whereNull('parent_id')
            ->orderBy('title')
            ->get(['id', 'title', 'level']);
    }

    #[Computed]
    public function historyKr(): ?OkrKeyResult
    {
        return $this->historyKrId
            ? OkrKeyResult::with(['checkins.user', 'objective'])->find($this->historyKrId)
            : null;
    }

    // ── Permissões ────────────────────────────────────────────────────

    public function canManageObjective(OkrObjective $obj): bool
    {
        $user = Auth::user();
        $slug = $user->accessProfile?->slug ?? '';
        if (in_array($slug, ['administrator', 'hr'])) return true;
        if ($slug === 'manager' && $obj->department_id === $user->department_id) return true;
        return $obj->owner_id === $user->id || $obj->created_by === $user->id;
    }

    public function canCheckin(OkrKeyResult $kr): bool
    {
        $user = Auth::user();
        $slug = $user->accessProfile?->slug ?? '';
        if (in_array($slug, ['administrator', 'hr'])) return true;
        if ($slug === 'manager') {
            $obj = $kr->objective;
            if ($obj->department_id === $user->department_id) return true;
        }
        return $kr->owner_id === $user->id;
    }

    public function canManageCycles(): bool
    {
        return Auth::user()?->isRhOuDp() ?? false;
    }

    // ── Expansão ──────────────────────────────────────────────────────

    public function clearFilters(): void
    {
        $this->search       = '';
        $this->filterLevel  = '';
        $this->filterStatus = '';
    }

    public function toggleObjective(int $id): void
    {
        if (in_array($id, $this->expandedObjectives)) {
            $this->expandedObjectives = array_values(array_filter($this->expandedObjectives, fn ($i) => $i !== $id));
        } else {
            $this->expandedObjectives[] = $id;
        }
    }

    // ── CRUD: Ciclos ──────────────────────────────────────────────────

    public function openCreateCycle(): void
    {
        $this->requireRhOrAdmin();
        $this->resetCycleForm();
        $this->cycleMode  = 'create';
        $this->cycleModal = true;
    }

    public function openEditCycle(int $id): void
    {
        $this->requireRhOrAdmin();
        $cycle = OkrCycle::findOrFail($id);
        $this->editCycleId      = $id;
        $this->cycleName        = $cycle->name;
        $this->cycleStart       = $cycle->start_date->format('Y-m-d');
        $this->cycleEnd         = $cycle->end_date->format('Y-m-d');
        $this->cycleStatus      = $cycle->status;
        $this->cycleDescription = $cycle->description ?? '';
        $this->cycleMode        = 'edit';
        $this->cycleModal       = true;
    }

    public function saveCycle(): void
    {
        $this->requireRhOrAdmin();
        $this->validate([
            'cycleName'  => 'required|string|max:100',
            'cycleStart' => 'required|date',
            'cycleEnd'   => 'required|date|after:cycleStart',
            'cycleStatus'=> 'required|in:planning,active,closed',
        ], [
            'cycleName.required'  => 'Nome do ciclo é obrigatório.',
            'cycleEnd.after'      => 'Data de fim deve ser após o início.',
        ]);

        $data = [
            'name'        => $this->sanitize($this->cycleName),
            'start_date'  => $this->cycleStart,
            'end_date'    => $this->cycleEnd,
            'status'      => $this->cycleStatus,
            'description' => $this->sanitize($this->cycleDescription),
        ];

        if ($this->cycleMode === 'create') {
            $cycle = OkrCycle::create(array_merge($data, ['created_by' => Auth::id()]));
            $this->activeCycleId = $cycle->id;
            $this->alertSuccess('Ciclo criado!');
        } else {
            OkrCycle::findOrFail($this->editCycleId)->update($data);
            $this->alertSuccess('Ciclo atualizado!');
        }

        $this->cycleModal = false;
        $this->resetCycleForm();
        unset($this->cycles, $this->activeCycle);
    }

    public function deleteCycle(int $id): void
    {
        $this->requireRhOrAdmin();
        OkrCycle::findOrFail($id)->delete();
        if ($this->activeCycleId === $id) {
            $this->activeCycleId = OkrCycle::latest()->first()?->id;
        }
        $this->alertSuccess('Ciclo removido.');
        unset($this->cycles, $this->activeCycle, $this->objectives, $this->dashboardStats);
    }

    // ── CRUD: Objetivos ───────────────────────────────────────────────

    public function openCreateObjective(string $level = 'individual'): void
    {
        $this->requireAuth();
        if (! $this->activeCycleId) {
            $this->alertError('Crie um ciclo primeiro.');
            return;
        }
        $this->resetObjectiveForm();
        $this->objLevel        = $level;
        $this->objectiveMode   = 'create';
        $this->objectiveModal  = true;
    }

    public function openEditObjective(int $id): void
    {
        $obj = OkrObjective::findOrFail($id);
        if (! $this->canManageObjective($obj)) abort(403);

        $this->editObjectiveId  = $id;
        $this->objTitle         = $obj->title;
        $this->objDescription   = $obj->description ?? '';
        $this->objLevel         = $obj->level;
        $this->objOwnerId       = $obj->owner_id;
        $this->objDepartmentId  = $obj->department_id;
        $this->objParentId      = $obj->parent_id;
        $this->objStatus        = $obj->status;
        $this->objectiveMode    = 'edit';
        $this->objectiveModal   = true;
    }

    public function saveObjective(): void
    {
        $this->requireAuth();

        $this->validate([
            'objTitle'       => 'required|string|max:255',
            'objLevel'       => 'required|in:company,department,individual',
            'objOwnerId'     => 'nullable|exists:users,id',
            'objDepartmentId'=> 'nullable|exists:departments,id',
        ], ['objTitle.required' => 'Título é obrigatório.']);

        // Somente RH/Admin pode criar objetivos de empresa
        if ($this->objLevel === 'company') $this->requireRhOrAdmin();
        // Somente Gestor+/RH/Admin pode criar de departamento
        if ($this->objLevel === 'department') {
            $this->requireRole(['administrator', 'hr', 'manager']);
        }

        $data = [
            'cycle_id'      => $this->activeCycleId,
            'title'         => $this->sanitize($this->objTitle),
            'description'   => $this->sanitize($this->objDescription),
            'level'         => $this->objLevel,
            'owner_id'      => $this->objOwnerId ?: Auth::id(),
            'department_id' => $this->objDepartmentId,
            'parent_id'     => $this->objParentId,
            'status'        => $this->objStatus,
        ];

        if ($this->objectiveMode === 'create') {
            OkrObjective::create(array_merge($data, ['created_by' => Auth::id()]));
            $this->alertSuccess('Objetivo criado!');
        } else {
            $obj = OkrObjective::findOrFail($this->editObjectiveId);
            if (! $this->canManageObjective($obj)) abort(403);
            $obj->update($data);
            $this->alertSuccess('Objetivo atualizado!');
        }

        $this->objectiveModal = false;
        $this->resetObjectiveForm();
        unset($this->objectives, $this->dashboardStats);
    }

    public function deleteObjective(int $id): void
    {
        $obj = OkrObjective::findOrFail($id);
        if (! $this->canManageObjective($obj)) abort(403);
        $obj->delete();
        $this->alertSuccess('Objetivo removido.');
        unset($this->objectives, $this->dashboardStats);
    }

    // ── CRUD: Key Results ─────────────────────────────────────────────

    public function openCreateKr(int $objectiveId): void
    {
        $obj = OkrObjective::findOrFail($objectiveId);
        if (! $this->canManageObjective($obj)) abort(403);
        $this->resetKrForm();
        $this->krObjectiveId = $objectiveId;

        // Pré-preenche o responsável com base no nível do objetivo pai:
        // - Empresa     → usuário logado (quem está criando/assumindo o KR)
        // - Departamento → dono do objetivo (head do depto)
        // - Individual  → dono do objetivo (a própria pessoa)
        $this->krOwnerId = match ($obj->level) {
            'company'    => Auth::id(),
            default      => $obj->owner_id ?? Auth::id(),
        };

        $this->krMode  = 'create';
        $this->krModal = true;
    }

    public function openEditKr(int $id): void
    {
        $kr = OkrKeyResult::with('objective')->findOrFail($id);
        if (! $this->canManageObjective($kr->objective)) abort(403);
        $this->editKrId       = $id;
        $this->krObjectiveId  = $kr->objective_id;
        $this->krTitle        = $kr->title;
        $this->krDescription  = $kr->description ?? '';
        $this->krType         = $kr->type;
        $this->krUnit         = $kr->unit ?? '';
        $this->krInitial      = (float) $kr->initial_value;
        $this->krTarget       = (float) $kr->target_value;
        $this->krCurrent      = (float) $kr->current_value;
        $this->krOwnerId      = $kr->owner_id;
        $this->krDueDate      = $kr->due_date?->format('Y-m-d') ?? '';
        $this->krMode         = 'edit';
        $this->krModal        = true;
    }

    public function saveKr(): void
    {
        $this->requireAuth();
        $this->validate([
            'krTitle'   => 'required|string|max:255',
            'krType'    => 'required|in:numeric,percentage,boolean,currency',
            'krTarget'  => 'required|numeric',
            'krInitial' => 'required|numeric',
            'krCurrent' => 'required|numeric',
            'krDueDate' => 'nullable|date',
        ], ['krTitle.required' => 'Título do Key Result é obrigatório.']);

        $data = [
            'title'         => $this->sanitize($this->krTitle),
            'description'   => $this->sanitize($this->krDescription),
            'type'          => $this->krType,
            'unit'          => $this->sanitize($this->krUnit) ?: null,
            'initial_value' => $this->krInitial,
            'target_value'  => $this->krTarget,
            'current_value' => $this->krCurrent,
            'owner_id'      => $this->krOwnerId ?: Auth::id(),
            'due_date'      => $this->krDueDate ?: null,
            'objective_id'  => $this->krObjectiveId,
        ];

        $previousOwnerId = null;

        if ($this->krMode === 'create') {
            $kr = OkrKeyResult::create($data);
            $kr->aplicarCheckin($this->krCurrent);
            $this->alertSuccess('Key Result criado!');
        } else {
            $kr = OkrKeyResult::findOrFail($this->editKrId);
            $previousOwnerId = $kr->owner_id;
            $kr->update($data);
            $kr->aplicarCheckin($this->krCurrent);
            $this->alertSuccess('Key Result atualizado!');
        }

        // ── Notificação: KR atribuído ────────────────────────────────
        // Notifica o responsável se for diferente de quem está salvando
        // e se o dono mudou (edição) ou é novo (criação)
        $novoOwnerId = $kr->owner_id;
        $ownerMudou  = $this->krMode === 'create' || $novoOwnerId !== $previousOwnerId;

        if ($ownerMudou && $novoOwnerId && $novoOwnerId !== Auth::id()) {
            $dono = User::find($novoOwnerId);
            if ($dono) {
                $this->notificarUsuario(
                    $dono,
                    new OkrKrAtribuidoNotification($kr, Auth::user()->name)
                );
            }
        }

        $this->krModal = false;
        $this->resetKrForm();
        unset($this->objectives, $this->dashboardStats);
    }

    public function deleteKr(int $id): void
    {
        $kr = OkrKeyResult::with('objective')->findOrFail($id);
        if (! $this->canManageObjective($kr->objective)) abort(403);
        $kr->delete();
        $kr->objective->recalcularProgresso();
        $this->alertSuccess('Key Result removido.');
        unset($this->objectives, $this->dashboardStats);
    }

    // ── Check-in ──────────────────────────────────────────────────────

    public function openCheckin(int $krId): void
    {
        $kr = OkrKeyResult::findOrFail($krId);
        if (! $this->canCheckin($kr)) abort(403);
        $this->checkinKrId      = $krId;
        $this->checkinValue     = (float) $kr->current_value;
        $this->checkinConfidence = 7;
        $this->checkinComment   = '';
        $this->checkinModal     = true;
    }

    public function saveCheckin(): void
    {
        $this->requireAuth();
        $this->validate([
            'checkinValue'      => 'required|numeric',
            'checkinConfidence' => 'required|integer|min:1|max:10',
            'checkinComment'    => 'nullable|string|max:1000',
        ], ['checkinValue.required' => 'Informe o valor atual.']);

        $kr = OkrKeyResult::findOrFail($this->checkinKrId);
        if (! $this->canCheckin($kr)) abort(403);

        // Registra histórico
        OkrCheckin::create([
            'key_result_id'  => $kr->id,
            'user_id'        => Auth::id(),
            'previous_value' => $kr->current_value,
            'new_value'      => $this->checkinValue,
            'confidence'     => $this->checkinConfidence,
            'comment'        => $this->sanitize($this->checkinComment) ?: null,
        ]);

        // Aplica o novo valor (recalcula progresso + status)
        $kr->aplicarCheckin($this->checkinValue);

        // ── Notificação: check-in realizado ──────────────────────────
        // Carrega o objetivo com o dono para notificar
        $kr->load('objective.owner');
        $objetivo       = $kr->objective;
        $donoObjetivo   = $objetivo?->owner;
        $nomeAutor      = Auth::user()->name;

        // Notifica o dono do objetivo (se diferente de quem fez o check-in)
        if ($donoObjetivo && $donoObjetivo->id !== Auth::id()) {
            $kr->refresh();
            $this->notificarUsuario(
                $donoObjetivo,
                new OkrCheckinRealizadoNotification(
                    $kr,
                    $nomeAutor,
                    $this->checkinValue,
                    (float) $kr->progress
                )
            );
        }

        // ── Notificação: objetivo concluído ──────────────────────────
        // Se após o check-in o objetivo atingiu 100%, notifica dono + RH/Admin
        $objetivo->refresh();
        if ($objetivo->status === 'completed') {
            $notifConcluido = new OkrObjetivoConcluidoNotification($objetivo);

            // Notifica o dono do objetivo
            if ($donoObjetivo) {
                $this->notificarUsuario($donoObjetivo, $notifConcluido);
            }

            // Notifica RH e Admins
            $this->notificarRhAdmin($notifConcluido);
        }

        $this->checkinModal = false;
        $this->alertSuccess('Check-in registrado!', 'Progresso atualizado com sucesso.');
        unset($this->objectives, $this->dashboardStats);
    }

    // ── Histórico ─────────────────────────────────────────────────────

    public function openHistory(int $krId): void
    {
        $this->historyKrId  = $krId;
        $this->historyModal = true;
        unset($this->historyKr);
    }

    // ── Helpers de reset ──────────────────────────────────────────────

    private function resetObjectiveForm(): void
    {
        $this->editObjectiveId  = null;
        $this->objTitle         = '';
        $this->objDescription   = '';
        $this->objLevel         = 'individual';
        $this->objOwnerId       = Auth::id();
        $this->objDepartmentId  = null;
        $this->objParentId      = null;
        $this->objStatus        = 'not_started';
        $this->resetValidation();
    }

    private function resetKrForm(): void
    {
        $this->editKrId      = null;
        $this->krObjectiveId = null;
        $this->krTitle       = '';
        $this->krDescription = '';
        $this->krType        = 'numeric';
        $this->krUnit        = '';
        $this->krInitial     = 0;
        $this->krTarget      = 100;
        $this->krCurrent     = 0;
        $this->krOwnerId     = null;
        $this->krDueDate     = '';
        $this->resetValidation();
    }

    private function resetCycleForm(): void
    {
        $this->editCycleId      = null;
        $this->cycleName        = '';
        $this->cycleStart       = '';
        $this->cycleEnd         = '';
        $this->cycleStatus      = 'planning';
        $this->cycleDescription = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.pages.okrs.index')
            ->layout('components.layouts.app', ['title' => 'OKRs']);
    }
}
