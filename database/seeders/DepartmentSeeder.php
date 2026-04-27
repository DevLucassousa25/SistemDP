<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Tecnologia',
                'description' => 'TI e desenvolvimento de software',
            ],
            [
                'name' => 'Comercial',
                'description' => 'Vendas e relacionamento com clientes',
            ],
            [
                'name' => 'Financeiro',
                'description' => 'Gestão financeira',
            ],
            [
                'name' => 'Recursos Humanos',
                'description' => 'Gestão de pessoas e cultura',
            ],
            [
                'name' => 'Marketing',
                'description' => 'Estratégias de divulgação e marca',
            ],
            [
                'name' => 'Jurídico',
                'description' => 'Assuntos legais e compliance',
            ],
            [
                'name' => 'Operações',
                'description' => 'Gestão de processos operacionais',
            ],
            [
                'name' => 'Logística',
                'description' => 'Transporte e distribuição de produtos',
            ],
            [
                'name' => 'Atendimento ao Cliente',
                'description' => 'Suporte e atendimento ao consumidor',
            ],
            [
                'name' => 'Pesquisa e Desenvolvimento',
                'description' => 'Inovação e criação de novos produtos',
            ],
            [
                'name' => 'Compras',
                'description' => 'Aquisição de materiais e serviços',
            ],
            [
                'name' => 'Qualidade',
                'description' => 'Controle e garantia de qualidade',
            ],
            [
                'name' => 'Engenharia',
                'description' => 'Projetos técnicos e desenvolvimento de soluções',
            ],
            [
                'name' => 'Segurança da Informação',
                'description' => 'Proteção de dados e sistemas',
            ],
            [
                'name' => 'Auditoria',
                'description' => 'Análise e verificação de processos internos',
            ],
            [
                'name' => 'Controladoria',
                'description' => 'Controle financeiro e planejamento orçamentário',
            ],
            [
                'name' => 'Relações Institucionais',
                'description' => 'Relacionamento com entidades e órgãos externos',
            ],
            [
                'name' => 'Comunicação',
                'description' => 'Comunicação interna e externa da empresa',
            ],
            [
                'name' => 'Design',
                'description' => 'Criação visual e experiência do usuário',
            ],
            [
                'name' => 'Suporte Técnico',
                'description' => 'Atendimento técnico e resolução de problemas',
            ],
            [
                'name' => 'Treinamento e Desenvolvimento',
                'description' => 'Capacitação e desenvolvimento de colaboradores',
            ],
            [
                'name' => 'Planejamento Estratégico',
                'description' => 'Definição de metas e estratégias organizacionais',
            ],
            [
                'name' => 'Expansão',
                'description' => 'Crescimento e abertura de novos mercados',
            ],
            [
                'name' => 'Sustentabilidade',
                'description' => 'Práticas ambientais e responsabilidade social',
            ],
        ];


        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
