<?php

namespace App\Http\Controllers;

use App\Models\EquipamentoAtribuicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipamentoTermoController extends Controller
{
    public function show(EquipamentoAtribuicao $atribuicao)
    {
        // Só RH/DP/TI ou o próprio funcionário podem ver
        $user  = Auth::user();
        $isTi  = str_contains(strtolower($user->department?->name ?? ''), 'ti')
               || str_contains(strtolower($user->department?->name ?? ''), 'tecnologia');

        if (! $user->isRhOuDp() && ! $isTi && $user->id !== $atribuicao->user_id) {
            abort(403);
        }

        $atribuicao->load([
            'equipamento.cadastradoPor',
            'funcionario.department',
            'responsavel',
        ]);

        $empresa = \App\Models\ConfiguracaoEmpresa::first();

        // Lê campos extras preenchidos no modal (session, expira em 10 min)
        $sessionKey  = 'termo_extras_' . $atribuicao->id;
        $extrasData  = session()->pull($sessionKey, []);
        $extrasValores = [];

        if (
            ! empty($extrasData['campos']) &&
            isset($extrasData['expires']) &&
            $extrasData['expires'] >= now()->timestamp
        ) {
            $extrasValores = $extrasData['campos'];
        }

        // Carrega o template selecionado (da session) ou o padrão
        $templateId = $extrasData['template_id'] ?? null;
        $cfg = $templateId
            ? (\App\Models\ConfiguracaoTermo::find($templateId) ?? \App\Models\ConfiguracaoTermo::instancia())
            : \App\Models\ConfiguracaoTermo::instancia();

        $camposExtras = array_values(array_filter(
            $cfg->campos_extras ?? \App\Models\ConfiguracaoTermo::camposExtrasPadrao(),
            fn ($c) => ! empty($c['ativo'])
        ));

        return view('equipamentos.termo', compact('atribuicao', 'empresa', 'extrasValores', 'camposExtras', 'cfg'));
    }
}
