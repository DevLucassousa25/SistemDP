<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela de tags
        Schema::create('post_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            // Cor base Tailwind: indigo | violet | blue | emerald | teal | amber | orange | red | pink | slate
            $table->string('color')->default('indigo');
            $table->timestamps();
        });

        // Pivot post <-> tag
        Schema::create('post_post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_tag_id')->constrained('post_tags')->cascadeOnDelete();
            $table->primary(['post_id', 'post_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_post_tag');
        Schema::dropIfExists('post_tags');
    }
};
