<?php

namespace App\Livewire\Pages\Communities;

use App\Livewire\SecureComponent;
use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;

class Index extends SecureComponent
{
    use WithFileUploads;

    // ── Tab ───────────────────────────────────────────────────────────
    public string $activeTab   = 'discover';  // discover | mine | pending
    public string $searchQuery = '';

    // ── Criar comunidade ──────────────────────────────────────────────
    public bool   $createModal       = false;
    public string $newName           = '';
    public string $newDescription    = '';
    public ?int   $newDepartmentId   = null;
    public bool   $newIsPrivate      = true;
    public $newCoverImage            = null;

    public function mount(): void
    {
        $this->requireAuth();
    }

    // ── Computeds ─────────────────────────────────────────────────────

    #[Computed]
    public function canCreate(): bool
    {
        $slug = Auth::user()->accessProfile?->slug ?? '';
        return in_array($slug, ['manager', 'hr', 'admin']);
    }

    #[Computed]
    public function departments()
    {
        return Department::orderBy('name')->get();
    }

    #[Computed]
    public function discoverCommunities()
    {
        $userId = Auth::id();

        return Community::withCount(['acceptedMembers', 'posts'])
            ->with(['department', 'creator'])
            ->when($this->searchQuery, fn ($q) =>
                $q->where('name', 'like', '%'.$this->searchQuery.'%')
                  ->orWhere('description', 'like', '%'.$this->searchQuery.'%')
            )
            ->whereDoesntHave('members', fn ($q) =>
                $q->where('user_id', $userId)->where('status', 'accepted')
            )
            ->latest()
            ->get();
    }

    #[Computed]
    public function myCommunities()
    {
        $userId = Auth::id();

        return Community::withCount(['acceptedMembers', 'posts'])
            ->with(['department'])
            ->whereHas('members', fn ($q) =>
                $q->where('user_id', $userId)->where('status', 'accepted')
            )
            ->latest()
            ->get();
    }

    #[Computed]
    public function pendingRequests()
    {
        $userId = Auth::id();

        return CommunityMember::with(['community.department'])
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->latest()
            ->get();
    }

    #[Computed]
    public function myMemberships(): array
    {
        return CommunityMember::where('user_id', Auth::id())
            ->pluck('status', 'community_id')
            ->toArray();
    }

    // ── Criar comunidade ──────────────────────────────────────────────

    public function createCommunity(): void
    {
        if (! $this->canCreate) {
            $this->alertError('Sem permissão para criar comunidades.');
            return;
        }

        $this->validate([
            'newName'        => 'required|string|min:3|max:80',
            'newDescription' => 'nullable|string|max:500',
            'newCoverImage'  => 'nullable|image|max:4096',
            'newDepartmentId'=> 'nullable|exists:departments,id',
        ], [
            'newName.required' => 'O nome da comunidade é obrigatório.',
            'newName.min'      => 'O nome precisa ter pelo menos 3 caracteres.',
        ]);

        $coverPath = null;
        if ($this->newCoverImage) {
            $coverPath = $this->newCoverImage->store('communities/covers', 'public');
        }

        $community = Community::create([
            'name'          => $this->sanitize($this->newName),
            'description'   => $this->sanitize($this->newDescription ?? ''),
            'cover_image'   => $coverPath,
            'created_by'    => Auth::id(),
            'department_id' => $this->newDepartmentId,
            'is_private'    => $this->newIsPrivate,
        ]);

        // Criador entra como admin automaticamente
        CommunityMember::create([
            'community_id' => $community->id,
            'user_id'      => Auth::id(),
            'role'         => 'admin',
            'status'       => 'accepted',
            'joined_at'    => now(),
        ]);

        $this->reset(['newName', 'newDescription', 'newCoverImage', 'newDepartmentId', 'newIsPrivate']);
        $this->newIsPrivate = true;
        $this->createModal  = false;
        unset($this->discoverCommunities, $this->myCommunities, $this->myMemberships);
        $this->alertSuccess('Comunidade criada!', 'Você já é o administrador.');
    }

    // ── Solicitar entrada ─────────────────────────────────────────────

    public function requestJoin(int $communityId): void
    {
        $existing = CommunityMember::where('community_id', $communityId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $this->alertError('Você já solicitou entrada ou já é membro.');
            return;
        }

        $community = Community::findOrFail($communityId);

        CommunityMember::create([
            'community_id' => $communityId,
            'user_id'      => Auth::id(),
            'role'         => 'member',
            'status'       => $community->is_private ? 'pending' : 'accepted',
            'joined_at'    => $community->is_private ? null : now(),
        ]);

        unset($this->discoverCommunities, $this->myCommunities, $this->myMemberships, $this->pendingRequests);

        if ($community->is_private) {
            $this->alertSuccess('Solicitação enviada!', 'Aguarde a aprovação do administrador.');
        } else {
            $this->alertSuccess('Você entrou na comunidade!');
        }
    }

    // ── Cancelar solicitação ──────────────────────────────────────────

    public function cancelRequest(int $communityId): void
    {
        CommunityMember::where('community_id', $communityId)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->delete();

        unset($this->discoverCommunities, $this->myMemberships, $this->pendingRequests);
        $this->alertSuccess('Solicitação cancelada.');
    }

    public function render()
    {
        return view('livewire.pages.communities.index')
            ->layout('components.layouts.app', ['title' => 'Comunidades']);
    }
}
