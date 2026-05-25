<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature   = 'posts:publish-scheduled';
    protected $description = 'Publica automaticamente os posts com published_at <= agora que ainda estão como rascunho.';

    public function handle(): int
    {
        $posts = Post::scheduled()->get();

        if ($posts->isEmpty()) {
            $this->info('Nenhum post agendado para publicar.');
            return self::SUCCESS;
        }

        foreach ($posts as $post) {
            $post->update(['status' => 'publicado']);
            $this->info("Publicado: [{$post->id}] {$post->title}");
        }

        $this->info("{$posts->count()} post(s) publicado(s).");
        return self::SUCCESS;
    }
}
