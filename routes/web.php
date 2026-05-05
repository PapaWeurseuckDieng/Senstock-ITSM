<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\ParcController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// ─── Authentification ────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Application (authentification requise) ──────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Tableau de bord
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ─── Tickets ─────────────────────────────────────────────────────────────
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/',          [TicketController::class, 'index'])->name('index');
        Route::get('/creer',     [TicketController::class, 'create'])->name('create');
        Route::post('/',         [TicketController::class, 'store'])->name('store');
        Route::get('/{ticket}',  [TicketController::class, 'show'])->name('show');

        Route::patch('/{ticket}/assigner',  [TicketController::class, 'assign'])->name('assign');
        Route::post('/{ticket}/statut',     [TicketController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{ticket}/resoudre',   [TicketController::class, 'resolve'])->name('resolve');
        Route::post('/{ticket}/valider',    [TicketController::class, 'validateResolution'])->name('validate');
        Route::post('/{ticket}/commenter',  [TicketController::class, 'comment'])->name('comment');
    });

    // ─── Parc Informatique ───────────────────────────────────────────────────
    Route::prefix('parc')->name('parc.')->group(function () {
        Route::get('/',              [ParcController::class, 'index'])->name('index');
        Route::get('/ajouter',       [ParcController::class, 'create'])->name('create');
        Route::post('/',             [ParcController::class, 'store'])->name('store');
        Route::get('/{equipement}',  [ParcController::class, 'show'])->name('show');
        Route::get('/{equipement}/modifier', [ParcController::class, 'edit'])->name('edit');
        Route::put('/{equipement}',  [ParcController::class, 'update'])->name('update');
        Route::get('/export/csv',        [ParcController::class, 'exportCsv'])->name('export');
        Route::get('/export/pdf',        [ParcController::class, 'exportPdf'])->name('export.pdf');
        Route::delete('/{equipement}', [ParcController::class, 'destroy'])->name('destroy');
    });


    // ─── Assistant IA ────────────────────────────────────────────────────────
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/assistant',            [AiController::class, 'assistant'])->name('assistant');
        Route::get('/base-connaissances',   [AiController::class, 'baseConnaissances'])->name('base-connaissances');
        Route::get('/faq',                  [AiController::class, 'faq'])->name('faq');
        // Endpoints API
        Route::post('/analyser-ticket',     [AiController::class, 'analyserTicket'])->name('analyser-ticket');
        Route::post('/suggerer-reponse',    [AiController::class, 'suggererReponse'])->name('suggerer-reponse');
        Route::post('/analyser-performances',[AiController::class, 'analyserPerformances'])->name('analyser-performances');
        Route::post('/rechercher-solution', [AiController::class, 'rechercherSolution'])->name('rechercher-solution');
        Route::post('/faq-repondre',        [AiController::class, 'faqRepondre'])->name('faq.repondre');
        Route::post('/chat',                [AiController::class, 'chat'])->name('chat');
    });

    // ─── Rapports ────────────────────────────────────────────────────────────
    Route::prefix('rapports')->name('reports.')->group(function () {
        Route::get('/',       [ReportController::class, 'index'])->name('index');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
        Route::get('/csv',    [ReportController::class, 'exportCsv'])->name('csv');
        Route::get('/pdf',    [ReportController::class, 'exportPdf'])->name('pdf');
    });

    // ─── Administration ──────────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/utilisateurs',                  [AdminController::class, 'users'])->name('users.index');
        Route::get('/utilisateurs/creer',            [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/utilisateurs',                 [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/utilisateurs/{user}/modifier',  [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/utilisateurs/{user}',           [AdminController::class, 'updateUser'])->name('users.update');
        Route::patch('/utilisateurs/{user}/statut',  [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
        Route::get('/audit',                         [AdminController::class, 'auditLogs'])->name('audit');
    });
});
