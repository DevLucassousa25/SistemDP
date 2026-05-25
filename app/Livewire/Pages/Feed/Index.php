<?php

namespace App\Livewire\Pages\Feed;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Livewire\SecureComponent;
use App\Notifications\FeedInteracaoNotification;
use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\CommunityPost;
use App\Models\CommunityPostReaction;
use App\Models\FeedPost;
use App\Models\FeedPostBookmark;
use App\Models\FeedPostComment;
use App\Models\FeedPostCommentReaction;
use App\Models\FeedPostRead;
use App\Models\FeedPostReaction;
use App\Models\FeedPoll;
use App\Models\FeedPollVote;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostReaction;
use App\Models\PostRead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\WithFileUploads;

class Index extends SecureComponent
{
    use EnviaNotificacoes;
    use WithFileUploads;

    // ── Criar post ────────────────────────────────────────────────────
    public string $newPostContent    = '';
    public $newPostImages            = [];   // múltiplas imagens
    public bool   $postBoxExpanded   = false;
    public bool   $showPollCreator   = false;
    public string $pollQuestion      = '';
    public array  $pollOptions       = ['', ''];
    public string $pollDuration      = '7';  // dias (0 = sem expiração)

    // ── Feed ──────────────────────────────────────────────────────────
    public int    $loadedCount    = 15;
    public int    $newPostsBanner = 0;
    public string $activeFilter   = 'all';   // all | official | users | following | bookmarks | communities
    public string $searchQuery    = '';
    public string $sortBy         = 'recent'; // recent | popular

    // ── Comentários ───────────────────────────────────────────────────
    public array  $openComments  = [];
    public string $newComment    = '';
    public array  $replyingTo    = [];    // [commentId => true]
    public array  $replyTexts    = [];    // [commentId => string]

    // ── Preview post oficial ──────────────────────────────────────────
    public bool $previewOpen   = false;
    #[Locked]
    public ?int $previewPostId = null;

    // ── Exclusão de feed post ─────────────────────────────────────────
    public bool $deleteModal    = false;
    #[Locked]
    public ?int $deleteFeedPostId = null;

    // ── Edição de feed post ───────────────────────────────────────────
    public bool   $editModal      = false;
    public string $editContent    = '';
    #[Locked]
    public ?int   $editPostId     = null;

    // ── Repost ───────────────────────────────────────────────────────
    public bool   $repostModal   = false;
    public string $repostComment = '';
    #[Locked]
    public ?int $repostTargetId  = null;

    // ── Enquete ───────────────────────────────────────────────────────
    public bool $pollResultsModal   = false;
    #[Locked]
    public ?int $pollResultsPostId  = null;

    // ── Ciclo de vida ─────────────────────────────────────────────────

    public function mount(): void
    {
        $this->requireAuth();
    }

    // ── Computeds ─────────────────────────────────────────────────────

    #[Computed]
    public function pinnedPosts()
    {
        return Post::with(['author', 'tags'])
            ->published()
            ->where('pinned', true)
            ->where(fn ($q) => $q->where('target_audience', 'todos')
                ->orWhere('target_audience', $this->currentUserAudience()))
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();
    }

