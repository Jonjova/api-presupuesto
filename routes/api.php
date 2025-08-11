<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\EjecucionMensualController;
use App\Http\Controllers\Api\PresupuestoController;

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

// Rutas adicionales para ejecuciones
Route::prefix('ejecuciones')->group(function () {
    Route::get('resumen-anual', [EjecucionMensualController::class, 'resumenAnual'])->name('ejecuciones.resumenAnual');
    Route::get('por-unidad/{unidadId}', [EjecucionMensualController::class, 'porUnidad'])
        ->whereNumber('unidadId')
        ->name('ejecuciones.porUnidad');
    Route::get('por-categoria/{categoriaId}', [EjecucionMensualController::class, 'porCategoria'])
        ->whereNumber('categoriaId')
        ->name('ejecuciones.porCategoria');

        // CRUD estándar (definir DESPUÉS de las rutas personalizadas)
    Route::get('/', [EjecucionMensualController::class, 'index'])->name('ejecuciones.index');
    Route::post('/', [EjecucionMensualController::class, 'store'])->name('ejecuciones.store');
    Route::get('/{id}', [EjecucionMensualController::class, 'show'])
        ->whereNumber('id')
        ->name('ejecuciones.show');
    Route::match(['put', 'patch'], '/{id}', [EjecucionMensualController::class, 'update'])
        ->whereNumber('id')
        ->name('ejecuciones.update');
    Route::delete('/{id}', [EjecucionMensualController::class, 'destroy'])
        ->whereNumber('id')
        ->name('ejecuciones.destroy');
});

Route::prefix('presupuestos')->group(function () {
  
    Route::get('/', [PresupuestoController::class, 'index'])->name('presupuestos.index');
    Route::post('/', [PresupuestoController::class, 'store'])->name('presupuestos.store');
    Route::get('/{id}', [PresupuestoController::class, 'show'])
        ->whereNumber('id')
        ->name('presupuestos.show');
    Route::match(['put', 'patch'], '/{id}', [PresupuestoController::class, 'update'])
        ->whereNumber('id')
        ->name('presupuestos.update');
    Route::delete('/{id}', [PresupuestoController::class, 'destroy'])
        ->whereNumber('id')
        ->name('presupuestos.destroy');
});

Route::apiResource('provisiones', 'App\Http\Controllers\Api\ProvisionController');
Route::apiResource('unidades', 'App\Http\Controllers\Api\UnidadController');
