<?php

use App\Livewire\Pages\Dashboard\Index;
use App\Livewire\Pages\Meetings\Index as MeetingsIndex;
use App\Livewire\Pages\Meetings\Details as MeetingsDetails;
use App\Http\Controllers\Surveys\ExportController as SurveysExport;
use App\Http\Controllers\Feedback\ExportController as FeedbackExport;
use App\Http\Controllers\Evaluation\ExportController as EvaluationExport;
use App\Http\Controllers\Humor\ExportController as HumorExport;
use App\Livewire\Pages\Feedback\Index as FeedbackIndex;
use App\Livewire\Pages\Surveys\Compare as SurveysCompare;
use App\Livewire\Pages\Surveys\Index as SurveysIndex;
use App\Livewire\Pages\Surveys\ManagerRanking as SurveysManagerRanking;
use App\Livewire\Pages\Surveys\Respond as SurveysRespond;
use App\Livewire\Pages\Surveys\Results as SurveysResults;
use App\Livewire\Pages\Team\Index as TeamIndex;
use App\Livewire\Pages\Users\Details;
use App\Livewire\Pages\Users\Index as UsersIndex;
use App\Livewire\Pages\Room\Index as RoomIndex;
use App\Livewire\Pages\Room\Details as RoomShow;
use App\Livewire\Pages\Ouvidoria\Details as OuvidoriaShow;
use App\Livewire\Pages\ManagerEvaluation\Index as ManagerEvaluationIndex;
use App\Livewire\Pages\Tasks\Index as TasksIndex;
use App\Livewire\Pages\Dpi\Index as DpiIndex;
use App\Livewire\Pages\Publicacoes\Index as PublicacoesIndex;
use App\Livewire\Pages\Feed\Index as FeedIndex;
use App\Livewire\Pages\Feed\Profile as FeedProfile;
use App\Livewire\Pages\Communities\Index as CommunitiesIndex;
use App\Livewire\Pages\Communities\Show as CommunityShow;
use App\Livewire\Pages\Calendario\Index as CalendarioIndex;
use App\Livewire\Pages\Rh\Dashboard as RhDashboard;
use App\Livewire\Pages\Rh\Curriculos as RhCurriculos;
use App\Livewire\Pages\Rh\Vagas as RhVagas;
use App\Livewire\Pages\Rh\Onboarding as RhOnboarding;
use App\Livewire\Pages\Rh\Pipeline as RhPipeline;
use App\Livewire\Pages\Rh\Testes as RhTestes;
use App\Livewire\Pages\Okrs\Index as OkrsIndex;
use App\Livewire\Pages\Humor\HistoricoPessoal as HumorHistoricoPessoal;
use App\Livewire\Pages\Candidatura\Apply as CandidaturaApply;
use App\Livewire\Pages\Candidato\Teste as CandidatoTeste;
use App\Livewire\Pages\Funcionario\EntrevistaDesligamento;
use App\Livewire\Pages\Funcionario\Solicitacoes as FuncionarioSolicitacoes;
use App\Livewire\Pages\Organograma\Index as OrganogramaIndex;
use App\Livewire\Pages\Equipamentos\Index as EquipamentosIndex;
use App\Livewire\Pages\Treinamentos\Catalogo as TreinamentosCatalogo;
use App\Livewire\Pages\Treinamentos\Curso as TreinamentosCurso;
use App\Livewire\Pages\Treinamentos\Gestao as TreinamentosGestao;
use App\Livewire\Pages\Treinamentos\Trilhas as TreinamentosTrilhas;
use App\Livewire\Pages\Treinamentos\Relatorios as TreinamentosRelatorios;
use App\Http\Controllers\Treinamentos\CertificadoController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Pages\Login\Index::class)->name('login')->middleware('guest');
});

// ── Páginas públicas (sem login) ─────────────────────────────────────────────
Route::get('/candidatura', CandidaturaApply::class)->name('candidatura.apply');
Route::get('/candidato/teste/{token}', CandidatoTeste::class)->name('candidato.teste');
Route::get('/funcionario/entrevista-desligamento/{token}', EntrevistaDesligamento::class)->name('funcionario.entrevista-desligamento');


