<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_post_comment_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_post_comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20); // like, heart, clap
            $table->timestamps();
            $table->unique(['feed_post_comment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_post_comment_reactions');
    }
};