    #[Computed]
    public function feedItems(): \Illuminate\Support\Collection
    {
        $audience  = $this->currentUserAudience();
        $isRhAdmin = in_array(Auth::user()->accessProfile?->slug ?? '', ['hr', 'admin']);
        $userId    = Auth::id();

        // ── Posts oficiais ────────────────────────────────────────────
        $official = collect();
        if (in_array($this->activeFilter, ['all', 'official'])) {
            $official = Post::with([
                    'author', 'tags', 'reads',
                    'reactions' => fn ($q) => $q->where('user_id', $userId),
                    'comments'  => fn ($q) => $q->with('user')->latest()->limit(5),
                ])
                ->withCount([
                    'reactions as like_count'  => fn ($q) => $q->where('type', 'like'),
                    'reactions as heart_count' => fn ($q) => $q->where('type', 'heart'),
                    'reactions as clap_count'  => fn ($q) => $q->where('type', 'clap'),
                    'comments',
                ])
                ->published()
                ->where('pinned', false)
                ->when(! $isRhAdmin, fn ($q) => $q->where(fn ($inner) =>
                    $inner->where('target_audience', 'todos')
                          ->orWhere('target_audience', $audience)
                ))
                ->when($this->searchQuery, fn ($q) =>
                    $q->where(fn ($i) =>
                        $i->where('title', 'like', '%'.$this->searchQuery.'%')
                          ->orWhere('content', 'like', '%'.$this->searchQuery.'%')
                    )
                )
                ->orderByDesc('published_at')
                ->limit(40)
                ->get()
                ->map(fn ($p) => [
                    'key'      => 'official_' . $p->id,
                    'type'     => 'official',
                    'post'     => $p,
                    'sortDate' => $p->published_at ?? $p->created_at,
                    'popular'  => $p->like_count + $p->heart_count + $p->clap_count + $p->comments_count,
                ]);
        }

        // ── Posts sociais ─────────────────────────────────────────────
        $userPosts = collect();
        if (! in_array($this->activeFilter, ['official', 'communities'])) {
            $userPosts = FeedPost::with([
                    'user',
                    'images',
                    'poll.options.votes',
                    'repostedFrom.user',
                    'repostedFrom.images',
                    'repostedFrom.reactions',
                    'reactions' => fn ($q) => $q->where('user_id', $userId),
                    'bookmarks' => fn ($q) => $q->where('user_id', $userId),
                    'comments'  => fn ($q) => $q->with(['user', 'replies.user', 'reactions'])
                                                ->latest()->limit(5),
                ])
                ->withCount([
                    'reactions as like_count'  => fn ($q) => $q->where('type', 'like'),
                    'reactions as heart_count' => fn ($q) => $q->where('type', 'heart'),
                    'reactions as clap_count'  => fn ($q) => $q->where('type', 'clap'),
                    'comments',
                    'reposts',
                ])
                ->when($this->activeFilter === 'following', function ($q) use ($userId) {
                    $followingIds = Auth::user()->following()->pluck('users.id')->toArray();
                    $followingIds[] = $userId;
                    $q->whereIn('user_id', $followingIds);
                })
                ->when($this->activeFilter === 'bookmarks', fn ($q) =>
                    $q->whereHas('bookmarks', fn ($b) => $b->where('user_id', $userId))
                )
                ->when(in_array($this->activeFilter, ['all', 'users']), fn ($q) => $q)
                ->when($this->searchQuery, fn ($q) =>
                    $q->where('content', 'like', '%'.$this->searchQuery.'%')
                )
                ->orderByDesc('created_at')
                ->limit(60)
                ->get()
                ->map(fn ($p) => [
                    'key'      => 'user_' . $p->id,
                    'type'     => 'user',
                    'post'     => $p,
                    'sortDate' => $p->created_at,
                    'popular'  => $p->like_count + $p->heart_count + $p->clap_count + $p->comments_count,
                ]);
        }

        // ── Posts de comunidades ──────────────────────────────────────
        $communityPosts = collect();
        if (in_array($this->activeFilter, ['all', 'communities'])) {
            $myCommunityIds = CommunityMember::where('user_id', $userId)
                ->where('status', 'accepted')
                ->pluck('community_id')
                ->toArray();

            if (! empty($myCommunityIds)) {
                $communityPosts = CommunityPost::with([
                        'user',
                        'community',
                        'images',
                        'poll.options.votes',
                        'reactions' => fn ($q) => $q->where('user_id', $userId),
                        'comments'  => fn ($q) => $q->with(['user', 'replies.user'])->latest()->limit(3),
                    ])
                    ->withCount([
                        'reactions as like_count'  => fn ($q) => $q->where('type', 'like'),
                        'reactions as heart_count' => fn ($q) => $q->where('type', 'heart'),
                        'reactions as clap_count'  => fn ($q) => $q->where('type', 'clap'),
                        'comments',
                    ])
                    ->whereIn('community_id', $myCommunityIds)
                    ->when($this->searchQuery, fn ($q) =>
                        $q->where('content', 'like', '%'.$this->searchQuery.'%')
                    )
                    ->orderByDesc('created_at')
                    ->limit(40)
                    ->get()
                    ->map(fn ($p) => [
                        'key'      => 'community_' . $p->id,
                        'type'     => 'community',
                        'post'     => $p,
                        'sortDate' => $p->created_at,
                        'popular'  => $p->like_count + $p->heart_count + $p->clap_count + $p->comments_count,
                    ]);
            }
        }

        $merged = $official->concat($userPosts)->concat($communityPosts);

        if ($this->sortBy === 'popular') {
            $merged = $merged->sortByDesc('popular');
        } else {
            $merged = $merged->sortByDesc(fn ($item) => $item['sortDate']->timestamp);
        }

        return $merged->values()->take($this->loadedCount);
    }

