<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Template do Termo de Responsabilidade de Equipamento.
 * Suporta múltiplos templates; um deles é marcado como padrão (padrao = true).
 */
class ConfiguracaoTermo extends Model
{
    protected $table = 'configuracao_termos';

    protected $fillable = [
        'nome',
        'padrao',
        'titulo',
        'subtitulo',
        'intro_texto',
        'clausulas',
        'campos_visiveis',
        'campos_extras',
        'assinaturas',
        'rodape_texto',
        'logo',
        'logo_posicao',
        'atualizado_por',
    ];

    protected $casts = [
        'clausulas'       => 'array',
        'campos_visiveis' => 'array',
        'campos_extras'   => 'array',
        'assinaturas'     => 'array',
        'padrao'          => 'boolean',
    ];

    // ── Template padrão ────────────────────────────────────────────────────────

    /**
     * Retorna o template marcado como padrão.
     * Se não existir nenhum, cria o template inicial.
     */
    public static function instancia(): static
    {
        $padrao = static::where('padrao', true)->first();

        if (! $padrao) {
            // Tenta promover o primeiro existente
            $primeiro = static::orderBy('id')->first();
            if ($primeiro) {
                $primeiro->update(['padrao' => true]);
                return $primeiro->fresh();
            }

            // Cria o template inicial
            $padrao = static::create([
                'nome'            => 'Padrão',
                'padrao'          => true,
                'titulo'          => 'Termo de Entrega e Responsabilidade de Equipamento',
                'subtitulo'       => '',
                'intro_texto'     => '',
                'clausulas'       => static::clausulasPadrao(),
                'campos_visiveis' => static::camposPadraoVisiveis(),
                'campos_extras'   => static::camposExtrasPadrao(),
                'assinaturas'     => static::assinaturasPadrao(),
                'rodape_texto'    => 'Documento de controle interno',
                'logo_posicao'    => 'esquerda',
            ]);
        }

        return $padrao;
    }

    /**
     * Define este template como padrão, removendo o status dos demais.
     */
    public function definirComoPadrao(): void
    {
        static::where('padrao', true)->update(['padrao' => false]);
        $this->update(['padrao' => true]);
    }

    /**
     * Cria uma cópia deste template com o nome fornecido.
     */
    public function duplicar(string $nome): static
    {
        $novo = $this->replicate(['padrao']);
        $novo->nome   = $nome;
        $novo->padrao = false;
        $novo->save();
        return $novo;
    }

    // ── Padrões ────────────────────────────────────────────────────────────────

    public static function clausulasPadrao(): array
    {
        return [
            ['texto' => 'O funcionário recebe o equipamento acima descrito em **comodato**, permanecendo o bem de propriedade da empresa.'],
            ['texto' => 'O equipamento deverá ser utilizado exclusivamente para fins profissionais, em conformidade com as políticas internas da empresa.'],
            ['texto' => 'O funcionário se responsabiliza pelo **uso adequado, guarda e conservação** do equipamento durante o período de posse.'],
            ['texto' => 'Em caso de dano, perda ou furto, o funcionário deverá comunicar imediatamente ao setor de TI e/ou RH, respondendo civilmente pelos prejuízos causados por negligência ou dolo.'],
            ['texto' => 'O equipamento deverá ser devolvido em **perfeito estado de conservação** ao término do vínculo empregatício ou quando solicitado pela empresa.'],
            ['texto' => 'Alterações, instalações de software não autorizado ou modificações físicas são vedadas sem prévia autorização da TI.'],
            ['texto' => 'O presente termo tem validade a partir da data de assinatura e poderá ser rescindido a qualquer momento pela empresa.'],
        ];
    }

    public static function camposPadraoVisiveis(): array
    {
        return [
            'numero_serie',
            'codigo_patrimonio',
            'marca',
            'modelo',
            'condicao',
            'data_entrega',
            'garantia',
            'departamento',
            'cargo',
            'email',
        ];
    }

    public static function assinaturasPadrao(): array
    {
        return [
            ['label' => 'Funcionário',       'papel' => 'Recebedor do equipamento'],
            ['label' => 'Responsável RH/TI', 'papel' => 'Entregue por'],
        ];
    }

    public static function camposExtrasPadrao(): array
    {
        return [
            ['id' => 'cpf',             'label' => 'CPF',                  'tipo' => 'predefinido', 'ativo' => false, 'mascara' => '000.000.000-00'],
            ['id' => 'rg',              'label' => 'RG',                   'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
            ['id' => 'telefone',        'label' => 'Telefone',             'tipo' => 'predefinido', 'ativo' => false, 'mascara' => '(00) 00000-0000'],
            ['id' => 'matricula',       'label' => 'Matrícula',            'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
            ['id' => 'data_nascimento', 'label' => 'Data de Nascimento',   'tipo' => 'predefinido', 'ativo' => false, 'mascara' => '00/00/0000'],
            ['id' => 'endereco',        'label' => 'Endereço Residencial', 'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
            ['id' => 'ramal',           'label' => 'Ramal',                'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
        ];
    }

    // ── Relações ───────────────────────────────────────────────────────────────

    public function atualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atualizado_por');
    }
}
