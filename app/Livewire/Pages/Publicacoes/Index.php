<?php

namespace App\Livewire\Pages\Publicacoes;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Livewire\SecureComponent;
use App\Notifications\PublicacaoObrigatoriaNotification;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostReaction;
use App\Models\PostRead;
use App\Models\PostTag;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends SecureComponent
{
    use WithFileUploads, WithPagination, EnviaNotificacoes;

    // ── Filtros ───────────────────────────────────────────────────────
    public string $search         = '';
    public string $filterType     = '';
    public string $filterStatus   = '';
    public string $filterAudience = '';
    public string $filterTag      = '';

    // ── Ordenação e busca avançada ────────────────────────────────────
    public string $sortBy           = 'recent';
    public bool   $searchInContent  = false;

    // ── Form: criar / editar ──────────────────────────────────────────
    public bool   $formOpen       = false;
    public string $formMode       = 'create';

    #[Locked]
    public ?int   $editPostId     = null;

    public string  $title             = '';
    public string  $content           = '';
    public string  $type              = 'noticia';
    public string  $target_audience   = 'todos';
    public bool    $pinned            = false;
    public bool    $is_mandatory_read = false;
    public string  $published_at      = '';
    public string  $expires_at        = '';
    public         $coverImage        = null;
    public ?string $existingCover     = null;
    public array   $selectedTags      = [];

    // ── Autosave ──────────────────────────────────────────────────────
    public string $lastAutosavedAt = '';

    // ── Gerenciar tags ────────────────────────────────────────────────
    public bool   $tagModal    = false;
    public string $newTagName  = '';
    public string $newTagColor = 'indigo';

    // ── Modal: quem leu ───────────────────────────────────────────────
    public bool $readersModal  = false;
    #[Locked]
    public ?int $readersPostId = null;

    // ── Preview ───────────────────────────────────────────────────────
    public bool   $previewOpen   = false;
    #[Locked]
    public ?int   $previewPostId = null;

    // ── Engajamento ───────────────────────────────────────────────────
    public string $newComment = '';

    // ── Exclusão ──────────────────────────────────────────────────────
    public bool   $deleteModal  = false;
    #[Locked]
    public ?int   $deletePostId = null;

    // ── Ciclo de vida ─────────────────────────────────────────────────

    public function mount(): void
    {
        $this->requireAuth();
        $this->requireRhOrAdmin();
    }

    public function updatingSearch(): void         { $this->resetPage(); }
    public function updatingFilterType(): void     { $this->resetPage(); }
    public function updatingFilterStatus(): void   { $this->resetPage(); }
    public function updatingFilterAudience(): void { $this->resetPage(); }
    public function updatingFilterTag(): void      { $this->resetPage(); }
    public function updatingSortBy(): void         { $this->resetPage(); }

    // ── Computeds ─────────────────────────────────────────────────────

    #[Computed]
    public function posts()
    {
        return Post::with([
                'author', 'tags', 'reads',
                'reactions' => fn ($q) => $q->where('user_id', Auth::id()),
            ])
            ->withCount([
                'reactions as like_count'  => fn ($q) => $q->where('type', 'like'),
                'reactions as heart_count' => fn ($q) => $q->where('type', 'heart'),
                'reactions as clap_count'  => fn ($q) => $q->where('type', 'clap'),
                'comments',
            ])
            ->when($this->search, fn ($q) => $q->where(function ($inner) {
                $inner->where('title', 'ilike', '%' . $this->search . '%');
                if ($this->searchInContent) {
                    $inner->orWhere('content', 'ilike', '%' . $this->search . '%');
                }
            }))
            ->when($this->filterType,     fn ($q) => $q->where('type', $this->filterType))
            ->when($this->filterStatus,   fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterAudience, fn ($q) => $q->where('target_audience', $this->filterAudience))
            ->when($this->filterTag,      fn ($q) => $q->whereHas('tags', fn ($q2) => $q2->where('post_tags.id', $this->filterTag)))
            ->orderByDesc('pinned')
            ->when($this->sortBy === 'popular',   fn ($q) => $q->orderByDesc('views_count'))
            ->when($this->sortBy === 'mandatory', fn ($q) => $q->orderByDesc('is_mandatory_read')->orderByDesc('created_at'))
            ->when($this->sortBy === 'recent' || ! in_array($this->sortBy, ['popular', 'mandatory']), fn ($q) => $q->orderByDesc('created_at'))
            ->paginate(12);
    }

    #[Computed]
    public function stats(): array
    {
        $byStatus = Post::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return [
            'total'     => Post::count(),
            'publicado' => $byStatus['publicado'] ?? 0,
            'rascunho'  => $byStatus['rascunho']  ?? 0,
            'arquivado' => $byStatus['arquivado']  ?? 0,
            'pinned'    => Post::where('pinned', true)->count(),
            'mandatory' => Post::where('is_mandatory_read', true)->where('status', 'publicado')->count(),
        ];
    }

    #[Computed]
    public function availableTags()
    {
        return PostTag::orderBy('name')->get();
    }

    #[Computed]
    public function previewPost(): ?Post
    {
        if (! $this->previewPostId) return null;
        return Post::with(['author', 'tags', 'reads.user', 'reactions.user', 'comments.user'])->find($this->previewPostId);
    }

    #[Computed]
    public function readersPost(): ?Post
    {
        if (! $this->readersPostId) return null;
        return Post::with(['reads.user.department', 'reads.user.accessProfile'])->find($this->readersPostId);
    }

    #[Computed]
    public function nonReaders()
    {
        if (! $this->readersPostId) return collect();

        $post = Post::find($this->readersPostId);
        if (! $post) return collect();

        $readUserIds = PostRead::where('post_id', $this->readersPostId)->pluck('user_id');

        return User::with(['department', 'accessProfile'])
            ->where('is_active', true)
            ->when($post->target_audience !== 'todos', function ($q) use ($post) {
                $q->whereHas('accessProfile', fn ($q2) => $q2->where('slug', match ($post->target_audience) {
                    'gerentes'     => 'manager',
                    'funcionarios' => 'employee',
                    'rh'           => 'hr',
                    default        => '',
                }));
            })
            ->whereNotIn('id', $readUserIds)
            ->orderBy('name')
            ->get();
    }

    // ── Filtros ───────────────────────────────────────────────────────

    public function clearFilters(): void
    {
        $this->search         = '';
        $this->filterType     = '';
        $this->filterStatus   = '';
        $this->filterAudience = '';
        $this->filterTag      = '';
        $this->searchInContent = false;
        $this->sortBy         = 'recent';
        $this->resetPage();
        unset($this->posts);
    }

    // ── Formulário ────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->requireRhOrAdmin();
        $this->resetForm();
        $this->formMode = 'create';
        $this->formOpen = true;
    }

    public function openEdit(int $postId): void
    {
        $this->requireRhOrAdmin();
        $post = Post::with('tags')->findOrFail($postId);

        $this->editPostId        = $post->id;
        $this->title             = $post->title;
        $this->content           = $post->content;
        $this->type              = $post->type;
        $this->target_audience   = $post->target_audience;
        $this->pinned            = $post->pinned;
        $this->is_mandatory_read = $post->is_mandatory_read;
        $this->published_at      = $post->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->expires_at        = $post->expires_at?->format('Y-m-d\TH:i')   ?? '';
        $this->existingCover     = $post->cover_image;
        $this->coverImage        = null;
        $this->selectedTags      = $post->tags->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->lastAutosavedAt   = '';
        $this->formMode          = 'edit';
        $this->formOpen          = true;
    }

    public function closeForm(): void
    {
        $this->formOpen = false;
        $this->resetForm();
    }

    public function save(string $publishAction = 'rascunho'): void
    {
        $this->requireRhOrAdmin();

        $this->validate([
            'title'           => 'required|string|max:255',
            'content'         => 'required|string|min:10',
            'type'            => 'required|in:noticia,comunicado,evento,aviso',
            'target_audience' => 'required|in:todos,gerentes,funcionarios,rh',
            'coverImage'      => 'nullable|image|max:4096',
            'published_at'    => 'nullable|date',
            'expires_at'      => 'nullable|date|after_or_equal:published_at',
        ], [
            'title.required'   => 'O título é obrigatório.',
            'content.required' => 'O conteúdo não pode estar vazio.',
            'content.min'      => 'O conteúdo deve ter pelo menos 10 caracteres.',
            'coverImage.image' => 'O arquivo deve ser uma imagem.',
            'coverImage.max'   => 'A imagem não pode ultrapassar 4 MB.',
        ]);

        $coverPath = $this->existingCover;
        if ($this->coverImage) {
            if ($coverPath) Storage::disk('public')->delete($coverPath);
            $coverPath = $this->coverImage->store('posts/covers', 'public');
        }

        $status = match ($publishAction) {
            'publicado' => 'publicado',
            'arquivado' => 'arquivado',
            default     => 'rascunho',
        };

        $data = [
            'title'             => $this->sanitize($this->title),
            'content'           => strip_tags($this->content, '<p><br><b><i><u><ul><ol><li><h2><h3><a><strong><em><span>'),
            'type'              => $this->type,
            'target_audience'   => $this->target_audience,
            'pinned'            => $this->pinned,
            'is_mandatory_read' => $this->is_mandatory_read,
            'status'            => $status,
            'cover_image'       => $coverPath,
            'published_at'      => $status === 'publicado'
                ? ($this->published_at ?: now())
                : ($this->published_at ?: null),
            'expires_at'        => $this->expires_at ?: null,
        ];

        $tagIds = array_filter(array_map('intval', $this->selectedTags));

        if ($this->formMode === 'create') {
            $post = Post::create(array_merge($data, ['user_id' => Auth::id()]));
            $post->tags()->sync($tagIds);

            // Notifica todos se publicação obrigatória ao criar direto como publicada
            if ($status === 'publicado' && $this->is_mandatory_read) {
                $this->notificarPublicacaoObrigatoria($post);
            }

            $this->alertSuccess('Publicação criada!', match ($status) {
                'publicado' => 'A publicação foi ao ar.',
                'rascunho'  => 'Salva como rascunho.',
                default     => '',
            });
        } else {
            $post = Post::findOrFail($this->editPostId);
            $wasPublished = $post->status === 'publicado';
            $post->update($data);
            $post->tags()->sync($tagIds);

            // Notifica ao publicar pela primeira vez com leitura obrigatória
            if ($status === 'publicado' && ! $wasPublished && $this->is_mandatory_read) {
                $this->notificarPublicacaoObrigatoria($post->fresh());
            }

            $this->alertSuccess('Publicação atualizada!');
        }

        $this->closeForm();
        unset($this->posts, $this->stats);
    }

    // ── Autosave (somente modo edição) ────────────────────────────────

    public function autosave(): void
    {
        if ($this->formMode !== 'edit' || ! $this->editPostId || ! $this->title) return;

        $post = Post::find($this->editPostId);
        if (! $post) return;

        $post->update([
            'title'             => $this->sanitize($this->title),
            'content'           => strip_tags($this->content, '<p><br><b><i><u><ul><ol><li><h2><h3><a><strong><em><span>'),
            'type'              => $this->type,
            'target_audience'   => $this->target_audience,
            'pinned'            => $this->pinned,
            'is_mandatory_read' => $this->is_mandatory_read,
        ]);

        $this->lastAutosavedAt = now()->format('H:i');
        unset($this->posts);
    }

    // ── Ações rápidas ─────────────────────────────────────────────────

    public function publish(int $postId): void
    {
        $this->requireRhOrAdmin();
        $post = Post::findOrFail($postId);
        $post->update(['status' => 'publicado', 'published_at' => $post->published_at ?? now()]);

        // Notifica todos os usuários se for leitura obrigatória
        if ($post->is_mandatory_read) {
            $this->notificarPublicacaoObrigatoria($post->fresh());
        }

        $this->alertSuccess('Publicado!', '"' . $post->title . '" está no ar.');
        unset($this->posts, $this->stats);
    }

    /** Notifica todos os usuários ativos (exceto o atual) sobre publicação obrigatória. */
    private function notificarPublicacaoObrigatoria(Post $post): void
    {
        $total = $this->notificarTodosAtivos(new PublicacaoObrigatoriaNotification($post));
        $this->toastNotif(
            'Leitura obrigatória enviada!',
            "{$total} usuário(s) foram notificados sobre \"{$post->title}\".",
            'book-open', 'rose',
            route('publicacoes')
        );
    }

    public function unpublish(int $postId): void
    {
        $this->requireRhOrAdmin();
        Post::findOrFail($postId)->update(['status' => 'rascunho']);
        $this->alertInfo('Despublicado', 'Voltou para rascunho.');
        unset($this->posts, $this->stats);
    }

    public function archive(int $postId): void
    {
        $this->requireRhOrAdmin();
        Post::findOrFail($postId)->update(['status' => 'arquivado']);
        $this->alertInfo('Arquivado.');
        unset($this->posts, $this->stats);
    }

    public function togglePin(int $postId): void
    {
        $this->requireRhOrAdmin();
        $post = Post::findOrFail($postId);
        $post->update(['pinned' => ! $post->pinned]);
        unset($this->posts);
    }

    public function duplicate(int $postId): void
    {
        $this->requireRhOrAdmin();
        $original = Post::with('tags')->findOrFail($postId);

        $copy = $original->replicate(['views_count', 'published_at']);
        $copy->title             = 'Cópia de ' . $original->title;
        $copy->status            = 'rascunho';
        $copy->pinned            = false;
        $copy->is_mandatory_read = false;
        $copy->user_id           = Auth::id();
        $copy->push();

        $copy->tags()->sync($original->tags->pluck('id'));

        $this->alertSuccess('Publicação duplicada!', 'Abra a cópia para editar.');
        unset($this->posts, $this->stats);
    }

    // ── Engajamento ───────────────────────────────────────────────────

    public function toggleReaction(int $postId, string $type): void
    {
        $existing = PostReaction::where('post_id', $postId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->type === $type ? $existing->delete() : $existing->update(['type' => $type]);
        } else {
            PostReaction::create(['post_id' => $postId, 'user_id' => Auth::id(), 'type' => $type]);
        }

        unset($this->previewPost, $this->posts);
    }

    public function confirmRead(int $postId): void
    {
        PostRead::updateOrCreate(
            ['post_id' => $postId, 'user_id' => Auth::id()],
            ['read_at' => now(), 'confirmed_at' => now()]
        );
        unset($this->previewPost, $this->readersPost, $this->nonReaders);
        $this->alertSuccess('Leitura confirmada!');
    }

    public function addComment(int $postId): void
    {
        $this->validate(
            ['newComment' => 'required|string|max:1000'],
            ['newComment.required' => 'Escreva um comentário.']
        );

        PostComment::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'content' => $this->sanitize($this->newComment),
        ]);

        $this->newComment = '';
        unset($this->previewPost);
    }

    public function deleteComment(int $commentId): void
    {
        PostComment::findOrFail($commentId)->delete();
        unset($this->previewPost);
    }

    // ── Exportar relatório ────────────────────────────────────────────

    public function exportReadersReport(int $postId): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->requireRhOrAdmin();
        $post     = Post::with(['reads.user.department'])->findOrFail($postId);
        $filename = 'leitores-' . Str::slug($post->title) . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($post) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
            fputcsv($handle, ['Nome', 'Departamento', 'Lido em', 'Confirmado em'], ';');

            foreach ($post->reads as $read) {
                fputcsv($handle, [
                    $read->user?->name             ?? '—',
                    $read->user?->department?->name ?? '—',
                    $read->read_at?->format('d/m/Y H:i')      ?? '—',
                    $read->confirmed_at?->format('d/m/Y H:i') ?? '—',
                ], ';');
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Exclusão ──────────────────────────────────────────────────────

    public function confirmDelete(int $postId): void
    {
        $this->deletePostId = $postId;
        $this->deleteModal  = true;
    }

    public function delete(): void
    {
        $this->requireRhOrAdmin();
        if (! $this->deletePostId) return;

        $post = Post::findOrFail($this->deletePostId);
        if ($post->cover_image) Storage::disk('public')->delete($post->cover_image);
        $post->delete();

        $this->deleteModal  = false;
        $this->deletePostId = null;
        $this->alertSuccess('Publicação excluída.');
        unset($this->posts, $this->stats);
    }

    // ── Preview ───────────────────────────────────────────────────────

    public function openPreview(int $postId): void
    {
        $this->previewPostId = $postId;
        $this->previewOpen   = true;
        $this->newComment    = '';
        unset($this->previewPost);

        PostRead::firstOrCreate(
            ['post_id' => $postId, 'user_id' => Auth::id()],
            ['read_at' => now()]
        );

        Post::where('id', $postId)->increment('views_count');
        unset($this->posts);
    }

    public function closePreview(): void
    {
        $this->previewOpen   = false;
        $this->previewPostId = null;
        $this->newComment    = '';
    }

    // ── Leitores ──────────────────────────────────────────────────────

    public function openReadersReport(int $postId): void
    {
        $this->readersPostId = $postId;
        $this->readersModal  = true;
        unset($this->readersPost, $this->nonReaders);
    }

    public function closeReadersReport(): void
    {
        $this->readersModal  = false;
        $this->readersPostId = null;
    }

    // ── Gerenciar tags ────────────────────────────────────────────────

    public function openTagManager(): void
    {
        $this->tagModal = true;
        unset($this->availableTags);
    }

    public function closeTagManager(): void
    {
        $this->tagModal    = false;
        $this->newTagName  = '';
        $this->newTagColor = 'indigo';
        unset($this->availableTags);
    }

    public function saveTag(): void
    {
        $this->requireRhOrAdmin();
        $this->validate(['newTagName' => 'required|string|max:40'], ['newTagName.required' => 'Nome é obrigatório.']);

        PostTag::create([
            'name'  => $this->sanitize($this->newTagName),
            'slug'  => Str::slug($this->newTagName),
            'color' => $this->newTagColor,
        ]);

        $this->newTagName  = '';
        $this->newTagColor = 'indigo';
        unset($this->availableTags);
    }

    public function deleteTag(int $tagId): void
    {
        $this->requireRhOrAdmin();
        PostTag::findOrFail($tagId)->delete();
        $this->selectedTags = array_filter($this->selectedTags, fn ($id) => (int) $id !== $tagId);
        unset($this->availableTags);
    }

    public function toggleSelectedTag(string $tagId): void
    {
        if (in_array($tagId, $this->selectedTags)) {
            $this->selectedTags = array_values(array_filter($this->selectedTags, fn ($id) => $id !== $tagId));
        } else {
            $this->selectedTags[] = $tagId;
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->editPostId        = null;
        $this->title             = '';
        $this->content           = '';
        $this->type              = 'noticia';
        $this->target_audience   = 'todos';
        $this->pinned            = false;
        $this->is_mandatory_read = false;
        $this->published_at      = '';
        $this->expires_at        = '';
        $this->coverImage        = null;
        $this->existingCover     = null;
        $this->selectedTags      = [];
        $this->lastAutosavedAt   = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.pages.publicacoes.index')
            ->layout('components.layouts.app', ['title' => 'Publicações']);
    }
}
