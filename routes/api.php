<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;

// Rutas para Categorías
Route::prefix('categorias')->middleware('api')->group(function () {
    // Rutas GET personalizadas (definirlas PRIMERO)
    Route::get('arbol', [CategoriaController::class, 'tree'])->name('categorias.tree');
    Route::get('principales', [CategoriaController::class, 'index'])->name('categorias.principales');
    Route::get('tipo/{type}', [CategoriaController::class, 'byType'])->name('categorias.byType');
    
    // CRUD estándar (definir DESPUÉS de las rutas personalizadas)
    Route::get('/', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::get('/{id}', [CategoriaController::class, 'show'])
        ->whereNumber('id')
        ->name('categorias.show');
    Route::match(['put', 'patch'], '/{id}', [CategoriaController::class, 'update'])
        ->whereNumber('id')
        ->name('categorias.update');
    Route::delete('/{id}', [CategoriaController::class, 'destroy'])
        ->whereNumber('id')
        ->name('categorias.destroy');
});

// Rutas para otros recursos (mantén las existentes)
Route::apiResource('ejecuciones', 'App\Http\Controllers\Api\EjecucionMensualController');
Route::apiResource('presupuestos', 'App\Http\Controllers\Api\PresupuestoController');
Route::apiResource('provisiones', 'App\Http\Controllers\Api\ProvisionController');
Route::apiResource('unidades', 'App\Http\Controllers\Api\UnidadController');
