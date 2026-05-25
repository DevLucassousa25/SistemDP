<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->boolean('allows_multiple')->default(false);
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('community_poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_poll_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('community_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_poll_option_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['community_poll_option_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_poll_votes');
        Schema::dropIfExists('community_poll_options');
        Schema::dropIfExists('community_polls');
    }
};
