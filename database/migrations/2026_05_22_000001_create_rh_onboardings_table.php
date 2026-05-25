<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_onboardings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidatura_id')->constrained('rh_candidaturas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();          // novo funcionário
            $table->foreignId('criado_by')->constrained('users')->cascadeOnDelete();        // quem aprovou
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('cargo', 150)->nullable();
            $table->date('data_inicio')->nullable();
            $table->enum('status', ['em_andamento', 'concluido', 'cancelado'])->default('em_andamento');
            $table->timestamp('concluido_at')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_onboardings');
    }
};
