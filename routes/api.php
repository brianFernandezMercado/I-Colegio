<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaServicioController;
use App\Http\Controllers\SubServicioController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\AgendaUsuarioController;

Route::apiResource('categorias-servicios', CategoriaServicioController::class);
Route::apiResource('sub-servicios', SubServicioController::class);
Route::apiResource('paises', PaisController::class);
Route::apiResource('departamentos', DepartamentoController::class);
Route::apiResource('provincias', ProvinciaController::class);
Route::apiResource('agenda-usuarios', AgendaUsuarioController::class);
