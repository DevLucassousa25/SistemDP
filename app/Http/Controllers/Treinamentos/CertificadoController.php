<?php

namespace App\Http\Controllers\Treinamentos;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracaoEmpresa;
use App\Models\TreinamentoCertificado;
use App\Models\TreinamentoInscricao;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificadoController extends Controller
{
    /**
     * Gera (ou regenera) e exibe o PDF do certificado.
     */
    public function show(int $inscricaoId)
    {
        $inscricao = TreinamentoInscricao::with(['usuario', 'treinamento', 'certificado'])
            ->findOrFail($inscricaoId);

        // Somente o próprio colaborador ou RH/Admin pode ver
        $user = Auth::user();
        if ($user->id !== $inscricao->user_id && ! $user->isRhOuDp()) {
            abort(403);
        }

        if ($inscricao->status !== 'concluido') {
            abort(403, 'Curso não concluído.');
        }

        // Cria registro de certificado se ainda não existir
        $certificado = $inscricao->certificado ?? TreinamentoCertificado::create([
            'inscricao_id' => $inscricao->id,
            'codigo'       => strtoupper(Str::random(12)),
            'emitido_em'   => now(),
        ]);

        $empresa = ConfiguracaoEmpresa::instancia();

        $data = [
            'colaborador'   => $inscricao->usuario->name,
            'curso'         => $inscricao->treinamento->titulo,
            'carga_horaria' => $inscricao->treinamento->carga_horaria_formatada,
            'instrutor'     => $inscricao->treinamento->instrutor,
            'concluido_em'  => $inscricao->concluido_em?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'emitido_em'    => $certificado->emitido_em->format('d/m/Y'),
            'codigo'        => $certificado->codigo,
            'empresa'       => $empresa->nome_empresa ?? config('app.name'),
            'nivel'         => ucfirst($inscricao->treinamento->nivel),
        ];

        $pdf = Pdf::loadView('treinamentos.certificado', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'dpi'                  => 150,
                'defaultFont'          => 'sans-serif',
                'isRemoteEnabled'      => true,
                'isHtml5ParserEnabled' => true,
            ]);

        $filename = 'certificado-' . Str::slug($inscricao->treinamento->titulo) . '-' . $certificado->codigo . '.pdf';

        return $pdf->stream($filename);
    }
}
