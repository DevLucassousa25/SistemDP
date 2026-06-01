<?php

namespace App\Livewire\Components\Ui\Modal;
use App\Livewire\SecureComponent;

use App\Models\AccessProfile;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Locked;

class ModalCreate extends SecureComponent
{
    public $open = false;

    // #[Locked] impede que o cliente force modo e userId via JS/wire
    #[Locked]
    public $mode = 'create'; // create | update

    #[Locked]
    public $userId = null;

    // Campos do formulário
    public $name;
    public $email;
    public string $cpf      = '';
    public string $telefone = '';
    public $department;
    public $cargo;
    public $perfil;
    public $password;
    public $ativo = true;

    public $departments;
    public $accessProfiles;

    #[Locked]
    public bool $allowMultipleManagersPerDepartment = false;

    #[Locked]
    public array $departmentsWithManager = [];

    public ?string $managerWarning = null;

    protected $listeners = [
        'openUserModal' => 'open',
        'editUser'      => 'edit',
    ];

    protected $messages = [
        'name.required'       => 'O nome é obrigatório',
        'name.max'            => 'O nome deve ter no máximo 255 caracteres',
        'email.required'      => 'O email é obrigatório',
        'email.email'         => 'Email inválido',
        'email.unique'        => 'Este email já está em uso',
        'department.exists'   => 'Departamento inválido',
        'perfil.exists'       => 'Perfil inválido',
        'cargo.required'      => 'O cargo é obrigatório',
        'cargo.string'        => 'O cargo deve ser uma string',
        'cargo.max'           => 'O cargo deve ter no máximo 100 caracteres',
        'password.required'   => 'A senha é obrigatória para novos usuários',
        'password.min'        => 'A senha deve ter no mínimo 8 caracteres',
    ];

    public function mount(): void
    {
        $this->departments    = Department::all();
        $this->accessProfiles = AccessProfile::all();
    }

    // ─── Abertura ────────────────────────────────────────────────────────────

    public function open(): void
    {
        if (! Auth::user()->isRhOuDp()) {
            abort(403, 'Sem permissão para criar usuários.');
        }

        $this->resetForm();
        $this->mode = 'create';
        $this->open = true;
    }

    public function edit(int $id): void
    {
        if (! Auth::user()->isRhOuDp()) {
            abort(403, 'Sem permissão para editar usuários.');
        }

        $user = User::findOrFail($id);

        $this->userId     = $user->id;
        $this->name       = $user->name;
        $this->email      = $user->email;
        $this->cpf        = $user->cpf ?? '';
        $this->telefone   = $user->telefone ?? '';
        $this->department = (string) $user->department_id;
        $this->cargo      = $user->position;
        $this->perfil     = (string) $user->access_profile_id;
        $this->ativo      = $user->is_active;

        $this->mode = 'update';
        $this->open = true;

        $this->recalculateDepartmentsWithManager();
    }

    public function close(): void
    {
        $this->open = false;
    }

    // ─── Watchers reativos ───────────────────────────────────────────────────

    public function updatedPerfil(): void
    {
        $this->managerWarning = null;
        $this->recalculateDepartmentsWithManager();

        if (
            $this->department &&
            $this->isManagerProfile($this->perfil) &&
            ! $this->allowMultipleManagersPerDepartment &&
            in_array((int) $this->department, $this->departmentsWithManager)
        ) {
            $this->department     = null;
            $this->managerWarning = null;
        }
    }

    public function checkDepartment(): void
    {
        $this->managerWarning = null;

        if (
            $this->department &&
            $this->isManagerProfile($this->perfil) &&
            in_array((int) $this->department, $this->departmentsWithManager)
        ) {
            $dept = $this->departments->firstWhere('id', (int) $this->department);
            $this->managerWarning = "O departamento \"{$dept?->name}\" já possui um gerente cadastrado.";
        }
    }

    // ─── Salvar ──────────────────────────────────────────────────────────────

