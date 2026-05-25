<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Configurações globais da empresa.
 * Singleton: sempre existe no máximo um registro.
 */
class ConfiguracaoEmpresa extends Model
{
    protected $table = 'configuracoes_empresa';

    protected $fillable = [
        'nome_empresa',
        'email_dominio',
        'cnpj',
        'telefone',
        'site',
    ];

    // ── Singleton ─────────────────────────────────────────────────────

    public static function instancia(): static
    {
        return static::firstOrCreate([], [
            'nome_empresa'  => null,
            'email_dominio' => null,
            'cnpj'          => null,
            'telefone'      => null,
            'site'          => null,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /**
     * Retorna o domínio cadastrado, ou string vazia se não configurado.
     */
    public static function dominio(): string
    {
        return static::instancia()->email_dominio ?? '';
    }

    /**
     * Gera e-mail corporativo no formato nome.sobrenome@dominio.com
     * a partir do nome completo do candidato.
     *
     * Ex: "João Victor da Silva" → "joao.victor@empresa.com"
     *     (usa primeiro + segundo nome para evitar conflitos)
     */
    public static function gerarEmail(string $nomeCompleto): string
    {
        $dominio = static::dominio();
        if (!$dominio) return '';

        // Normaliza e remove acentos
        $nome = static::normalizarTexto($nomeCompleto);

        // Separa partes do nome, ignorando artigos/preposições
        $ignorar = ['de', 'da', 'do', 'dos', 'das', 'e', 'van', 'von', 'del'];
        $partes  = array_values(array_filter(
            explode(' ', $nome),
            fn($p) => strlen($p) > 0 && !in_array($p, $ignorar)
        ));

        $primeiro = $partes[0] ?? '';
        $segundo  = $partes[1] ?? '';

        if ($primeiro && $segundo) {
            $local = "{$primeiro}.{$segundo}";
        } elseif ($primeiro) {
            $local = $primeiro;
        } else {
            return '';
        }

        // Remove caracteres inválidos em endereços de e-mail
        $local = preg_replace('/[^a-z0-9._\-]/', '', $local);

        return "{$local}@{$dominio}";
    }

    /**
     * Converte string para minúsculas sem acentos.
     */
    private static function normalizarTexto(string $texto): string
    {
        $texto = mb_strtolower(trim($texto), 'UTF-8');

        // Tabela de transliteração de caracteres acentuados
        $from = ['á','à','ã','â','ä','é','è','ê','ë','í','ì','î','ï',
                 'ó','ò','õ','ô','ö','ú','ù','û','ü','ç','ñ',
                 'Á','À','Ã','Â','Ä','É','È','Ê','Ë','Í','Ì','Î','Ï',
                 'Ó','Ò','Õ','Ô','Ö','Ú','Ù','Û','Ü','Ç','Ñ'];
        $to   = ['a','a','a','a','a','e','e','e','e','i','i','i','i',
                 'o','o','o','o','o','u','u','u','u','c','n',
                 'a','a','a','a','a','e','e','e','e','i','i','i','i',
                 'o','o','o','o','o','u','u','u','u','c','n'];

        return str_replace($from, $to, $texto);
    }
}
