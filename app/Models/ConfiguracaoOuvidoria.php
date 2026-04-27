<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Configuração global do auto-encerramento de ouvidorias.
 * Singleton: sempre existe no máximo um registro.
 */
class ConfiguracaoOuvidoria extends Model
{
    protected $table = 'configuracoes_ouvidoria';

    protected $fillable = [
        'auto_encerramento_ativo',
        'prazo_horas',
        'mensagem_auto_encerramento',
    ];

    protected $casts = [
        'auto_encerramento_ativo' => 'boolean',
        'prazo_horas'             => 'integer',
    ];

    // ──────────────────────────────────────────────────────────────────────────
    // SINGLETON HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Retorna o registro único de configuração.
     * Cria um com valores padrão se ainda não existir.
     */
    public static function instancia(): static
    {
        return static::firstOrCreate([], [
            'auto_encerramento_ativo'      => false,
            'prazo_horas'                  => 72,
            'mensagem_auto_encerramento'   =>
                'Esta manifestação foi encerrada automaticamente por inatividade. ' .
                'Caso necessite de atendimento adicional, abra uma nova manifestação. ' .
                'Agradecemos o contato.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS DE PRAZO
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Retorna o prazo em dias (arredondado para exibição).
     */
    public function prazoDias(): float
    {
        return round($this->prazo_horas / 24, 1);
    }

    /**
     * Define o prazo a partir de uma quantidade de dias.
     */
    public function setPrazoDias(float $dias): void
    {
        $this->prazo_horas = (int) round($dias * 24);
    }
}
