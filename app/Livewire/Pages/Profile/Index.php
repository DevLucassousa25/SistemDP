<?php

namespace App\Livewire\Pages\Profile;

use App\Livewire\SecureComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\WithFileUploads;

class Index extends SecureComponent
{
    use WithFileUploads;

    // ── Dados editáveis ───────────────────────────────────────────────
    public string $name     = '';
    public string $email    = '';
    public string $position = '';
    public string $bio      = '';

    // ── Avatar ───────────────────────────────────────────────────────
    public $avatarFile = null;   // arquivo temporário (upload)

    // ── Senha ────────────────────────────────────────────────────────
    public string $currentPassword = '';
    public string $newPassword     = '';
    public string $confirmPassword = '';

    // ── UI ───────────────────────────────────────────────────────────
    public string $activeTab = 'info';   // info | senha

    public function mount(): void
    {
        $this->requireAuth();
        $user = Auth::user();

        $this->name     = $user->name;
        $this->email    = $user->email;
        $this->position = $user->position ?? '';
        $this->bio      = $user->bio      ?? '';
    }

    // ── Salvar informações pessoais ───────────────────────────────────

    public function salvarInfo(): void
    {
        $this->requireAuth();

        if (! $this->rateLimit('profile-info', 10, 60)) {
            return;
        }

        $data = $this->validate([
            'name'     => ['required', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'bio'      => ['nullable', 'string', 'max:300'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.max'      => 'O nome pode ter no máximo 100 caracteres.',
            'bio.max'       => 'A bio pode ter no máximo 300 caracteres.',
        ]);

        $data['name']     = $this->sanitize($data['name']);
        $data['position'] = $this->sanitize($data['position'] ?? '');
        $data['bio']      = $this->sanitize($data['bio'] ?? '');

        Auth::user()->update($data);

        $this->alertSuccess('Perfil atualizado!', 'Suas informações foram salvas.');
    }

    // ── Upload de avatar ─────────────────────────────────────────────

    public function updatedAvatarFile(): void
    {
        $this->validate([
            'avatarFile' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'avatarFile.image' => 'O arquivo deve ser uma imagem.',
            'avatarFile.max'   => 'A imagem deve ter no máximo 2 MB.',
            'avatarFile.mimes' => 'Formatos aceitos: JPG, PNG, WebP.',
        ]);
    }

    public function salvarAvatar(): void
    {
        $this->requireAuth();

        if (! $this->rateLimit('profile-avatar', 5, 60)) {
            return;
        }

        $this->validate([
            'avatarFile' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $user = Auth::user();

        // Remove o avatar antigo
        if ($user->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $path = $this->avatarFile->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        $this->avatarFile = null;
        $this->alertSuccess('Foto atualizada!', 'Sua foto de perfil foi salva.');
    }

    public function removerAvatar(): void
    {
        $this->requireAuth();

        $user = Auth::user();

        if ($user->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
            $this->alertSuccess('Foto removida.', 'Sua foto de perfil foi removida.');
        }
    }

    // ── Alterar senha ─────────────────────────────────────────────────

    public function salvarSenha(): void
    {
        $this->requireAuth();

        if (! $this->rateLimit('profile-senha', 5, 60)) {
            return;
        }

        $this->validate([
            'currentPassword' => ['required'],
            'newPassword'     => ['required', Password::min(8)->mixedCase()->numbers(), 'different:currentPassword'],
            'confirmPassword' => ['required', 'same:newPassword'],
        ], [
            'currentPassword.required' => 'Informe a senha atual.',
            'newPassword.required'     => 'Informe a nova senha.',
            'newPassword.different'    => 'A nova senha deve ser diferente da atual.',
            'confirmPassword.same'     => 'As senhas não conferem.',
        ]);

        $user = Auth::user();

        if (! Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Senha atual incorreta.');
            return;
        }

        $user->update(['password' => Hash::make($this->newPassword)]);

        $this->currentPassword = '';
        $this->newPassword     = '';
        $this->confirmPassword = '';

        $this->alertSuccess('Senha alterada!', 'Sua senha foi atualizada com sucesso.');
    }

    public function render()
    {
        return view('livewire.pages.profile.index');
    }
}
