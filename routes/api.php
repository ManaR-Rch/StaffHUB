<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PaieController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\TempsTravailController;


Route::middleware('web')->prefix("{$apiPrefix}/{$apiVersion}")->group(function () {
   Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth');
        Route::get('me', [AuthController::class, 'me'])->middleware('auth');
    });

    Route::middleware('auth')->group(function () {
    
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('dashboard/activities', [DashboardController::class, 'recentActivities']);

     
        Route::apiResource('employes', EmployeController::class);

   
        Route::apiResource('conges', CongeController::class);
        Route::post('conges/{conge}/approve', [CongeController::class, 'approve']);
        Route::post('conges/{conge}/reject', [CongeController::class, 'reject']);

   
        Route::apiResource('absences', AbsenceController::class);
        Route::post('absences/{absence}/justify', [AbsenceController::class, 'justify']);

    
        Route::apiResource('documents', DocumentController::class);
        Route::get('documents/{document}/download', [DocumentController::class, 'download']);

   
        Route::apiResource('paies', PaieController::class);
        Route::post('paies/{paie}/validate', [PaieController::class, 'validatePaie']);
        Route::get('paies/{paie}/download', [PaieController::class, 'download']);

  
        Route::apiResource('taches', TacheController::class);
        Route::post('taches/{tache}/complete', [TacheController::class, 'complete']);


        Route::apiResource('emails', EmailController::class)->only(['index', 'store', 'show']);
        Route::get('emails/types', [EmailController::class, 'types']);

  
        Route::apiResource('temps-travail', TempsTravailController::class);
        Route::get('temps-travail/stats', [TempsTravailController::class, 'stats']);
    });
});
