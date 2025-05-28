<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas para noticias
Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/all', [NewsController::class, 'all']);
    Route::get('/{id}', [NewsController::class, 'show'])->where('id', '[0-9]+');
    Route::post('/', [NewsController::class, 'store'])->middleware('auth:sanctum');
    Route::post('/{id}', [NewsController::class, 'update'])->middleware('auth:sanctum')->where('id', '[0-9]+');
    Route::delete('/{id}', [NewsController::class, 'destroy'])->middleware('auth:sanctum')->where('id', '[0-9]+');
    Route::post('/{id}/heart', [NewsController::class, 'addHeart']);
    Route::put('/{id}/publish', [NewsController::class, 'publish'])->middleware('auth:sanctum')->where('id', '[0-9]+');
    Route::get('/by-category', [NewsController::class, 'newsByCategory']);
});

// Rutas para autenticación de administradores
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AdminAuthController::class, 'logout']);
});

// Rutas para categorías
Route::get('/categories', [CategoryController::class, 'index']);

// Rutas para etiquetas
Route::get('/tags', [TagController::class, 'index']);

// Rutas para autores
Route::prefix('authors')->group(function () {
    Route::get('/', [AuthorController::class, 'index']);
    Route::post('/', [AuthorController::class, 'store'])->middleware('auth:sanctum');
    Route::post('/{author}', [AuthorController::class, 'update'])->middleware('auth:sanctum'); // Cambiado a parámetro de ruta
    Route::delete('/{author}', [AuthorController::class, 'destroy'])->middleware('auth:sanctum'); // Cambiado a parámetro de ruta
});

// Rutas para videos
Route::prefix('videos')->group(function () {
    Route::get('/', [VideoController::class, 'index']);
    Route::post('/upload', [VideoController::class, 'upload'])->middleware('auth:sanctum');
});

// Rutas protegidas para el panel de administración
Route::middleware('auth:sanctum')->group(function () {
    // Aquí puedes agregar rutas adicionales que requieran autenticación
    // Ejemplo:
    // Route::apiResource('settings', SettingController::class);
});