    #[Computed]
    public function totalFeedCount(): int
    {
        $audience  = $this->currentUserAudience();
        $isRhAdmin = in_array(Auth::user()->accessProfile?->slug ?? '', ['hr', 'admin']);

        $official = Post::published()->where('pinned', false)
            ->when(! $isRhAdmin, fn ($q) => $q->where(fn ($inner) =>
                $inner->where('target_audience', 'todos')->orWhere('target_audience', $audience)
            ))
            ->count();

        return $official + FeedPost::count();
    }

    #[Computed]
    public function unreadOfficialCount(): int
    {
        $audience  = $this->currentUserAudience();
        $isRhAdmin = in_array(Auth::user()->accessProfile?->slug ?? '', ['hr', 'admin']);

        return Post::published()
            ->where('pinned', false)
            ->when(! $isRhAdmin, fn ($q) => $q->where(fn ($inner) =>
                $inner->where('target_audience', 'todos')
                      ->orWhere('target_audience', $audience)
            ))
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', Auth::id()))
            ->count();
    }

    #[Computed]
    public function previewPost(): ?Post
    {
        if (! $this->previewPostId) return null;
        return Post::with(['author', 'tags', 'reads.user', 'reactions.user', 'comments.user'])->find($this->previewPostId);
    }

    #[Computed]
    public function recentOfficial()
    {
        return Post::with(['author'])
            ->published()
            ->where(fn ($q) => $q->where('target_audience', 'todos')
                ->orWhere('target_audience', $this->currentUserAudience()))
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function myCommunities()
    {
        return Community::withCount('posts')
            ->whereHas('members', fn ($q) =>
                $q->where('user_id', Auth::id())->where('status', 'accepted')
            )
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function suggestedUsers()
    {
        $followingIds = Auth::user()->following()->pluck('users.id')->toArray();
        $followingIds[] = Auth::id();

        return User::whereNotIn('id', $followingIds)
            ->withCount('feedPosts')
            ->orderByDesc('feed_posts_count')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function myFollowingIds(): array
    {
        return Auth::user()->following()->pluck('users.id')->toArray();
    }

    // ── Criar post social ─────────────────────────────────────────────

    public function createPost(): void
    {
        $this->validate([
            'newPostContent'   => 'required|string|min:1|max:2000',
            'newPostImages'    => 'nullable|array|max:4',
            'newPostImages.*'  => 'image|max:4096',
            'pollQuestion'     => 'nullable|string|max:200',
            'pollOptions'      => 'nullable|array|min:2|max:6',
            'pollOptions.*'    => 'nullable|string|max:100',
            'pollDuration'     => 'nullable|integer|min:0|max:30',
        ], [
            'newPostContent.required' => 'Escreva algo para publicar.',
            'newPostContent.max'      => 'O post pode ter no máximo 2000 caracteres.',
            'newPostImages.max'       => 'Você pode enviar no máximo 4 imagens.',
            'newPostImages.*.image'   => 'O arquivo deve ser uma imagem.',
            'newPostImages.*.max'     => 'Cada imagem não pode ultrapassar 4 MB.',
        ]);

        $post = FeedPost::create([
            'user_id'  => Auth::id(),
            'content'  => $this->sanitize($this->newPostContent),
            'edited_at' => null,
        ]);

        // Salvar imagens
        foreach (($this->newPostImages ?? []) as $order => $img) {
            $path = $img->store('feed/images', 'public');
            $post->images()->create(['path' => $path, 'order' => $order]);
        }

        // Criar enquete se preenchida
        if ($this->showPollCreator && trim($this->pollQuestion)) {
            $options = array_filter($this->pollOptions, fn ($o) => trim($o));
            if (count($options) >= 2) {
                $poll = $post->poll()->create([
                    'question'        => $this->sanitize($this->pollQuestion),
                    'allows_multiple' => false,
                    'ends_at'         => $this->pollDuration > 0
                                            ? now()->addDays((int) $this->pollDuration)
                                            : null,
                ]);
                foreach (array_values($options) as $i => $label) {
                    $poll->options()->create(['label' => $this->sanitize($label), 'order' => $i]);
                }
            }
        }

        $this->reset(['newPostContent', 'newPostImages', 'postBoxExpanded',
                      'showPollCreator', 'pollQuestion', 'pollOptions', 'pollDuration']);
        $this->pollOptions = ['', ''];
        $this->pollDuration = '7';
        unset($this->feedItems, $this->totalFeedCount);
        $this->dispatch('post-created');
    }

    public function addPollOption(): void
    {
        if (count($this->pollOptions) < 6) {
            $this->pollOptions[] = '';
        }
    }

    public function removePollOption(int $index): void
    {
        if (count($this->pollOptions) > 2) {
            array_splice($this->pollOptions, $index, 1);
            $this->pollOptions = array_values($this->pollOptions);
        }
    }

    // ── Editar post ───────────────────────────────────────────────────

    public function openEditModal(int $id): void
    {
        $post = FeedPost::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $this->editPostId  = $id;
        $this->editContent = $post->content;
        $this->editModal   = true;
    }

    public function saveEdit(): void
    {
        if (! $this->editPostId) return;

        $this->validate(
            ['editContent' => 'required|string|min:1|max:2000'],
            ['editContent.required' => 'O post não pode ficar vazio.']
        );

        FeedPost::where('id', $this->editPostId)
            ->where('user_id', Auth::id())
            ->update([
                'content'   => $this->sanitize($this->editContent),
                'edited_at' => now(),
            ]);

        $this->editModal   = false;
        $this->editPostId  = null;
        $this->editContent = '';
        unset($this->feedItems);
    }

    // ── Deletar post ──────────────────────────────────────────────────

    public function confirmDeleteFeedPost(int $id): void
    {
        $this->deleteFeedPostId = $id;
        $this->deleteModal      = true;
    }

    public function deleteFeedPost(): void
    {
        if (! $this->deleteFeedPostId) return;

        $post = FeedPost::where('id', $this->deleteFeedPostId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($post->image) Storage::disk('public')->delete($post->image);
        foreach ($post->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $post->delete();

        $this->deleteModal      = false;
        $this->deleteFeedPostId = null;
        $this->alertSuccess('Post excluído.');
        unset($this->feedItems, $this->totalFeedCount);
    }

    // ── Repost ───────────────────────────────────────────────────────

    public function openRepostModal(int $feedPostId): void
    {
        $this->repostTargetId = $feedPostId;
        $this->repostComment  = '';
        $this->repostModal    = true;
    }

    public function repost(): void
    {
        if (! $this->repostTargetId) return;

        $alreadyReposted = FeedPost::where('user_id', Auth::id())
            ->where('repost_of_id', $this->repostTargetId)
            ->exists();

        if ($alreadyReposted) {
            $this->repostModal = false;
            $this->alertError('Você já repostou esse post.');
            return;
        }

        $this->validate(
            ['repostComment' => 'nullable|string|max:1000'],
            ['repostComment.max' => 'O comentário pode ter no máximo 1000 caracteres.']
        );

        FeedPost::create([
            'user_id'      => Auth::id(),
            'content'      => $this->sanitize($this->repostComment),
            'repost_of_id' => $this->repostTargetId,
        ]);

        $this->repostModal    = false;
        $this->repostTargetId = null;
        $this->repostComment  = '';
        $this->alertSuccess('Post repostado!');
        unset($this->feedItems, $this->totalFeedCount);
    }

    // ── Seguir / Deixar de seguir ─────────────────────────────────────

    public function toggleFollow(int $userId): void
    {
        if ($userId === Auth::id()) return;

        $user = Auth::user();
        if ($user->isFollowing($userId)) {
            $user->following()->detach($userId);
        } else {
            $user->following()->attach($userId);
        }
        unset($this->myFollowingIds, $this->suggestedUsers, $this->feedItems);
    }

    // ── Bookmark ─────────────────────────────────────────────────────

    public function toggleBookmark(int $feedPostId): void
    {
        $existing = FeedPostBookmark::where('user_id', Auth::id())
            ->where('feed_post_id', $feedPostId)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            FeedPostBookmark::create(['user_id' => Auth::id(), 'feed_post_id' => $feedPostId]);
        }
        unset($this->feedItems);
    }

    // ── Enquete ───────────────────────────────────────────────────────

    public function votePoll(int $pollOptionId): void
    {
        $option = \App\Models\FeedPollOption::with('poll')->findOrFail($pollOptionId);
        $poll   = $option->poll;

        if ($poll->is_expired) {
            $this->alertError('Esta enquete já encerrou.');
            return;
        }

        // Se já votou nessa opção, remove (toggle off)
        $existing = FeedPollVote::where('feed_poll_option_id', $pollOptionId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            if (! $poll->allows_multiple) {
                // Remove votos anteriores nas outras opções desta enquete
                FeedPollVote::whereHas('option', fn ($q) => $q->where('feed_poll_id', $poll->id))
                    ->where('user_id', Auth::id())
                    ->delete();
            }
            FeedPollVote::create(['feed_poll_option_id' => $pollOptionId, 'user_id' => Auth::id()]);
        }
        unset($this->feedItems);
    }

    // ── Leitura automática ────────────────────────────────────────────

    public function markFeedRead(int $postId): void
    {
        // firstOrCreate garante que não duplica no banco
        $read = FeedPostRead::firstOrCreate(
            ['feed_post_id' => $postId, 'user_id' => Auth::id()],
            ['read_at'      => now()],
        );

        // Incrementa views_count apenas na primeira leitura deste usuário
        if ($read->wasRecentlyCreated) {
            FeedPost::where('id', $postId)->increment('views_count');
        }
    }

    // ── Reações (oficiais + sociais) ──────────────────────────────────

    public function toggleReaction(string $itemKey, string $type): void
    {
        [$postType, $id] = explode('_', $itemKey, 2);

        if ($postType === 'official') {
            $existing = PostReaction::where('post_id', $id)->where('user_id', Auth::id())->first();
            if ($existing) {
                $existing->type === $type ? $existing->delete() : $existing->update(['type' => $type]);
            } else {
                PostReaction::create(['post_id' => $id, 'user_id' => Auth::id(), 'type' => $type]);
            }
        } elseif ($postType === 'community') {
            $existing = CommunityPostReaction::where('community_post_id', $id)->where('user_id', Auth::id())->first();
            if ($existing) {
                $existing->type === $type ? $existing->delete() : $existing->update(['type' => $type]);
            } else {
                CommunityPostReaction::create(['community_post_id' => $id, 'user_id' => Auth::id(), 'type' => $type]);
            }
        } else {
            $existing = FeedPostReaction::where('feed_post_id', $id)->where('user_id', Auth::id())->first();
            if ($existing) {
                $existing->type === $type ? $existing->delete() : $existing->update(['type' => $type]);
            } else {
                FeedPostReaction::create(['feed_post_id' => $id, 'user_id' => Auth::id(), 'type' => $type]);

                // Notifica o autor do post (apenas nova reação, não ao remover)
                $feedPost = FeedPost::with('user')->find($id);
                if ($feedPost && $feedPost->user && (int) $feedPost->user_id !== (int) Auth::id()) {
                    $this->notificarUsuario($feedPost->user, new FeedInteracaoNotification($feedPost, Auth::user()->name, 'curtiu'));
                }
            }
        }

        unset($this->feedItems, $this->previewPost);
    }

    // ── Comentários (oficiais + sociais) ──────────────────────────────

    public function toggleComments(string $itemKey): void
    {
        if (in_array($itemKey, $this->openComments)) {
            $this->openComments = array_values(array_filter($this->openComments, fn ($k) => $k !== $itemKey));
        } else {
            $this->openComments[] = $itemKey;
        }
    }

    public function submitComment(string $itemKey, string $commentText = '', ?int $parentId = null): void
    {
        $text = trim($commentText ?: $this->newComment);

        if (! $text) {
            $this->addError('newComment', 'Escreva um comentário.');
            return;
        }
        if (strlen($text) > 1000) {
            $this->addError('newComment', 'O comentário pode ter no máximo 1000 caracteres.');
            return;
        }

        [$postType, $id] = explode('_', $itemKey, 2);

        if ($postType === 'official') {
            PostComment::create([
                'post_id' => $id,
                'user_id' => Auth::id(),
                'content' => $this->sanitize($text),
            ]);
        } elseif ($postType === 'community') {
            \App\Models\CommunityPostComment::create([
                'community_post_id' => $id,
                'user_id'           => Auth::id(),
                'parent_id'         => $parentId,
                'content'           => $this->sanitize($text),
            ]);
        } else {
            FeedPostComment::create([
                'feed_post_id' => $id,
                'user_id'      => Auth::id(),
                'parent_id'    => $parentId,
                'content'      => $this->sanitize($text),
            ]);

            // Notifica o autor do post sobre novo comentário
            $feedPost = FeedPost::with('user')->find($id);
            if ($feedPost && $feedPost->user && (int) $feedPost->user_id !== (int) Auth::id()) {
                $this->notificarUsuario($feedPost->user, new FeedInteracaoNotification($feedPost, Auth::user()->name, 'comentou'));
            }
        }

        $this->newComment = '';
        if ($parentId && isset($this->replyTexts[$parentId])) {
            unset($this->replyTexts[$parentId], $this->replyingTo[$parentId]);
        }
        $this->dispatch('comment-saved', key: $itemKey);
        unset($this->feedItems);
    }

    public function toggleCommentReaction(int $commentId, string $type): void
    {
        $existing = FeedPostCommentReaction::where('feed_post_comment_id', $commentId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->type === $type ? $existing->delete() : $existing->update(['type' => $type]);
        } else {
            FeedPostCommentReaction::create([
                'feed_post_comment_id' => $commentId,
                'user_id'              => Auth::id(),
                'type'                 => $type,
            ]);
        }
        unset($this->feedItems);
    }

    public function deleteComment(string $itemKey, int $commentId): void
    {
        [$postType] = explode('_', $itemKey, 2);

        if ($postType === 'official') {
            PostComment::where('id', $commentId)->where('user_id', Auth::id())->firstOrFail()->delete();
        } else {
            FeedPostComment::where('id', $commentId)->where('user_id', Auth::id())->firstOrFail()->delete();
        }
        unset($this->feedItems);
    }

    // ── Menções ───────────────────────────────────────────────────────

    public function searchMentions(string $query): array
    {
        if (strlen(trim($query)) < 1) return [];

        return User::where('name', 'like', '%'.$query.'%')
            ->where('id', '!=', Auth::id())
            ->limit(6)
            ->get()
            ->map(function ($u) {
                $parts = explode(' ', $u->name);
                return [
                    'id'       => $u->id,
                    'name'     => $u->name,
                    'first'    => $parts[0],
                    'initials' => strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : '')),
                ];
            })
            ->toArray();
    }

    // ── Hashtag ───────────────────────────────────────────────────────

    public function setHashtag(string $tag): void
    {
        $this->searchQuery  = '#'.$tag;
        $this->activeFilter = 'all';
        $this->loadedCount  = 15;
        unset($this->feedItems);
    }

    // ── Filtro / Busca ────────────────────────────────────────────────

    public function setFilter(string $filter): void
    {
        $this->activeFilter = $filter;
        $this->loadedCount  = 15;
        unset($this->feedItems);
    }

    public function setSortBy(string $sort): void
    {
        $this->sortBy      = $sort;
        $this->loadedCount = 15;
        unset($this->feedItems);
    }

    public function updatedSearchQuery(): void
    {
        $this->loadedCount = 15;
        unset($this->feedItems);
    }

    // ── Autocomplete @menção ──────────────────────────────────────────

    public function getMentionSuggestions(string $query): array
    {
        if (strlen($query) < 2) return [];

        return User::where('name', 'like', '%'.$query.'%')
            ->limit(8)
            ->get(['id', 'name'])
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])
            ->toArray();
    }

