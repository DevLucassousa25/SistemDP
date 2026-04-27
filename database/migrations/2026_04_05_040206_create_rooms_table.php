<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('capacity');
            $table->string('location')->nullable();
            $table->boolean('has_tv')->default(false);
            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_video_conference')->default(false);
            $table->boolean('has_projector')->default(false);
            $table->boolean('has_coffee')->default(false);
            $table->boolean('has_whiteboard')->default(false);
            $table->enum('status', ['disponivel', 'ocupada', 'manutencao'])->default('disponivel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
