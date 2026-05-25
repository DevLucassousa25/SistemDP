<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
        });

        Schema::create('community_post_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('community_post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['like', 'heart', 'clap']);
            $table->timestamps();
            $table->unique(['community_post_id', 'user_id']);
        });

        Schema::create('community_post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('community_post_comments')->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_post_comments');
        Schema::dropIfExists('community_post_reactions');
        Schema::dropIfExists('community_post_images');
        Schema::dropIfExists('community_posts');
    }
};