    // ── Preview post oficial ──────────────────────────────────────────

    public function openPreview(int $postId): void
    {
        $this->previewPostId = $postId;
        $this->previewOpen   = true;
        unset($this->previewPost);

        PostRead::firstOrCreate(
            ['post_id' => $postId, 'user_id' => Auth::id()],
            ['read_at' => now()]
        );
        Post::where('id', $postId)->increment('views_count');
        unset($this->feedItems, $this->unreadOfficialCount);
    }

    public function closePreview(): void
    {
        $this->previewOpen   = false;
        $this->previewPostId = null;
    }

    public function confirmRead(int $postId): void
    {
        PostRead::updateOrCreate(
            ['post_id' => $postId, 'user_id' => Auth::id()],
            ['read_at' => now(), 'confirmed_at' => now()]
        );
        unset($this->previewPost, $this->feedItems, $this->unreadOfficialCount);
        $this->alertSuccess('Leitura confirmada!');
    }

    // ── Load more ─────────────────────────────────────────────────────

    public function loadMore(): void
    {
        $this->loadedCount += 10;
        unset($this->feedItems);
    }

    // ── Poll: detecta novos posts sem re-renderizar o feed ────────────

    public function checkNewPosts(): void
    {
        $audience  = $this->currentUserAudience();
        $isRhAdmin = in_array(Auth::user()->accessProfile?->slug ?? '', ['hr', 'admin']);

        $officialCount = Post::published()->where('pinned', false)
            ->when(! $isRhAdmin, fn ($q) => $q->where(fn ($i) =>
                $i->where('target_audience', 'todos')->orWhere('target_audience', $audience)
            ))->count();

        $total = $officialCount + FeedPost::whereNull('repost_of_id')->count();

        $diff = $total - $this->totalFeedCount;
        if ($diff > 0) {
            $this->newPostsBanner = $diff;
        }
    }

    public function refreshFeed(): void
    {
        $this->newPostsBanner = 0;
        $this->loadedCount    = 15;
        unset($this->feedItems, $this->totalFeedCount);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function currentUserAudience(): string
    {
        return match (Auth::user()->accessProfile?->slug ?? '') {
            'manager'        => 'gerentes',
            'employee'       => 'funcionarios',
            'hr', 'admin'    => 'rh',
            default          => 'todos',
        };
    }

    public function render()
    {
        return view('livewire.pages.feed.index')
            ->layout('components.layouts.app', ['title' => 'Feed']);
    }
}
