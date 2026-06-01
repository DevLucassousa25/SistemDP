<?php

namespace App\Livewire\Pages\Communities;

use App\Livewire\SecureComponent;
use App\Models\Community;
use App\Models\CommunityEventRsvp;
use App\Models\CommunityMember;
use App\Models\CommunityPost;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostReaction;
use App\Models\CommunityPoll;
use App\Models\CommunityPollOption;
use App\Models\CommunityPollVote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\WithFileUploads;

class Show extends SecureComponent
{
    use WithFileUploads;

    #[Locked]
    public string $slug;

    // ── Criar post ────────────────────────────────────────────────────
    public string $newPostContent  = '';
    public $newPostImages          = [];
    public bool   $postBoxExpanded = false;
    public bool   $showPollCreator = false;
    public string $pollQuestion    = '';
    public array  $pollOptions     = ['', ''];
    public string $pollDuration    = '7';
    public string $newPostType     = 'post';   // post | aviso | evento | discussao
    public string $eventLocation   = '';
    public string $eventDate       = '';
    public string $eventEndsAt     = '';

    // ── Feed ──────────────────────────────────────────────────────────
    public int    $loadedCount   = 15;
    public array  $openComments  = [];

    // ── Modais ────────────────────────────────────────────────────────
    public bool   $membersModal       = false;
    public bool   $requestsModal      = false;
    public bool   $statsModal         = false;
    public bool   $editCommunityModal = false;
    public string $editName           = '';
    public string $editDescription    = '';
    public string $editRules          = '';
    public $editCoverImage         = null;

    // ── Editar / Excluir post ─────────────────────────────────────────
    public bool   $editPostModal = false;
    public string $editContent   = '';
    #[Locked]
    public ?int   $editPostId    = null;
    public bool   $deletePostModal = false;
    #[Locked]
    public ?int   $deletePostId  = null;

    public function mount(string $slug): void
    {
        $this->requireAuth();
        $this->slug = $slug;

        // Garante que o usuário é membro aceito (ou admin do sistema)
        $community = Community::where('slug', $slug)->firstOrFail();
        $sysAdmin  = in_array(Auth::user()->accessProfile?->slug ?? '', ['hr', 'admin']);

        if (! $sysAdmin && ! $community->isMember(Auth::id())) {
            abort(403, 'Você não é membro desta comunidade.');
        }

        $this->dispatch('breadcrumb-set', items: [
            ['label' => 'Comunidades', 'icon' => 'users', 'url' => route('communities')],
            ['label' => $community->name, 'url' => null],
        ]);
    }

    // ── Computeds ─────────────────────────────────────────────────────

    #[Computed]
    public function community(): Community
    {
        return Community::with(['department', 'creator'])
            ->withCount(['acceptedMembers'])
            ->where('slug', $this->slug)
            ->firstOrFail();
    }

    #[Computed]
    public function myMembership(): ?CommunityMember
    {
        return CommunityMember::where('community_id', $this->community->id)
            ->where('user_id', Auth::id())
            ->first();
    }

    #[Computed]
    public function isAdmin(): bool
    {
        $sysAdmin = in_array(Auth::user()->accessProfile?->slug ?? '', ['hr', 'admin']);
        return $sysAdmin || $this->community->isAdmin(Auth::id());
    }

    #[Computed]
    public function pinnedPosts()
    {
        return CommunityPost::with(['user', 'images', 'reactions', 'poll.options.votes'])
            ->withCount(['reactions as like_count' => fn ($q) => $q->where('type', 'like'), 'comments'])
            ->where('community_id', $this->community->id)
            ->where('is_pinned', true)
            ->latest()
            ->get();
    }

    #[Computed]
    public function feedItems()
    {
        return CommunityPost::with([
                'user',
                'images',
                'poll.options.votes',
                'rsvps',
                'reactions' => fn ($q) => $q->where('user_id', Auth::id()),
                'comments'  => fn ($q) => $q->with(['user', 'replies.user'])->latest()->limit(5),
            ])
            ->withCount([
                'reactions as like_count'  => fn ($q) => $q->where('type', 'like'),
                'reactions as heart_count' => fn ($q) => $q->where('type', 'heart'),
                'reactions as clap_count'  => fn ($q) => $q->where('type', 'clap'),
                'comments',
            ])
            ->where('community_id', $this->community->id)
            ->where('is_pinned', false)
            ->latest()
            ->take($this->loadedCount)
            ->get();
    }

