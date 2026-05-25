<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mood_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('mood', ['otimo', 'bem', 'normal', 'pessimo']);
            $table->text('note')->nullable();
            $table->date('checkin_date');                   // para métricas por dia
            $table->timestamps();

            $table->unique(['user_id', 'checkin_date']);    // 1 resposta por dia
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mood_checkins');
    }
};
