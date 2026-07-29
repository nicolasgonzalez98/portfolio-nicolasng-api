<?php

use App\Http\Controllers\Api\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Público ---
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{project:slug}', [ProjectController::class, 'show']);

// --- Autenticado (admin) — se completa en la Fase 3 ---
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
