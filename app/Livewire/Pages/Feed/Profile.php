<?php

namespace App\Livewire\Pages\Feed;

use App\Livewire\SecureComponent;
use App\Models\FeedPost;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class Profile extends SecureComponent
{
    #[Locked]
    public int $userId;

    // ── Edição de perfil ──────────────────────────────────────────────
    public bool   $editModal    = false;
    public string $editBio      = '';
    public string $editPosition = '';

    public function mount(int $userId): void
    {
        $this->requireAuth();
        $this->userId = $userId;
    }

    public function openEditModal(): void
    {
        $user = Auth::user();
        $this->editBio      = $user->bio ?? '';
        $this->editPosition = $user->position ?? '';
        $this->editModal    = true;
    }

    public function saveProfile(): void
    {
        $this->validate([
            'editBio'      => 'nullable|string|max:300',
            'editPosition' => 'nullable|string|max:100',
        ], [
            'editBio.max'      => 'A bio pode ter no máximo 300 caracteres.',
            'editPosition.max' => 'O cargo pode ter no máximo 100 caracteres.',
        ]);

        Auth::user()->update([
            'bio'      => $this->sanitize($this->editBio),
            'position' => $this->sanitize($this->editPosition),
        ]);

        $this->editModal = false;
        unset($this->profileUser);
        $this->alertSuccess('Perfil atualizado!');
    }

    #[Computed]
    public function profileUser(): User
    {
        return User::with(['accessProfile', 'department', 'feedPosts'])->withCount([
            'feedPosts',
            'followers',
            'following',
        ])->findOrFail($this->userId);
    }

    #[Computed]
    public function posts()
    {
        return FeedPost::with(['user', 'images', 'poll.options.votes', 'reactions', 'comments'])
            ->withCount(['reactions as like_count' => fn ($q) => $q->where('type', 'like'), 'comments'])
            ->where('user_id', $this->userId)
            ->latest()
            ->paginate(12);
    }

    #[Computed]
    public function isFollowing(): bool
    {
        return Auth::user()->isFollowing($this->userId);
    }

    #[Computed]
    public function isOwnProfile(): bool
    {
        return Auth::id() === $this->userId;
    }

    public function toggleFollow(): void
    {
        if ($this->isOwnProfile) return;

        $user = Auth::user();
        if ($user->isFollowing($this->userId)) {
            $user->following()->detach($this->userId);
        } else {
            $user->following()->attach($this->userId);
        }
        unset($this->isFollowing, $this->profileUser);
    }

    public function render()
    {
        return view('livewire.pages.feed.profile')
            ->layout('components.layouts.app', ['title' => 'Perfil']);
    }
}
