<?php

use App\Livewire\Pages\Dashboard\Index;
use App\Livewire\Pages\Meetings\Index as MeetingsIndex;
use App\Livewire\Pages\Meetings\Details as MeetingsDetails;
use App\Http\Controllers\Surveys\ExportController as SurveysExport;
use App\Http\Controllers\Feedback\ExportController as FeedbackExport;
use App\Livewire\Pages\Feedback\Index as FeedbackIndex;
use App\Livewire\Pages\Surveys\Compare as SurveysCompare;
use App\Livewire\Pages\Surveys\Index as SurveysIndex;
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
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Pages\Login\Index::class)->name('login')->middleware('guest');
});


Route::middleware('auth')->group(function () {
    Route::get('/', Index::class)->name('dashboard');
    Route::get('/rooms', RoomIndex::class)->name('rooms');
    Route::get('/rooms/{id}', RoomShow::class)->name('rooms.details');
    Route::get('/ouvidoria', \App\Livewire\Pages\Ouvidoria\Index::class)->name('ouvidoria');
    Route::get('/ouvidoria/{id}', OuvidoriaShow::class)->name('ouvidoria.details');
    Route::get('/reunioes', MeetingsIndex::class)->name('reunioes');
    Route::get('/reunioes/{id}', MeetingsDetails::class)->name('reunioes.details');
    Route::get('/time', TeamIndex::class)->name('time');
    Route::get('/pesquisas', SurveysIndex::class)->name('pesquisas');
    Route::get('/pesquisas/{id}/resultados',        SurveysResults::class)->name('pesquisas.resultados');
    Route::get('/pesquisas/{id}/resultados/pdf',   [SurveysExport::class, 'pdf'])->name('pesquisas.export.pdf');
    Route::get('/pesquisas/{id}/resultados/excel', [SurveysExport::class, 'excel'])->name('pesquisas.export.excel');
    Route::get('/pesquisas/comparativo', SurveysCompare::class)->name('pesquisas.comparativo');
    Route::get('/pesquisas/{id}', SurveysRespond::class)->name('pesquisas.responder');

    Route::get('/feedback', FeedbackIndex::class)->name('feedback');
    Route::get('/tarefas', TasksIndex::class)->name('tarefas');

    // ── Avaliação de Desempenho (Gerente + RH/Admin) ─────────────────
    Route::middleware('can_evaluate')->group(function () {
        Route::get('/avaliacoes', ManagerEvaluationIndex::class)->name('avaliacoes');
    });
    Route::get('/feedback/exportar/pdf',   [FeedbackExport::class, 'pdf'])->name('feedback.export.pdf');
    Route::get('/feedback/exportar/excel', [FeedbackExport::class, 'excel'])->name('feedback.export.excel');

    // ── Restrito a Administrador e RH/DP ────────────────────────────
    Route::middleware('rh_or_admin')->group(function () {
        Route::get('/users', UsersIndex::class)->name('users');
        Route::get('/users/{id}', Details::class)->name('users.details');
    });
});