    #[Computed]
    public function totalPostCount(): int
    {
        return CommunityPost::where('community_id', $this->community->id)
            ->where('is_pinned', false)
            ->count();
    }

    #[Computed]
    public function acceptedMembers()
    {
        return CommunityMember::with('user.accessProfile')
            ->where('community_id', $this->community->id)
            ->where('status', 'accepted')
            ->orderBy('role')
            ->get();
    }

    #[Computed]
    public function pendingMembers()
    {
        return CommunityMember::with('user')
            ->where('community_id', $this->community->id)
            ->where('status', 'pending')
            ->latest()
            ->get();
    }

    #[Computed]
    public function pendingCount(): int
    {
        return CommunityMember::where('community_id', $this->community->id)
            ->where('status', 'pending')
            ->count();
    }

    // ── Computeds extras ──────────────────────────────────────────────

    #[Computed]
    public function communityStats(): array
    {
        $cid = $this->community->id;

        $typeCounts = CommunityPost::where('community_id', $cid)
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        $topPoster = CommunityPost::where('community_id', $cid)
            ->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with('user')
            ->first();

        return [
            'total_posts'    => CommunityPost::where('community_id', $cid)->count(),
            'posts_week'     => CommunityPost::where('community_id', $cid)
                                    ->where('created_at', '>=', now()->startOfWeek())->count(),
            'active_members' => CommunityPost::where('community_id', $cid)
                                    ->where('created_at', '>=', now()->subDays(30))
                                    ->distinct('user_id')->count('user_id'),
            'types'          => $typeCounts,
            'top_poster'     => $topPoster,
        ];
    }

    // ── Criar post ────────────────────────────────────────────────────

    public function createPost(): void
    {
        // Limpa arquivos temporários inválidos/corrompidos antes de validar
        if (!empty($this->newPostImages)) {
            $valid = [];
            foreach ((array) $this->newPostImages as $img) {
                try {
                    if ($img && method_exists($img, 'getSize') && $img->getSize() !== false) {
                        $valid[] = $img;
                    }
                } catch (\Throwable) {
                    // arquivo temporário inválido — ignora silenciosamente
                }
            }
            $this->newPostImages = $valid;
        }

        $rules = [
            'newPostContent'  => 'required|string|min:1|max:2000',
            'newPostImages'   => 'nullable|array|max:4',
            'newPostImages.*' => 'image|max:4096',
            'newPostType'     => 'required|in:post,aviso,evento,discussao',
            'pollQuestion'    => 'nullable|string|max:200',
            'pollOptions'     => 'nullable|array|min:2|max:6',
            'pollOptions.*'   => 'nullable|string|max:100',
            'pollDuration'    => 'nullable|integer|min:0|max:30',
        ];

        if ($this->newPostType === 'evento') {
            $rules['eventDate']     = 'required|date';
            $rules['eventLocation'] = 'nullable|string|max:200';
            $rules['eventEndsAt']   = 'nullable|date|after:eventDate';
        }

        $this->validate($rules);

        $postData = [
            'community_id' => $this->community->id,
            'user_id'      => Auth::id(),
            'content'      => $this->sanitize($this->newPostContent),
            'type'         => $this->newPostType,
        ];

        if ($this->newPostType === 'evento') {
            $postData['event_date']     = $this->eventDate;
            $postData['event_ends_at']  = $this->eventEndsAt ?: null;
            $postData['event_location'] = $this->sanitize($this->eventLocation);
        }

        $post = CommunityPost::create($postData);

        foreach (($this->newPostImages ?? []) as $order => $img) {
            $path = $img->store('communities/posts', 'public');
            $post->images()->create(['path' => $path, 'order' => $order]);
        }

        if ($this->showPollCreator && trim($this->pollQuestion)) {
            $options = array_filter($this->pollOptions, fn ($o) => trim($o));
            if (count($options) >= 2) {
                $poll = $post->poll()->create([
                    'question'        => $this->sanitize($this->pollQuestion),
                    'allows_multiple' => false,
                    'ends_at'         => $this->pollDuration > 0
                                            ? now()->addDays((int) $this->pollDuration) : null,
                ]);
                foreach (array_values($options) as $i => $label) {
                    $poll->options()->create(['label' => $this->sanitize($label), 'order' => $i]);
                }
            }
        }

        $this->reset(['newPostContent', 'newPostImages', 'postBoxExpanded', 'showPollCreator',
                      'pollQuestion', 'pollOptions', 'pollDuration',
                      'newPostType', 'eventLocation', 'eventDate', 'eventEndsAt']);
        $this->pollOptions  = ['', ''];
        $this->pollDuration = '7';
        $this->newPostType  = 'post';
        unset($this->feedItems, $this->totalPostCount);
    }

