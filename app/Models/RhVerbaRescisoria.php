<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RhVerbaRescisoria extends Model
{
    protected $table = 'rh_verbas_rescisórias';

    protected $fillable = [
        'desligamento_id',
        // Dados base
        'salario_base', 'data_admissao', 'data_demissao',
        'meses_trabalhados', 'avos_ferias', 'avos_decimo', 'dias_aviso',
        'num_dependentes', 'tem_ferias_vencidas',
        // Proventos
        'saldo_salario',
        'ferias_proporcionais', 'ferias_vencidas', 'um_terco_ferias',
        'decimo_terceiro',
        'aviso_previo_valor',
        'outros_creditos',
        'total_bruto',
        // Descontos
        'inss', 'irrf', 'descontos', 'total_descontos',
        // FGTS
        'saldo_fgts', 'multa_fgts', 'fgts_disponivel',
        // Total
        'total_liquido',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data_admissao'       => 'date',
            'data_demissao'       => 'date',
            'tem_ferias_vencidas' => 'boolean',
            'meses_trabalhados'   => 'integer',
            'avos_ferias'         => 'integer',
            'avos_decimo'         => 'integer',
            'dias_aviso'          => 'integer',
            'num_dependentes'     => 'integer',
            'salario_base'        => 'decimal:2',
            'saldo_salario'       => 'decimal:2',
            'ferias_proporcionais'=> 'decimal:2',
            'ferias_vencidas'     => 'decimal:2',
            'um_terco_ferias'     => 'decimal:2',
            'decimo_terceiro'     => 'decimal:2',
            'aviso_previo_valor'  => 'decimal:2',
            'outros_creditos'     => 'decimal:2',
            'total_bruto'         => 'decimal:2',
            'inss'                => 'decimal:2',
            'irrf'                => 'decimal:2',
            'descontos'           => 'decimal:2',
            'total_descontos'     => 'decimal:2',
            'saldo_fgts'          => 'decimal:2',
            'multa_fgts'          => 'decimal:2',
            'fgts_disponivel'     => 'decimal:2',
            'total_liquido'       => 'decimal:2',
        ];
    }

    public function desligamento(): BelongsTo
    {
        return $this->belongsTo(RhDesligamento::class, 'desligamento_id');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  CÁLCULO PRINCIPAL — CLT
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Calcula verbas rescisórias conforme a CLT brasileira.
     *
     * Tipos de desligamento aceitos:
     *   sem_justa_causa | voluntario | com_justa_causa | acordo_mutuo | aposentadoria
     *
     * Aviso Prévio:
     *   'indenizado' → empresa paga; 'trabalhado' → empregado cumpriu, sem pagamento extra.
     *   Se $avisoPrevioDias = 0, calcula proporcional automaticamente (Lei 12.506/2011).
     *
     * Saldo FGTS:
     *   Se $saldoFgts = 0, estima como salário × 8% × meses (use o saldo real para precisão).
     *
     * @return array{
     *   meses_trabalhados: int, avos_ferias: int, avos_decimo: int, dias_aviso: int,
     *   saldo_salario: float, ferias_proporcionais: float, ferias_vencidas: float,
     *   um_terco_ferias: float, decimo_terceiro: float, aviso_previo_valor: float,
     *   outros_creditos: float, total_bruto: float,
     *   inss: float, irrf: float, descontos: float, total_descontos: float,
     *   saldo_fgts: float, multa_fgts: float, fgts_disponivel: float,
     *   total_liquido: float,
     *   direitos: array, observacoes_legais: string[]
     * }
     */
    public static function calcular(
        float  $salarioBase,
        string $dataAdmissao,
        string $dataDemissao,
        string $tipo               = 'sem_justa_causa',
        string $avisoPrevioTipo    = 'indenizado',
        int    $avisoPrevioDias    = 0,
        bool   $temFeriasVencidas  = false,
        float  $saldoFgts          = 0.0,
        int    $numeroDependentes  = 0,
        float  $outrosCreditos     = 0.0,
        float  $descontosExtras    = 0.0,
    ): array {
        $admissao = Carbon::parse($dataAdmissao)->startOfDay();
        $demissao = Carbon::parse($dataDemissao)->startOfDay();

        // ── Tempo de serviço ─────────────────────────────────────────
        $mesesTotal    = (int) $admissao->diffInMonths($demissao);
        $anosCompletos = (int) floor($mesesTotal / 12);
        $diasMes       = $demissao->daysInMonth;
        $diaDemissao   = $demissao->day;

        // ── Direitos por tipo ────────────────────────────────────────
        $dir = self::direitosPorTipo($tipo);

        // ── Avos (regra dos 15 dias — CLT) ──────────────────────────
        $avosFer = $dir['ferias_proporcionais'] ? self::calcularAvosFeriasProporcionais($admissao, $demissao) : 0;
        $avos13  = $dir['decimo_terceiro']      ? self::calcularAvos13($admissao, $demissao)                 : 0;

        // ═══════════════════════════════════════════════════════════
        //  PROVENTOS
        // ═══════════════════════════════════════════════════════════

        // 1 — Saldo de salário (sempre devido)
        //     Proporcional ao número de dias trabalhados no último mês
        $saldoSalario = round(($salarioBase / $diasMes) * $diaDemissao, 2);

        // 2 — Férias proporcionais + 1/3 constitucional (art. 7°, XVII, CF)
        $feriasProp    = 0.0;
        $umTercoProp   = 0.0;
        if ($avosFer > 0) {
            $feriasProp  = round(($salarioBase / 12) * $avosFer, 2);
            $umTercoProp = round($feriasProp / 3, 2);
        }

        // 3 — Férias vencidas + 1/3 (período aquisitivo completo não gozado)
        //     Devidas em todas as modalidades (inclusive justa causa — Súmula 171 TST)
        $feriasVenc  = 0.0;
        $umTercoVenc = 0.0;
        if ($dir['ferias_vencidas'] && $temFeriasVencidas) {
            $feriasVenc  = $salarioBase; // 1 período = 1 salário completo (30 dias)
            $umTercoVenc = round($feriasVenc / 3, 2);
        }

        // 4 — 13° proporcional (Lei 4.090/62)
        $decimoTerceiro = 0.0;
        if ($avos13 > 0) {
            $decimoTerceiro = round(($salarioBase / 12) * $avos13, 2);
        }

        // 5 — Aviso prévio
        $diasAviso        = 0;
        $avisoPrevioValor = 0.0;

        if ($tipo === 'acordo_mutuo') {
            // Art. 484-A CLT: empresa paga 50% do aviso proporcional
            $diasProporcional = self::diasAvisoProporcional($anosCompletos);
            $diasAviso        = (int) ceil($diasProporcional / 2);
            $avisoPrevioValor = round(($salarioBase / 30) * $diasAviso, 2);

        } elseif ($dir['aviso_previo'] && $avisoPrevioTipo === 'indenizado') {
            // Sem justa causa — aviso indenizado (Lei 12.506/2011)
            $diasProporcional = self::diasAvisoProporcional($anosCompletos);
            $diasAviso        = $avisoPrevioDias > 0 ? $avisoPrevioDias : $diasProporcional;
            $avisoPrevioValor = round(($salarioBase / 30) * $diasAviso, 2);

        } elseif ($dir['aviso_previo'] && $avisoPrevioTipo === 'trabalhado') {
            // Aviso trabalhado: empregado cumpriu; registramos os dias mas sem valor extra
            $diasAviso        = self::diasAvisoProporcional($anosCompletos);
            $avisoPrevioValor = 0.0; // pago como salário normal durante o aviso
        }

        // ═══════════════════════════════════════════════════════════
        //  FGTS
        // ═══════════════════════════════════════════════════════════

        // Saldo FGTS: usa valor real se informado; senão estima (8% × salário × meses)
        $saldoFgtsReal = $saldoFgts > 0
            ? $saldoFgts
            : round($salarioBase * 0.08 * $mesesTotal, 2);

        // Multa FGTS
        $aliquotaMulta = match ($tipo) {
            'sem_justa_causa' => 0.40,  // 40% — art. 18 §1° Lei 8.036/90
            'acordo_mutuo'    => 0.20,  // 20% — art. 484-A CLT
            default           => 0.00,
        };
        $multaFgts = round($saldoFgtsReal * $aliquotaMulta, 2);

        // FGTS disponível para saque
        $fgtsDisponivel = match ($tipo) {
            'sem_justa_causa' => round($saldoFgtsReal + $multaFgts, 2),
            'acordo_mutuo'    => round($saldoFgtsReal * 0.80 + $multaFgts, 2), // 80% do saldo
            'aposentadoria'   => $saldoFgtsReal, // sem multa, mas saque integral
            default           => 0.0, // voluntário / justa causa: não saca
        };

        // ═══════════════════════════════════════════════════════════
        //  DESCONTOS LEGAIS
        // ═══════════════════════════════════════════════════════════

        // INSS — base: saldo salário + 13° proporcional + aviso prévio indenizado
        //        Férias e 1/3 são isentas de INSS (art. 28 §9° Lei 8.212/91)
        $baseInss = $saldoSalario + $decimoTerceiro + $avisoPrevioValor;
        $inss     = self::calcularInss($baseInss);

        // IRRF — base: saldo salário + férias + 1/3 + 13°, menos INSS e dedução por dependente
        //        Aviso indenizado: isento de IRRF (Solução Consulta COSIT 215/2017)
        //        Multa FGTS: isenta (indenização — art. 6° Lei 7.713/88)
        $baseFeriasIrrf = $feriasProp + $umTercoProp + $feriasVenc + $umTercoVenc;
        $baseOutraIrrf  = $saldoSalario + $decimoTerceiro;
        $baseIrrf       = max(0.0, $baseFeriasIrrf + $baseOutraIrrf - $inss - ($numeroDependentes * 189.59));
        $irrf           = self::calcularIrrf($baseIrrf);

        // ═══════════════════════════════════════════════════════════
        //  TOTAIS
        // ═══════════════════════════════════════════════════════════

        $totalProventos = $saldoSalario
            + $feriasProp + $umTercoProp
            + $feriasVenc + $umTercoVenc
            + $decimoTerceiro
            + $avisoPrevioValor
            + $outrosCreditos;

        $totalDescontos = $inss + $irrf + $descontosExtras;
        $totalLiquido   = round($totalProventos - $totalDescontos, 2);

        return [
            // Dados de referência
            'meses_trabalhados'    => $mesesTotal,
            'anos_completos'       => $anosCompletos,
            'avos_ferias'          => $avosFer,
            'avos_decimo'          => $avos13,
            'dias_aviso'           => $diasAviso,
            'num_dependentes'      => $numeroDependentes,
            'tem_ferias_vencidas'  => $temFeriasVencidas,
            // Proventos
            'saldo_salario'        => $saldoSalario,
            'ferias_proporcionais' => $feriasProp,
            'ferias_vencidas'      => $feriasVenc,
            'um_terco_ferias'      => round($umTercoProp + $umTercoVenc, 2),
            'decimo_terceiro'      => $decimoTerceiro,
            'aviso_previo_valor'   => $avisoPrevioValor,
            'outros_creditos'      => $outrosCreditos,
            'total_bruto'          => round($totalProventos, 2),
            // Descontos
            'inss'                 => $inss,
            'irrf'                 => $irrf,
            'descontos'            => $descontosExtras,
            'total_descontos'      => round($totalDescontos, 2),
            // FGTS
            'saldo_fgts'           => $saldoFgtsReal,
            'multa_fgts'           => $multaFgts,
            'fgts_disponivel'      => $fgtsDisponivel,
            // Total
            'total_liquido'        => $totalLiquido,
            // Metadados
            'direitos'             => $dir,
        ];
    }

    // ═══════════════════════════════════════════════════════════════════
    //  HELPERS PRIVADOS
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Direitos por tipo de desligamento.
     * Referência: CLT arts. 477–484-A, Lei 8.036/90, jurisprudência TST.
     */
    private static function direitosPorTipo(string $tipo): array
    {
        return match ($tipo) {
            'sem_justa_causa' => [
                'ferias_proporcionais' => true,
                'ferias_vencidas'      => true,
                'decimo_terceiro'      => true,
                'aviso_previo'         => true,   // owed by employer
                'multa_fgts'           => true,   // 40%
            ],
            'voluntario' => [                      // Pedido de demissão
                'ferias_proporcionais' => true,
                'ferias_vencidas'      => true,
                'decimo_terceiro'      => true,
                'aviso_previo'         => false,   // empregado deve ao empregador
                'multa_fgts'           => false,
            ],
            'com_justa_causa' => [
                'ferias_proporcionais' => false,   // não devido
                'ferias_vencidas'      => true,    // devido — Súmula 171 TST (período completo)
                'decimo_terceiro'      => false,   // não devido
                'aviso_previo'         => false,
                'multa_fgts'           => false,
            ],
            'acordo_mutuo' => [                    // Art. 484-A CLT
                'ferias_proporcionais' => true,
                'ferias_vencidas'      => true,
                'decimo_terceiro'      => true,
                'aviso_previo'         => true,   // 50% do proporcional (tratado em calcular())
                'multa_fgts'           => true,   // 20%
            ],
            'aposentadoria' => [
                'ferias_proporcionais' => true,
                'ferias_vencidas'      => true,
                'decimo_terceiro'      => true,
                'aviso_previo'         => false,  // não exigido
                'multa_fgts'           => false,  // sem multa, mas saca o saldo integral
            ],
            default => [
                'ferias_proporcionais' => false,
                'ferias_vencidas'      => true,
                'decimo_terceiro'      => false,
                'aviso_previo'         => false,
                'multa_fgts'           => false,
            ],
        };
    }

    /**
     * Avos para férias proporcionais — regra dos 15 dias.
     * Cada mês do período aquisitivo em curso com 15+ dias trabalhados = 1 avo.
     * Máximo 11 (12 avos = período vencido, não proporcional).
     */
    private static function calcularAvosFeriasProporcionais(Carbon $admissao, Carbon $demissao): int
    {
        $mesesTotal = (int) $admissao->diffInMonths($demissao);
        $avos       = $mesesTotal % 12;

        // Mês da demissão: conta se trabalhou 15+ dias
        if ($demissao->day >= 15) {
            $avos++;
        }

        return min(11, $avos); // 12 = período vencido, não proporcional
    }

    /**
     * Avos para 13° proporcional — regra dos 15 dias aplicada ao ano corrente.
     * Considera o início do vínculo se admitido no mesmo ano.
     */
    private static function calcularAvos13(Carbon $admissao, Carbon $demissao): int
    {
        // Ponto de partida no ano da demissão
        $mesInicio = ($admissao->year < $demissao->year) ? 1 : $admissao->month;
        $diaInicio = ($admissao->year < $demissao->year) ? 1 : $admissao->day;

        $avos = 0;
        for ($m = $mesInicio; $m <= $demissao->month; $m++) {
            // Mês de admissão: trabalhou a partir do $diaInicio; conta se <= 15
            if ($m === $mesInicio && $diaInicio > 15) {
                continue;
            }
            // Mês da demissão: conta se trabalhou >= 15 dias
            if ($m === $demissao->month && $demissao->day < 15) {
                continue;
            }
            $avos++;
        }

        return min(12, $avos);
    }

    /**
     * Dias de aviso prévio proporcional — Lei 12.506/2011.
     * 30 dias base + 3 dias por ano completo de serviço, máximo 90 dias.
     */
    private static function diasAvisoProporcional(int $anosCompletos): int
    {
        return min(90, 30 + ($anosCompletos * 3));
    }

    /**
     * INSS — tabela progressiva 2024 (IN RFB 2.141/2023).
     * Base: saldo salário + 13° + aviso prévio indenizado.
     * Férias + 1/3 são ISENTAS de INSS.
     */
    private static function calcularInss(float $base): float
    {
        if ($base <= 0) return 0.0;

        // [limite_superior, alíquota]
        $faixas = [
            [1_412.00, 0.075],
            [2_666.68, 0.09],
            [4_000.03, 0.12],
            [7_786.02, 0.14],
        ];

        $inss     = 0.0;
        $anterior = 0.0;

        foreach ($faixas as [$limite, $aliquota]) {
            if ($base <= $anterior) break;
            $faixa = min($base, $limite) - $anterior;
            $inss += $faixa * $aliquota;
            $anterior = $limite;
            if ($base <= $limite) break;
        }

        // Acima do teto: não há mais incidência (tabela progressiva)
        return round($inss, 2);
    }

    /**
     * IRRF — tabela progressiva mensal 2024 (Lei 14.848/2024).
     * Base já deve estar líquida de INSS e dedução de dependentes.
     * Observação: aviso prévio indenizado e multa FGTS são isentos (não entram na base).
     */
    private static function calcularIrrf(float $baseCalculo): float
    {
        if ($baseCalculo <= 0) return 0.0;

        // Tabela progressiva: base → alíquota - parcela a deduzir
        if ($baseCalculo <= 2_259.20) return 0.0;
        if ($baseCalculo <= 2_826.65) return max(0.0, round($baseCalculo * 0.075 -  169.44, 2));
        if ($baseCalculo <= 3_751.05) return max(0.0, round($baseCalculo * 0.15  -  381.44, 2));
        if ($baseCalculo <= 4_664.68) return max(0.0, round($baseCalculo * 0.225 -  662.77, 2));

        return max(0.0, round($baseCalculo * 0.275 - 896.00, 2));
    }
}
