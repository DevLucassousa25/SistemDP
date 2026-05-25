<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_post_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->boolean('allows_multiple')->default(false);
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('feed_poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_poll_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('feed_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_poll_option_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['feed_poll_option_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_poll_votes');
        Schema::dropIfExists('feed_poll_options');
        Schema::dropIfExists('feed_polls');
    }
};