    public function addPollOption(): void
    {
        if (count($this->pollOptions) < 6) $this->pollOptions[] = '';
    }

    public function removePollOption(int $index): void
    {
        if (count($this->pollOptions) > 2) {
            array_splice($this->pollOptions, $index, 1);
            $this->pollOptions = array_values($this->pollOptions);
        }
    }

    // ── Pin / Unpin ───────────────────────────────────────────────────

    public function togglePin(int $postId): void
    {
        if (! $this->isAdmin) return;

        $post = CommunityPost::where('id', $postId)
            ->where('community_id', $this->community->id)
            ->firstOrFail();

        $post->update(['is_pinned' => ! $post->is_pinned]);
        unset($this->feedItems, $this->pinnedPosts);
    }

    // ── Editar post ───────────────────────────────────────────────────

    public function openEditPost(int $id): void
    {
        $post = CommunityPost::where('id', $id)
            ->where('community_id', $this->community->id)
            ->firstOrFail();

        if ($post->user_id !== Auth::id() && ! $this->isAdmin) abort(403);

        $this->editPostId   = $id;
        $this->editContent  = $post->content;
        $this->editPostModal = true;
    }

    public function saveEditPost(): void
    {
        $this->validate(['editContent' => 'required|string|min:1|max:2000']);

        CommunityPost::where('id', $this->editPostId)
            ->where('community_id', $this->community->id)
            ->update(['content' => $this->sanitize($this->editContent), 'edited_at' => now()]);

        $this->editPostModal = false;
        $this->editPostId    = null;
        $this->editContent   = '';
        unset($this->feedItems);
    }

    // ── Excluir post ──────────────────────────────────────────────────

    public function confirmDeletePost(int $id): void
    {
        $this->deletePostId    = $id;
        $this->deletePostModal = true;
    }

    public function deletePost(): void
    {
        $post = CommunityPost::where('id', $this->deletePostId)
            ->where('community_id', $this->community->id)
            ->firstOrFail();

        if ($post->user_id !== Auth::id() && ! $this->isAdmin) abort(403);

        foreach ($post->images as $img) Storage::disk('public')->delete($img->path);
        $post->delete();

        $this->deletePostModal = false;
        $this->deletePostId    = null;
        unset($this->feedItems, $this->totalPostCount);
        $this->alertSuccess('Post excluído.');
    }

    // ── Reações ───────────────────────────────────────────────────────

    public function toggleReaction(int $postId, string $type): void
    {
        $existing = CommunityPostReaction::where('community_post_id', $postId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->type === $type ? $existing->delete() : $existing->update(['type' => $type]);
        } else {
            CommunityPostReaction::create(['community_post_id' => $postId, 'user_id' => Auth::id(), 'type' => $type]);
        }
        unset($this->feedItems);
    }

    // ── Comentários ───────────────────────────────────────────────────

    public function toggleComments(int $postId): void
    {
        if (in_array($postId, $this->openComments)) {
            $this->openComments = array_values(array_filter($this->openComments, fn ($k) => $k !== $postId));
        } else {
            $this->openComments[] = $postId;
        }
    }

    public function submitComment(int $postId, string $text = '', ?int $parentId = null): void
    {
        $text = trim($text);
        if (! $text || strlen($text) > 1000) return;

        CommunityPostComment::create([
            'community_post_id' => $postId,
            'user_id'           => Auth::id(),
            'parent_id'         => $parentId,
            'content'           => $this->sanitize($text),
        ]);

        $this->dispatch('comment-saved', postId: $postId);
        unset($this->feedItems);
    }

    public function deleteComment(int $commentId): void
    {
        $comment = CommunityPostComment::findOrFail($commentId);
        if ($comment->user_id !== Auth::id() && ! $this->isAdmin) abort(403);
        $comment->delete();
        unset($this->feedItems);
    }

    // ── Enquete ───────────────────────────────────────────────────────

