<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            // online | presencial
            $table->string('location_type')->default('online');
            // google_meet | zoom | teams | outro (apenas para online)
            $table->string('online_platform')->nullable();
            $table->string('online_link', 500)->nullable();
            // sala física (opcional)
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            // organizador
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();
            // pendente | confirmada | cancelada
            $table->string('status')->default('pendente');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
