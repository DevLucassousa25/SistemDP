<?php

namespace App\Livewire\Pages\Login;
use App\Livewire\SecureComponent;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class Index extends SecureComponent
{
    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;

    protected array $rules = [
        'email'    => ['required', 'email'],
        'password' => ['required', 'min:6'],
    ];

    protected array $messages = [
        'email.required'    => 'O e-mail é obrigatório.',
        'email.email'       => 'Informe um e-mail válido.',
        'password.required' => 'A senha é obrigatória.',
        'password.min'      => 'A senha deve ter pelo menos 6 caracteres.',
    ];

    public function login(): void
    {
        // Rate limiting: máximo 5 tentativas por e-mail + IP por minuto
        $throttleKey = Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, maxAttempts: 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Muitas tentativas de login. Aguarde {$seconds} segundos e tente novamente.");
            return;
        }

        $this->validate();

        // Busca o usuário pelo e-mail
        $user = User::where('email', $this->email)->first();

        // Credenciais inválidas — resposta genérica para evitar enumeração de usuários
        if (! $user || ! Hash::check($this->password, $user->password)) {
            RateLimiter::hit($throttleKey, decaySeconds: 60);
            $this->addError('email', 'Credenciais inválidas.');
            return;
        }

        // Verifica se a conta está ativa
        if (! $user->is_active) {
            RateLimiter::hit($throttleKey, decaySeconds: 60);
            $this->addError('email', 'Sua conta está inativa. Contate o administrador.');
            return;
        }

        // Verifica se possui departamento vinculado
        if (is_null($user->department_id)) {
            $this->addError('email', 'Sua conta não está associada a nenhum departamento.');
            return;
        }

        // Verifica se possui perfil de acesso vinculado
        if (is_null($user->access_profile_id)) {
            $this->addError('email', 'Sua conta não possui perfil de acesso definido.');
            return;
        }

        // Login bem-sucedido: limpa o rate limiter
        RateLimiter::clear($throttleKey);

        Auth::login($user, $this->remember);

        // Regenera a sessão para evitar session fixation
        session()->regenerate();

        $this->redirect($this->resolveRedirect($user), navigate: true);
    }

    private function resolveRedirect(User $user): string
    {
        return match ($user->accessProfile?->slug) {
            default => route('dashboard'),
        };
    }

    public function render()
    {
        return view('livewire.pages.login.index')->layout('components.layouts.guest');
    }
}