    public function votePoll(int $pollOptionId): void
    {
        $option = CommunityPollOption::with('poll')->findOrFail($pollOptionId);
        $poll   = $option->poll;

        if ($poll->is_expired) {
            $this->alertError('Esta enquete já encerrou.');
            return;
        }

        $existing = CommunityPollVote::where('community_poll_option_id', $pollOptionId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            if (! $poll->allows_multiple) {
                CommunityPollVote::whereHas('option', fn ($q) =>
                    $q->where('community_poll_id', $poll->id)
                )->where('user_id', Auth::id())->delete();
            }
            CommunityPollVote::create(['community_poll_option_id' => $pollOptionId, 'user_id' => Auth::id()]);
        }
        unset($this->feedItems);
    }

    // ── Load more ─────────────────────────────────────────────────────

    public function loadMore(): void
    {
        $this->loadedCount += 10;
        unset($this->feedItems);
    }

    // ── Gerenciar membros (admin) ─────────────────────────────────────

    public function approveMember(int $memberId): void
    {
        if (! $this->isAdmin) return;

        CommunityMember::where('id', $memberId)
            ->where('community_id', $this->community->id)
            ->update(['status' => 'accepted', 'joined_at' => now()]);

        unset($this->pendingMembers, $this->acceptedMembers, $this->pendingCount);
        $this->alertSuccess('Membro aprovado!');
    }

    public function rejectMember(int $memberId): void
    {
        if (! $this->isAdmin) return;

        CommunityMember::where('id', $memberId)
            ->where('community_id', $this->community->id)
            ->delete();

        unset($this->pendingMembers, $this->pendingCount);
        $this->alertSuccess('Solicitação rejeitada.');
    }

    public function removeMember(int $memberId): void
    {
        if (! $this->isAdmin) return;

        $member = CommunityMember::where('id', $memberId)
            ->where('community_id', $this->community->id)
            ->where('role', '!=', 'admin')
            ->firstOrFail();

        $member->delete();
        unset($this->acceptedMembers);
        $this->alertSuccess('Membro removido.');
    }

    public function promoteToAdmin(int $memberId): void
    {
        if (! $this->isAdmin) return;

        CommunityMember::where('id', $memberId)
            ->where('community_id', $this->community->id)
            ->update(['role' => 'admin']);

        unset($this->acceptedMembers);
        $this->alertSuccess('Membro promovido a administrador.');
    }

    // ── Editar comunidade (admin) ─────────────────────────────────────

    // ── RSVP evento ───────────────────────────────────────────────────

    public function rsvp(int $postId, string $status): void
    {
        $allowed = ['going', 'not_going', 'maybe'];
        if (! in_array($status, $allowed)) return;

        $existing = CommunityEventRsvp::where('community_post_id', $postId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->status === $status ? $existing->delete() : $existing->update(['status' => $status]);
        } else {
            CommunityEventRsvp::create([
                'community_post_id' => $postId,
                'user_id'           => Auth::id(),
                'status'            => $status,
            ]);
        }

        unset($this->feedItems, $this->pinnedPosts);
    }

    public function openEditCommunity(): void
    {
        if (! $this->isAdmin) return;
        $this->editName        = $this->community->name;
        $this->editDescription = $this->community->description ?? '';
        $this->editRules       = $this->community->rules ?? '';
        $this->editCommunityModal = true;
    }

    public function saveEditCommunity(): void
    {
        if (! $this->isAdmin) return;

        $this->validate([
            'editName'        => 'required|string|min:3|max:80',
            'editDescription' => 'nullable|string|max:500',
            'editRules'       => 'nullable|string|max:1000',
            'editCoverImage'  => 'nullable|image|max:4096',
        ]);

        $data = [
            'name'        => $this->sanitize($this->editName),
            'description' => $this->sanitize($this->editDescription ?? ''),
            'rules'       => $this->sanitize($this->editRules ?? ''),
        ];

        if ($this->editCoverImage) {
            if ($this->community->cover_image) {
                Storage::disk('public')->delete($this->community->cover_image);
            }
            $data['cover_image'] = $this->editCoverImage->store('communities/covers', 'public');
        }

        $this->community->update($data);
        $this->editCommunityModal = false;
        $this->editCoverImage     = null;
        unset($this->community);
        $this->alertSuccess('Comunidade atualizada!');
    }

    // ── Sair da comunidade ────────────────────────────────────────────

    public function leaveCommunity(): void
    {
        if ($this->community->isAdmin(Auth::id())) {
            $this->alertError('Administradores não podem sair. Transfira a liderança primeiro.');
            return;
        }

        CommunityMember::where('community_id', $this->community->id)
            ->where('user_id', Auth::id())
            ->delete();

        $this->redirectRoute('communities', navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.communities.show')
            ->layout('components.layouts.app', ['title' => $this->community->name]);
    }
}
