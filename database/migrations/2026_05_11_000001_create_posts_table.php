<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('content');
            $table->string('cover_image')->nullable();

            // Tipo: noticia | comunicado | evento | aviso
            $table->string('type')->default('noticia');

            // Status: rascunho | publicado | arquivado
            $table->string('status')->default('rascunho');

            // Público-alvo: todos | gerentes | funcionarios | rh
            $table->string('target_audience')->default('todos');

            $table->boolean('pinned')->default(false);
            $table->unsignedInteger('views_count')->default(0);

            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
