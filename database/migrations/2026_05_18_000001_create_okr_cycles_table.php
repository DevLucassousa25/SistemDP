<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okr_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // "Q1 2025", "Anual 2025"
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['planning', 'active', 'closed'])->default('planning');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okr_cycles');
    }
};
