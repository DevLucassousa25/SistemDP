<?php

namespace App\Livewire\Components;

use App\Models\MoodCheckin as MoodCheckinModel;
use App\Models\User;
use App\Notifications\HumorPessimoNotification;
use App\Livewire\SecureComponent;
use Illuminate\Support\Facades\Auth;

class MoodCheckin extends SecureComponent
{
    public bool   $open      = false;
    public string $humor     = '';
    public string $nota      = '';
    public bool   $respondeu = false;   // respondeu hoje
    public bool   $dispensou = false;   // fechou sem responder hoje

    // Chave de sessão para "dispensado hoje"
    private function sessionKey(): string
    {
        return 'mood_dispensado_' . today()->toDateString() . '_' . Auth::id();
    }

    public function mount(): void
    {
        if (!Auth::check() || Auth::user()->isAdmin()) {
            return;
        }

        $this->respondeu = MoodCheckinModel::respondeuHoje(Auth::id());
        $this->dispensou = session($this->sessionKey(), false);

        // Abre automaticamente se não respondeu e não dispensou hoje
        if (!$this->respondeu && !$this->dispensou) {
            $this->open = true;
        }
    }

    public function mostrar(): void
    {
        if (Auth::user()->isAdmin()) return;
        if ($this->respondeu) return;

        $this->open = true;
    }

    public function dispensar(): void
    {
        $this->open      = false;
        $this->dispensou = true;
        session([$this->sessionKey() => true]);
    }

    /**
     * Recebe humor e nota diretamente do Alpine (sem round-trip extra para setHumor).
     * Isso elimina qualquer AJAX intermediário para seleção de humor.
     */
    public function submeter(string $humor, string $nota = ''): void
    {
        if (!Auth::check() || Auth::user()->isAdmin()) return;

        $this->humor = $humor;
        $this->nota  = $nota;

        $this->validate([
            'humor' => ['required', 'in:otimo,bem,normal,pessimo'],
            'nota'  => ['nullable', 'string', 'max:500'],
        ], [
            'humor.required' => 'Selecione como você está se sentindo.',
        ]);

        $user = Auth::user();

        MoodCheckinModel::firstOrCreate(
            ['user_id' => $user->id, 'checkin_date' => today()],
            ['mood' => $this->humor, 'note' => $this->sanitize($this->nota)]
        );

        if ($this->humor === 'pessimo') {
            $this->notificarPessimo($user);
        }

        $this->respondeu = true;
        $this->open      = false;
        $this->dispensou = false;

        session()->forget($this->sessionKey());

        $this->alertSuccess('Obrigado pelo seu retorno! 💙', 'Sua resposta foi registrada.');
    }

    private function notificarPessimo(User $user): void
    {
        // Notifica o gerente do departamento do usuário
        if ($user->department_id) {
            $gerentes = User::where('department_id', $user->department_id)
                ->whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))
                ->get();

            foreach ($gerentes as $gerente) {
                $gerente->notify(new HumorPessimoNotification($user->name, 'gerente'));
            }
        }

        // Notifica todos os usuários de RH/Admin
        $rhUsers = User::whereHas('accessProfile', fn ($q) =>
            $q->whereIn('slug', ['hr', 'administrator'])
        )->get();

        foreach ($rhUsers as $rh) {
            $rh->notify(new HumorPessimoNotification($user->name, 'rh'));
        }
    }

    public function render()
    {
        return view('livewire.components.mood-checkin');
    }
}
