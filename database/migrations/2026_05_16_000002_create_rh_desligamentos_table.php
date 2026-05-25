<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_desligamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();         // funcionário
            $table->foreignId('rh_user_id')->constrained('users')->cascadeOnDelete(); // quem abriu o processo
            $table->enum('tipo', [
                'voluntario', 'sem_justa_causa', 'com_justa_causa',
                'acordo_mutuo', 'aposentadoria',
            ]);
            $table->enum('status', ['em_processo', 'concluido', 'cancelado'])->default('em_processo');
            $table->date('data_aviso')->nullable();
            $table->date('data_ultimo_dia')->nullable();
            $table->enum('aviso_previo_tipo', ['trabalhado', 'indenizado'])->default('trabalhado');
            $table->unsignedInteger('aviso_previo_dias')->default(30);
            $table->boolean('recontratavel')->nullable();      // null = não avaliado ainda
            $table->text('observacoes')->nullable();
            $table->timestamp('concluido_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_desligamentos');
    }
};