    public function save(): void
    {
        // Re-valida autorização no servidor
        if (! Auth::user()->isRhOuDp()) {
            abort(403);
        }

        try {
            $rules = [
                'name'       => 'required|string|max:255',
                'email'      => 'required|email:rfc,dns',
                'department' => 'nullable|exists:departments,id',
                'cargo'      => 'required|string|max:100',
                'perfil'     => 'nullable|exists:access_profiles,id',
            ];

            // Email único: ignorando o próprio usuário em edição
            $rules['email'] .= $this->mode === 'create'
                ? '|unique:users,email'
                : '|unique:users,email,' . $this->userId;

            // Senha: obrigatória na criação, opcional na edição (mas forte se fornecida)
            if ($this->mode === 'create') {
                $rules['password'] = ['required', Password::min(8)->letters()->numbers()];
            } elseif ($this->password) {
                $rules['password'] = [Password::min(8)->letters()->numbers()];
            }

            $this->validate($rules);

            // ── Validações extras para perfil Gestor ────────────────────────
            if ($this->isManagerProfile($this->perfil) && ! $this->allowMultipleManagersPerDepartment) {
                if (! $this->department) {
                    $this->managerWarning = 'O perfil Gestor exige que um departamento seja selecionado.';
                    return;
                }

                $occupiedByOther = User::where('department_id', (int) $this->department)
                    ->where('access_profile_id', $this->getManagerProfileId())
                    ->when($this->userId, fn($q) => $q->where('id', '!=', $this->userId))
                    ->exists();

                if ($occupiedByOther) {
                    $dept = $this->departments->firstWhere('id', (int) $this->department);
                    $this->managerWarning = "O departamento \"{$dept?->name}\" já possui um gerente. Selecione outro departamento ou altere o perfil de acesso.";
                    return;
                }
            }

            if ($this->mode === 'create') {
                User::create([
                    'name'              => strip_tags($this->name),
                    'email'             => $this->email,
                    'cpf'               => strip_tags($this->cpf) ?: null,
                    'telefone'          => strip_tags($this->telefone) ?: null,
                    'department_id'     => (int) $this->department ?: null,
                    'position'          => strip_tags($this->cargo),
                    'access_profile_id' => $this->perfil ?: null,
                    'password'          => Hash::make($this->password),
                    'is_active'         => $this->ativo,
                ]);

                $this->dispatch('userCreated');

            } else {
                $user = User::findOrFail($this->userId);

                $data = [
                    'name'              => strip_tags($this->name),
                    'email'             => $this->email,
                    'cpf'               => strip_tags($this->cpf) ?: null,
                    'telefone'          => strip_tags($this->telefone) ?: null,
                    'department_id'     => (int) $this->department ?: null,
                    'position'          => strip_tags($this->cargo),
                    'access_profile_id' => $this->perfil ?: null,
                    'is_active'         => $this->ativo,
                ];

                if ($this->password) {
                    $data['password'] = Hash::make($this->password);
                }

                $user->update($data);
                $this->dispatch('userUpdated');
            }

            $this->resetForm();
            $this->close();

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Erro inesperado ao salvar usuário.');
        }
    }

    // ─── Helpers privados ────────────────────────────────────────────────────

    private function recalculateDepartmentsWithManager(): void
    {
        if (! $this->isManagerProfile($this->perfil) || $this->allowMultipleManagersPerDepartment) {
            $this->departmentsWithManager = [];
            return;
        }

        $this->departmentsWithManager = User::where('access_profile_id', $this->getManagerProfileId())
            ->when($this->userId, fn($q) => $q->where('id', '!=', $this->userId))
            ->whereNotNull('department_id')
            ->pluck('department_id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();
    }

    private function getManagerProfileId(): ?int
    {
        $profile = $this->accessProfiles->firstWhere('slug', 'manager');
        return $profile ? (int) $profile->id : null;
    }

    private function isManagerProfile(mixed $perfilId): bool
    {
        if (! $perfilId) return false;
        $managerId = $this->getManagerProfileId();
        return $managerId !== null && (int) $perfilId === $managerId;
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'email', 'cpf', 'telefone', 'department', 'cargo', 'perfil', 'password', 'ativo', 'userId']);
        $this->ativo                  = true;
        $this->departmentsWithManager = [];
        $this->managerWarning         = null;
    }

    public function render()
    {
        return view('livewire.components.ui.modal.modal-create', [
            'departments'    => $this->departments,
            'accessProfiles' => $this->accessProfiles,
        ]);
    }
}
