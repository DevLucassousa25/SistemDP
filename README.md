# SistemDP — Plataforma de Gestão de Pessoas

Sistema web para RH e Departamento Pessoal que reúne em um só lugar o ciclo completo do colaborador: do recrutamento ao desligamento, passando por treinamentos, avaliação de desempenho, metas e comunicação interna.

Desenvolvido com **Laravel 12**, **Livewire 3** e **PostgreSQL**, com ambiente completo em **Docker**.

<!-- Adicione aqui 2 ou 3 prints das telas principais (dashboard, pipeline de recrutamento, treinamentos) -->

---

## Funcionalidades

### Recrutamento e seleção
- Cadastro de vagas com etapas personalizáveis e página pública de candidatura
- Pipeline de candidatos em quadro, com histórico e comentários por candidatura
- Banco de currículos com extração automática de texto de PDF/DOCX (processada em fila) para busca por conteúdo
- Testes online para candidatos, acessados por link com token

### Ciclo do colaborador
- **Onboarding** com tarefas por novo colaborador
- **Desligamento** com checklist, entrevista de desligamento por link e cálculo de verbas rescisórias (CLT)
- Solicitações dos funcionários ao RH/DP com acompanhamento de status
- Organograma, departamentos e perfis de acesso (Administrador, RH, CEO, gestor, colaborador)

### Desenvolvimento e desempenho
- Ciclos de avaliação de desempenho com critérios ponderados, autoavaliação e calibração
- **PDI** (Plano de Desenvolvimento Individual) com metas, ações e modelos reutilizáveis
- **OKRs** com objetivos, resultados-chave e check-ins
- Plano de sucessão
- Feedbacks com plano de ação e histórico de status

### Treinamentos (LMS)
- Catálogo de cursos com aulas, controle de progresso e vídeo obrigatório
- Trilhas de aprendizagem e treinamentos obrigatórios
- Provas, avaliação de reação e dúvidas por aula
- Emissão de certificado em PDF e relatórios para o RH

### Clima e comunicação interna
- Pesquisas de clima com modelos, comparativo entre pesquisas e ranking de gestores
- Check-in de humor diário com painel por equipe
- Feed social (posts, reações, comentários, enquetes, salvos) e comunidades com eventos
- Publicações oficiais com confirmação de leitura
- Ouvidoria com anexos, respostas e encerramento automático

### Operação
- Reserva de salas com imagens e controle de manutenção
- Reuniões com pauta e atas
- Gestão de tarefas com subtarefas, tags e comentários
- Controle de equipamentos: atribuição, manutenção, inventário e termo de responsabilidade em PDF
- Calendário corporativo com importação de feriados nacionais
- Notificações internas para os principais eventos do sistema

### Relatórios
- Exportação em **PDF** e **Excel** de pesquisas, avaliações, feedbacks e humor das equipes

---

## Tecnologias

| Camada | Tecnologias |
|---|---|
| Back-end | PHP 8.3, Laravel 12, Livewire 3 |
| Front-end | Blade, Tailwind CSS 4, Vite, ApexCharts |
| Banco de dados | PostgreSQL 16 |
| Filas e cache | Redis, Laravel Queues |
| Documentos | DomPDF, PhpSpreadsheet, PDF Parser |
| Infraestrutura | Docker, Docker Compose, Nginx |
| Testes e qualidade | Pest, Laravel Pint |

---

## Arquitetura do ambiente Docker

| Serviço | Função |
|---|---|
| `app` | PHP-FPM 8.3 com a aplicação |
| `nginx` | Servidor web (porta 8080) |
| `postgres` | Banco de dados, com healthcheck |
| `redis` | Cache e filas |
| `queue` | Worker processando jobs em segundo plano |
| `node` | Vite em modo de desenvolvimento (porta 5173) |

---

## Como rodar

**Pré-requisitos:** Docker e Docker Compose.

```bash
# 1. Clonar o repositório
git clone https://github.com/DevLucassousa25/SistemDP.git
cd SistemDP

# 2. Criar o arquivo de ambiente
cp .env.example .env
# Ajuste DB_HOST=postgres, DB_USERNAME e DB_PASSWORD no .env

# 3. Subir os containers
docker compose up -d --build

# 4. Instalar dependências e preparar a aplicação
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

Acesse em **http://localhost:8080**.

## Testes

```bash
docker compose exec app php artisan test
```

---

## Autor

**Lucas Sousa** — Desenvolvedor Full Stack
[LinkedIn](https://www.linkedin.com/in/lucas-sousa-a10474212/) · [GitHub](https://github.com/DevLucassousa25)
