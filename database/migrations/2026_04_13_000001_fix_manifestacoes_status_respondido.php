<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adiciona as colunas de timestamp de marco
        Schema::table('manifestacoes', function (Blueprint $table) {
            $table->timestamp('respondido_em')->nullable()->after('status');
            $table->timestamp('concluido_em')->nullable()->after('respondido_em');
        });

        // 2. Corrige retroativamente: manifestações com respostas mas status em_analise/em_andamento
        $rows = DB::table('manifestacoes as m')
            ->join('respostas_manifestacaos as r', 'r.manifestacao_id', '=', 'm.id')
            ->whereIn('m.status', ['em_analise', 'em_andamento'])
            ->whereNull('m.deleted_at')
            ->select('m.id', DB::raw('MIN(r.created_at) as primeira_resposta'))
            ->groupBy('m.id')
            ->get();

        foreach ($rows as $row) {
            DB::table('manifestacoes')
                ->where('id', $row->id)
                ->update([
                    'status'        => 'respondido',
                    'respondido_em' => $row->primeira_resposta,
                    'updated_at'    => now(),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('manifestacoes', function (Blueprint $table) {
            $table->dropColumn(['respondido_em', 'concluido_em']);
        });
    }
};
