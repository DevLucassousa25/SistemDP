<?php

namespace Database\Seeders;

use App\Models\ConfiguracaoTermo;
use Illuminate\Database\Seeder;

class TermoTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que o template Padrão existe (cria se necessário)
        ConfiguracaoTermo::instancia();

        $templates = [

            // ── Notebook / Desktop ─────────────────────────────────────────────
            [
                'nome'            => 'Notebook / Desktop',
                'padrao'          => false,
                'titulo'          => 'Termo de Responsabilidade — Computador',
                'subtitulo'       => 'Entrega de Equipamento de Informática',
                'intro_texto'     => 'Pelo presente instrumento, a empresa entrega ao colaborador abaixo identificado o equipamento de informática descrito neste documento, ficando o colaborador ciente das condições e responsabilidades estabelecidas neste termo.',
                'clausulas'       => [
                    ['texto' => 'O colaborador recebe o equipamento de informática descrito acima em **comodato**, permanecendo o bem de propriedade exclusiva da empresa.'],
                    ['texto' => 'O equipamento deverá ser utilizado **exclusivamente para fins profissionais**, sendo vedado seu uso pessoal ou de terceiros sem autorização prévia da TI.'],
                    ['texto' => 'O colaborador compromete-se a **zelar pela integridade física e lógica** do equipamento, mantendo-o em boas condições de uso e armazenamento.'],
                    ['texto' => 'É expressamente proibida a instalação de softwares não licenciados, jogos, aplicativos pessoais ou qualquer conteúdo não relacionado às atividades profissionais.'],
                    ['texto' => 'O acesso à internet deve ser feito de forma responsável, sendo vedado o acesso a sites de conteúdo impróprio, baixar torrents ou utilizar a rede corporativa para fins pessoais.'],
                    ['texto' => 'Em caso de mau funcionamento, o colaborador deve reportar imediatamente ao setor de TI, sendo **vedada qualquer tentativa de conserto ou manutenção** por conta própria.'],
                    ['texto' => 'Em caso de perda, roubo ou dano por negligência, o colaborador responderá **civilmente pelos prejuízos causados**, podendo o custo de reposição ser descontado conforme legislação vigente.'],
                    ['texto' => 'O equipamento deverá ser devolvido em **perfeito estado de conservação**, com todos os acessórios recebidos, ao término do vínculo empregatício ou quando solicitado pela empresa.'],
                    ['texto' => 'A empresa reserva-se o direito de **auditar o equipamento** a qualquer momento, sendo que o colaborador não possui expectativa de privacidade em relação ao seu conteúdo.'],
                ],
                'campos_visiveis' => [
                    'numero_serie', 'codigo_patrimonio', 'marca', 'modelo',
                    'condicao', 'data_entrega', 'garantia', 'departamento', 'cargo', 'email',
                ],
                'campos_extras'   => [
                    ['id' => 'cpf',             'label' => 'CPF',                  'tipo' => 'predefinido', 'ativo' => true,  'mascara' => '000.000.000-00'],
                    ['id' => 'rg',              'label' => 'RG',                   'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                    ['id' => 'telefone',        'label' => 'Telefone',             'tipo' => 'predefinido', 'ativo' => false, 'mascara' => '(00) 00000-0000'],
                    ['id' => 'matricula',       'label' => 'Matrícula',            'tipo' => 'predefinido', 'ativo' => true,  'mascara' => ''],
                    ['id' => 'data_nascimento', 'label' => 'Data de Nascimento',   'tipo' => 'predefinido', 'ativo' => false, 'mascara' => '00/00/0000'],
                    ['id' => 'endereco',        'label' => 'Endereço Residencial', 'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                    ['id' => 'ramal',           'label' => 'Ramal',                'tipo' => 'predefinido', 'ativo' => true,  'mascara' => ''],
                ],
                'assinaturas'     => [
                    ['label' => 'Colaborador',        'papel' => 'Recebedor do equipamento'],
                    ['label' => 'Responsável de TI',  'papel' => 'Entregue por'],
                    ['label' => 'Gestor Imediato',    'papel' => 'Ciente'],
                ],
                'rodape_texto'    => 'Documento de controle de TI — uso interno',
                'logo_posicao'    => 'esquerda',
            ],

            // ── EPI ────────────────────────────────────────────────────────────
            [
                'nome'            => 'EPI — Equipamento de Proteção',
                'padrao'          => false,
                'titulo'          => 'Termo de Entrega de EPI',
                'subtitulo'       => 'Equipamento de Proteção Individual',
                'intro_texto'     => 'Em atendimento à NR-6 (Norma Regulamentadora nº 6 do Ministério do Trabalho e Emprego), a empresa fornece ao colaborador abaixo identificado o(s) Equipamento(s) de Proteção Individual (EPI) relacionado(s) neste documento, de forma gratuita e com as orientações de uso adequado.',
                'clausulas'       => [
                    ['texto' => 'O colaborador recebe o(s) EPI(s) listado(s) **gratuitamente**, conforme obrigação prevista na NR-6 e na CLT.'],
                    ['texto' => 'O colaborador é **obrigado a utilizar o EPI** sempre que exposto ao risco para o qual o equipamento foi designado, sendo sua recusa passível de sanção disciplinar.'],
                    ['texto' => 'É dever do colaborador **conservar, guardar e higienizar** o EPI conforme orientações recebidas, comunicando qualquer defeito ou dano ao responsável imediatamente.'],
                    ['texto' => 'O EPI **não pode ser transferido** a outros colaboradores, salvo após higienização adequada e com autorização da empresa.'],
                    ['texto' => 'Em caso de **extravio ou dano por negligência**, o colaborador poderá ser responsabilizado pelos custos de reposição, conforme previsto na legislação trabalhista.'],
                    ['texto' => 'O colaborador declara ter recebido **treinamento e orientação** sobre o uso correto do EPI entregue, estando ciente dos riscos inerentes à sua função.'],
                    ['texto' => 'O EPI deverá ser **devolvido** ao término do vínculo empregatício ou quando solicitado pela empresa, em condições compatíveis com o desgaste natural de uso.'],
                ],
                'campos_visiveis' => [
                    'numero_serie', 'codigo_patrimonio', 'marca', 'modelo',
                    'condicao', 'data_entrega', 'garantia', 'departamento', 'cargo',
                ],
                'campos_extras'   => [
                    ['id' => 'cpf',             'label' => 'CPF',                  'tipo' => 'predefinido', 'ativo' => true,  'mascara' => '000.000.000-00'],
                    ['id' => 'rg',              'label' => 'RG',                   'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                    ['id' => 'telefone',        'label' => 'Telefone',             'tipo' => 'predefinido', 'ativo' => false, 'mascara' => '(00) 00000-0000'],
                    ['id' => 'matricula',       'label' => 'Matrícula',            'tipo' => 'predefinido', 'ativo' => true,  'mascara' => ''],
                    ['id' => 'data_nascimento', 'label' => 'Data de Nascimento',   'tipo' => 'predefinido', 'ativo' => false, 'mascara' => '00/00/0000'],
                    ['id' => 'endereco',        'label' => 'Endereço Residencial', 'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                    ['id' => 'ramal',           'label' => 'Ramal',                'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                ],
                'assinaturas'     => [
                    ['label' => 'Colaborador',         'papel' => 'Recebedor do EPI'],
                    ['label' => 'Responsável de SESMT','papel' => 'Entregue por'],
                ],
                'rodape_texto'    => 'Documento obrigatório por força da NR-6 — Ministério do Trabalho e Emprego',
                'logo_posicao'    => 'esquerda',
            ],

            // ── Celular Corporativo ────────────────────────────────────────────
            [
                'nome'            => 'Celular Corporativo',
                'padrao'          => false,
                'titulo'          => 'Termo de Responsabilidade — Celular Corporativo',
                'subtitulo'       => 'Entrega de Dispositivo Móvel',
                'intro_texto'     => 'A empresa entrega ao colaborador abaixo identificado o dispositivo móvel descrito neste documento, para uso exclusivo em atividades profissionais, nas condições estabelecidas neste termo.',
                'clausulas'       => [
                    ['texto' => 'O colaborador recebe o dispositivo móvel em **comodato**, permanecendo o bem de propriedade da empresa.'],
                    ['texto' => 'O dispositivo é fornecido **exclusivamente para uso profissional**, sendo vedado o uso pessoal excessivo ou de terceiros.'],
                    ['texto' => 'O plano de dados e a linha telefônica são de **titularidade da empresa**, que poderá monitorar o consumo a qualquer momento.'],
                    ['texto' => 'O colaborador compromete-se a **não ultrapassar o limite de franquia** de dados e ligações definido pela empresa, sendo responsável pelos custos excedentes causados por uso pessoal.'],
                    ['texto' => 'É **proibida a instalação de aplicativos** não relacionados às atividades profissionais sem autorização prévia da TI.'],
                    ['texto' => 'O dispositivo **não deve ser rooteado, desbloqueado (jailbreak)** ou ter seu software original alterado de qualquer forma.'],
                    ['texto' => 'Em caso de perda, furto ou roubo, o colaborador deve **comunicar imediatamente** ao setor de TI para bloqueio remoto do dispositivo e da linha, e registrar Boletim de Ocorrência.'],
                    ['texto' => 'Em caso de dano por negligência ou mau uso, o colaborador responderá pelos custos de reparo ou substituição do equipamento.'],
                    ['texto' => 'O dispositivo e o chip SIM deverão ser **devolvidos em perfeito estado** ao término do vínculo empregatício ou quando solicitado pela empresa.'],
                ],
                'campos_visiveis' => [
                    'numero_serie', 'codigo_patrimonio', 'marca', 'modelo',
                    'condicao', 'data_entrega', 'garantia', 'departamento', 'cargo', 'email',
                ],
                'campos_extras'   => [
                    ['id' => 'cpf',             'label' => 'CPF',                  'tipo' => 'predefinido', 'ativo' => true,  'mascara' => '000.000.000-00'],
                    ['id' => 'rg',              'label' => 'RG',                   'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                    ['id' => 'telefone',        'label' => 'Telefone Pessoal',     'tipo' => 'predefinido', 'ativo' => true,  'mascara' => '(00) 00000-0000'],
                    ['id' => 'matricula',       'label' => 'Matrícula',            'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                    ['id' => 'data_nascimento', 'label' => 'Data de Nascimento',   'tipo' => 'predefinido', 'ativo' => false, 'mascara' => '00/00/0000'],
                    ['id' => 'endereco',        'label' => 'Endereço Residencial', 'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                    ['id' => 'ramal',           'label' => 'Ramal',                'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                ],
                'assinaturas'     => [
                    ['label' => 'Colaborador',       'papel' => 'Recebedor do dispositivo'],
                    ['label' => 'Responsável de TI', 'papel' => 'Entregue por'],
                ],
                'rodape_texto'    => 'Documento de controle de ativos móveis — uso interno',
                'logo_posicao'    => 'esquerda',
            ],

            // ── Simplificado ───────────────────────────────────────────────────
            [
                'nome'            => 'Simplificado',
                'padrao'          => false,
                'titulo'          => 'Recibo de Entrega de Equipamento',
                'subtitulo'       => '',
                'intro_texto'     => 'Declaro ter recebido da empresa o equipamento descrito abaixo, em bom estado de conservação, comprometendo-me a utilizá-lo com responsabilidade e devolvê-lo nas mesmas condições quando solicitado.',
                'clausulas'       => [
                    ['texto' => 'Recebo o equipamento acima em **comodato**, reconhecendo que é de propriedade da empresa e devo conservá-lo adequadamente.'],
                    ['texto' => 'Comprometo-me a **utilizar o equipamento exclusivamente para fins profissionais** e a devolvê-lo em perfeito estado ao término do vínculo ou quando solicitado.'],
                    ['texto' => 'Em caso de dano, perda ou furto, comunicarei imediatamente à empresa e assumo responsabilidade civil pelos prejuízos causados por **negligência ou dolo**.'],
                ],
                'campos_visiveis' => [
                    'numero_serie', 'marca', 'modelo', 'condicao', 'data_entrega',
                ],
                'campos_extras'   => [
                    ['id' => 'cpf',             'label' => 'CPF',                  'tipo' => 'predefinido', 'ativo' => false, 'mascara' => '000.000.000-00'],
                    ['id' => 'rg',              'label' => 'RG',                   'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                    ['id' => 'telefone',        'label' => 'Telefone',             'tipo' => 'predefinido', 'ativo' => false, 'mascara' => '(00) 00000-0000'],
                    ['id' => 'matricula',       'label' => 'Matrícula',            'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                    ['id' => 'data_nascimento', 'label' => 'Data de Nascimento',   'tipo' => 'predefinido', 'ativo' => false, 'mascara' => '00/00/0000'],
                    ['id' => 'endereco',        'label' => 'Endereço Residencial', 'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                    ['id' => 'ramal',           'label' => 'Ramal',                'tipo' => 'predefinido', 'ativo' => false, 'mascara' => ''],
                ],
                'assinaturas'     => [
                    ['label' => 'Colaborador',  'papel' => 'Recebedor'],
                    ['label' => 'Responsável',  'papel' => 'Entregue por'],
                ],
                'rodape_texto'    => 'Documento de controle interno',
                'logo_posicao'    => 'esquerda',
            ],

            // ── Dados Pessoais Completos ───────────────────────────────────────
            [
                'nome'            => 'Dados Pessoais Completos',
                'padrao'          => false,
                'titulo'          => 'Termo de Entrega e Responsabilidade de Equipamento',
                'subtitulo'       => 'Documento com Identificação Completa do Colaborador',
                'intro_texto'     => 'Pelo presente instrumento, a empresa entrega ao colaborador abaixo plenamente identificado o equipamento descrito, ficando o mesmo ciente das condições, obrigações e responsabilidades estabelecidas neste termo.',
                'clausulas'       => [
                    ['texto' => 'O colaborador recebe o equipamento acima descrito em **comodato**, permanecendo o bem de propriedade da empresa.'],
                    ['texto' => 'O equipamento deverá ser utilizado exclusivamente para fins profissionais, em conformidade com as políticas internas da empresa.'],
                    ['texto' => 'O colaborador se responsabiliza pelo **uso adequado, guarda e conservação** do equipamento durante o período de posse.'],
                    ['texto' => 'Em caso de dano, perda ou furto, o colaborador deverá comunicar imediatamente ao setor de TI e/ou RH, respondendo civilmente pelos prejuízos causados por negligência ou dolo.'],
                    ['texto' => 'O equipamento deverá ser devolvido em **perfeito estado de conservação** ao término do vínculo empregatício ou quando solicitado pela empresa.'],
                    ['texto' => 'Alterações, instalações de software não autorizado ou modificações físicas são vedadas sem prévia autorização da TI.'],
                    ['texto' => 'As informações pessoais fornecidas neste documento são coletadas exclusivamente para fins de identificação e controle de ativos, em conformidade com a **Lei Geral de Proteção de Dados (LGPD — Lei nº 13.709/2018)**.'],
                    ['texto' => 'O presente termo tem validade a partir da data de assinatura e poderá ser rescindido a qualquer momento pela empresa.'],
                ],
                'campos_visiveis' => [
                    'numero_serie', 'codigo_patrimonio', 'marca', 'modelo',
                    'condicao', 'data_entrega', 'garantia', 'departamento', 'cargo', 'email',
                ],
                'campos_extras'   => [
                    ['id' => 'cpf',             'label' => 'CPF',                  'tipo' => 'predefinido', 'ativo' => true, 'mascara' => '000.000.000-00'],
                    ['id' => 'rg',              'label' => 'RG',                   'tipo' => 'predefinido', 'ativo' => true, 'mascara' => ''],
                    ['id' => 'telefone',        'label' => 'Telefone',             'tipo' => 'predefinido', 'ativo' => true, 'mascara' => '(00) 00000-0000'],
                    ['id' => 'matricula',       'label' => 'Matrícula',            'tipo' => 'predefinido', 'ativo' => true, 'mascara' => ''],
                    ['id' => 'data_nascimento', 'label' => 'Data de Nascimento',   'tipo' => 'predefinido', 'ativo' => true, 'mascara' => '00/00/0000'],
                    ['id' => 'endereco',        'label' => 'Endereço Residencial', 'tipo' => 'predefinido', 'ativo' => true, 'mascara' => ''],
                    ['id' => 'ramal',           'label' => 'Ramal',                'tipo' => 'predefinido', 'ativo' => true, 'mascara' => ''],
                ],
                'assinaturas'     => [
                    ['label' => 'Colaborador',        'papel' => 'Recebedor do equipamento'],
                    ['label' => 'Responsável RH/TI',  'papel' => 'Entregue por'],
                    ['label' => 'Testemunha',          'papel' => 'Testemunha'],
                ],
                'rodape_texto'    => 'Documento de controle interno — dados tratados conforme LGPD (Lei nº 13.709/2018)',
                'logo_posicao'    => 'esquerda',
            ],

        ];

        foreach ($templates as $data) {
            // Evita duplicatas por nome
            if (! ConfiguracaoTermo::where('nome', $data['nome'])->exists()) {
                ConfiguracaoTermo::create($data);
            }
        }

        $this->command->info('✓ ' . count($templates) . ' templates de Termo criados/verificados.');
    }
}
