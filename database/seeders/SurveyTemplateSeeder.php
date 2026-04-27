<?php

namespace Database\Seeders;

use App\Models\SurveyTemplate;
use Illuminate\Database\Seeder;

class SurveyTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [

            // ─── NPS Padrão ───────────────────────────────────────────────
            [
                'name'        => 'NPS Padrão',
                'description' => 'Meça o índice de lealdade dos colaboradores com a metodologia Net Promoter Score.',
                'category'    => 'nps',
                'is_system'   => true,
                'created_by'  => null,
                'questions'   => [
                    [
                        'question' => 'Em uma escala de 0 a 10, o quanto você recomendaria nossa empresa como lugar para trabalhar?',
                        'type'     => 'escala',
                        'options'  => null,
                        'required' => true,
                        'order'    => 1,
                    ],
                    [
                        'question' => 'O que mais contribuiu para a nota que você deu?',
                        'type'     => 'texto_livre',
                        'options'  => null,
                        'required' => false,
                        'order'    => 2,
                    ],
                    [
                        'question' => 'O que poderíamos fazer para melhorar a sua experiência?',
                        'type'     => 'texto_livre',
                        'options'  => null,
                        'required' => false,
                        'order'    => 3,
                    ],
                ],
            ],

            // ─── Avaliação de Clima Organizacional ────────────────────────
            [
                'name'        => 'Avaliação de Clima',
                'description' => 'Avalie o ambiente de trabalho, comunicação, liderança e bem-estar dos colaboradores.',
                'category'    => 'clima',
                'is_system'   => true,
                'created_by'  => null,
                'questions'   => [
                    [
                        'question' => 'Como você avalia o ambiente de trabalho no seu departamento?',
                        'type'     => 'escala',
                        'options'  => null,
                        'required' => true,
                        'order'    => 1,
                    ],
                    [
                        'question' => 'Como você avalia a comunicação entre a equipe?',
                        'type'     => 'escala',
                        'options'  => null,
                        'required' => true,
                        'order'    => 2,
                    ],
                    [
                        'question' => 'Como você avalia a liderança do seu gestor imediato?',
                        'type'     => 'escala',
                        'options'  => null,
                        'required' => true,
                        'order'    => 3,
                    ],
                    [
                        'question' => 'Você se sente valorizado(a) pela empresa?',
                        'type'     => 'multipla_escolha',
                        'options'  => ['Sim, completamente', 'Parcialmente', 'Não me sinto valorizado(a)'],
                        'required' => true,
                        'order'    => 4,
                    ],
                    [
                        'question' => 'Quais são os principais pontos positivos do ambiente de trabalho?',
                        'type'     => 'texto_livre',
                        'options'  => null,
                        'required' => false,
                        'order'    => 5,
                    ],
                    [
                        'question' => 'O que poderia ser melhorado no ambiente de trabalho?',
                        'type'     => 'texto_livre',
                        'options'  => null,
                        'required' => false,
                        'order'    => 6,
                    ],
                ],
            ],

            // ─── Feedback de Treinamento ──────────────────────────────────
            [
                'name'        => 'Feedback de Treinamento',
                'description' => 'Colete avaliações sobre a qualidade, conteúdo e aplicabilidade de treinamentos realizados.',
                'category'    => 'feedback',
                'is_system'   => true,
                'created_by'  => null,
                'questions'   => [
                    [
                        'question' => 'Como você avalia o conteúdo abordado no treinamento?',
                        'type'     => 'escala',
                        'options'  => null,
                        'required' => true,
                        'order'    => 1,
                    ],
                    [
                        'question' => 'Como você avalia a qualidade do material e da apresentação?',
                        'type'     => 'escala',
                        'options'  => null,
                        'required' => true,
                        'order'    => 2,
                    ],
                    [
                        'question' => 'O treinamento atendeu às suas expectativas?',
                        'type'     => 'multipla_escolha',
                        'options'  => ['Sim, superou as expectativas', 'Atendeu parcialmente', 'Não atendeu às expectativas'],
                        'required' => true,
                        'order'    => 3,
                    ],
                    [
                        'question' => 'O que você aprendeu que poderá aplicar diretamente no seu trabalho?',
                        'type'     => 'texto_livre',
                        'options'  => null,
                        'required' => false,
                        'order'    => 4,
                    ],
                    [
                        'question' => 'Que sugestões você tem para melhorar os próximos treinamentos?',
                        'type'     => 'texto_livre',
                        'options'  => null,
                        'required' => false,
                        'order'    => 5,
                    ],
                ],
            ],

            // ─── Satisfação com o RH ──────────────────────────────────────
            [
                'name'        => 'Satisfação com o RH',
                'description' => 'Avalie o atendimento, processos e serviços oferecidos pelo setor de Recursos Humanos.',
                'category'    => 'rh',
                'is_system'   => true,
                'created_by'  => null,
                'questions'   => [
                    [
                        'question' => 'Como você avalia o atendimento do setor de RH?',
                        'type'     => 'escala',
                        'options'  => null,
                        'required' => true,
                        'order'    => 1,
                    ],
                    [
                        'question' => 'O RH responde às suas dúvidas e solicitações de forma eficiente?',
                        'type'     => 'multipla_escolha',
                        'options'  => ['Sempre', 'Na maioria das vezes', 'Às vezes', 'Raramente'],
                        'required' => true,
                        'order'    => 2,
                    ],
                    [
                        'question' => 'Como você avalia os benefícios oferecidos pela empresa?',
                        'type'     => 'escala',
                        'options'  => null,
                        'required' => true,
                        'order'    => 3,
                    ],
                    [
                        'question' => 'Os processos de RH (admissão, férias, folha de ponto) são claros e eficientes?',
                        'type'     => 'multipla_escolha',
                        'options'  => ['Sim, são muito claros', 'Poderiam ser melhorados', 'São confusos ou demorados'],
                        'required' => true,
                        'order'    => 4,
                    ],
                    [
                        'question' => 'Que melhorias você sugere para o setor de RH?',
                        'type'     => 'texto_livre',
                        'options'  => null,
                        'required' => false,
                        'order'    => 5,
                    ],
                ],
            ],

        ];

        foreach ($templates as $data) {
            SurveyTemplate::firstOrCreate(
                ['name' => $data['name'], 'is_system' => true],
                $data
            );
        }
    }
}