Route::middleware('auth')->group(function () {
    Route::get('/', Index::class)->name('dashboard');
    Route::get('/perfil', \App\Livewire\Pages\Profile\Index::class)->name('profile');
    Route::get('/rooms', RoomIndex::class)->name('rooms');
    Route::get('/rooms/{id}', RoomShow::class)->name('rooms.details');
    Route::get('/ouvidoria', \App\Livewire\Pages\Ouvidoria\Index::class)->name('ouvidoria');
    Route::get('/ouvidoria/{id}', OuvidoriaShow::class)->name('ouvidoria.details');
    Route::get('/calendario', CalendarioIndex::class)->name('calendario');
    Route::get('/reunioes', MeetingsIndex::class)->name('reunioes');
    Route::get('/reunioes/{id}', MeetingsDetails::class)->name('reunioes.details');
    Route::get('/time', TeamIndex::class)->name('time');
    Route::get('/organograma', OrganogramaIndex::class)->name('organograma');
    Route::get('/pesquisas', SurveysIndex::class)->name('pesquisas');
    Route::get('/pesquisas/{id}/resultados',        SurveysResults::class)->name('pesquisas.resultados');
    Route::get('/pesquisas/{id}/resultados/pdf',   [SurveysExport::class, 'pdf'])->name('pesquisas.export.pdf');
    Route::get('/pesquisas/{id}/resultados/excel', [SurveysExport::class, 'excel'])->name('pesquisas.export.excel');
    Route::get('/pesquisas/comparativo', SurveysCompare::class)->name('pesquisas.comparativo');
    Route::get('/pesquisas/ranking-gerentes', SurveysManagerRanking::class)->name('pesquisas.ranking-gerentes');
    Route::get('/pesquisas/{id}', SurveysRespond::class)->name('pesquisas.responder');

    Route::get('/feed', FeedIndex::class)->name('feed');
    Route::get('/feed/perfil/{userId}', FeedProfile::class)->name('feed.profile');
    Route::get('/comunidades', CommunitiesIndex::class)->name('communities');
    Route::get('/comunidades/{slug}', CommunityShow::class)->name('communities.show');
    Route::get('/publicacoes', PublicacoesIndex::class)->name('publicacoes')->middleware('rh_or_admin');
    Route::get('/feedback', FeedbackIndex::class)->name('feedback');
    Route::get('/solicitacoes', FuncionarioSolicitacoes::class)->name('solicitacoes');
    Route::get('/tarefas', TasksIndex::class)->name('tarefas');
    Route::get('/dpi', DpiIndex::class)->name('dpi');
    Route::get('/okrs', OkrsIndex::class)->name('okrs');
    Route::get('/meu-humor', HumorHistoricoPessoal::class)->name('humor.pessoal');

    // ── Avaliação de Desempenho ──────────────────────────────────────────
    Route::get('/avaliacoes', ManagerEvaluationIndex::class)->name('avaliacoes');

    // Exportações de Avaliação de Desempenho (apenas RH/Admin)
    Route::get('/avaliacoes/{cycle}/exportar/pdf',   [EvaluationExport::class, 'pdf'])->name('avaliacoes.export.pdf');
    Route::get('/avaliacoes/{cycle}/exportar/excel', [EvaluationExport::class, 'excel'])->name('avaliacoes.export.excel');
    Route::get('/feedback/exportar/pdf',   [FeedbackExport::class, 'pdf'])->name('feedback.export.pdf');
    Route::get('/feedback/exportar/excel', [FeedbackExport::class, 'excel'])->name('feedback.export.excel');

    // ── Humor das Equipes (RH/Admin) ───────────────────────────────
    Route::middleware('rh_or_admin')->group(function () {
        Route::get('/rh/humor-equipes', fn() => redirect()->route('rh.curriculos', ['aba' => 'humor-equipes']))->name('rh.humor-equipes');
        Route::get('/rh/humor-equipes/exportar/pdf/hoje',   [HumorExport::class, 'pdfHoje'])->name('rh.humor-equipes.export.pdf.hoje');
        Route::get('/rh/humor-equipes/exportar/pdf/mensal', [HumorExport::class, 'pdfMensal'])->name('rh.humor-equipes.export.pdf.mensal');
        Route::get('/rh/humor-equipes/exportar/excel',      [HumorExport::class, 'excelMensal'])->name('rh.humor-equipes.export.excel');
    });

    // ── Recrutamento & Selecao (apenas RH/Admin) -------------------
    Route::middleware('rh_or_admin')->prefix('rh')->group(function () {
        Route::get('/curriculos', \App\Livewire\Pages\Rh\Portal::class)->name('rh.curriculos');
        Route::get('/onboarding', RhOnboarding::class)->name('rh.onboarding');
    });

    // -- Restrito a Administrador e RH/DP ---------------------------
    Route::middleware('rh_or_admin')->group(function () {
        Route::get('/users', \App\Livewire\Pages\Users\Index::class)->name('users');
        Route::get('/users/{id}', \App\Livewire\Pages\Users\Details::class)->name('users.details');
    });

    // ── Treinamentos ────────────────────────────────────────────────
    Route::get('/treinamentos', TreinamentosCatalogo::class)->name('treinamentos');
    Route::get('/treinamentos/trilhas', TreinamentosTrilhas::class)->name('treinamentos.trilhas');
    Route::get('/treinamentos/gestao', TreinamentosGestao::class)->name('treinamentos.gestao')->middleware('rh_or_admin');
    Route::get('/treinamentos/relatorios', TreinamentosRelatorios::class)->name('treinamentos.relatorios')->middleware('rh_or_admin');
    Route::get('/treinamentos/certificado/{inscricaoId}', [CertificadoController::class, 'show'])->name('treinamentos.certificado');
    Route::get('/treinamentos/{id}', TreinamentosCurso::class)->name('treinamentos.curso');

    // -- Controle de Equipamentos (RH/DP ou Departamento de TI) -----
    // A autorização granular é feita dentro do componente Livewire
    Route::get('/equipamentos', EquipamentosIndex::class)->name('equipamentos');

    // Termo de Responsabilidade (impressão/PDF)
    Route::get('/equipamentos/termo/{atribuicao}', [App\Http\Controllers\EquipamentoTermoController::class, 'show'])
        ->name('equipamentos.termo');
});
