<?php

use App\Http\Controllers\Api\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

// --- Público ---
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{project:slug}', [ProjectController::class, 'show']);

Route::post('/login', [AuthController::class, 'login']);

// --- Autenticado (admin) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // CRUD de proyectos. Binding por id ({project:id}) porque el slug es editable.
    Route::prefix('admin')->group(function () {
        Route::get('/projects', [AdminProjectController::class, 'index']);
        Route::post('/projects', [AdminProjectController::class, 'store']);
        Route::get('/projects/{project:id}', [AdminProjectController::class, 'show']);
        Route::match(['put', 'patch'], '/projects/{project:id}', [AdminProjectController::class, 'update']);
        Route::delete('/projects/{project:id}', [AdminProjectController::class, 'destroy']);

        // Subida/borrado de imágenes (R2) se agrega en 3c.
    });
});
