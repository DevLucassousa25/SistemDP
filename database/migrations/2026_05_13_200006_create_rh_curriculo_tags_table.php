<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_curriculo_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculo_id')->constrained('rh_curriculos')->cascadeOnDelete();
            $table->string('tag', 80);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_curriculo_tags');
    }
};
