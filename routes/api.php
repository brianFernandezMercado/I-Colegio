<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaServicioController;
use App\Http\Controllers\SubServicioController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\AgendaUsuarioController;
use App\Http\Controllers\AuthController;



// Route::apiResource('categorias-servicios', CategoriaServicioController::class);
// Route::apiResource('sub-servicios', SubServicioController::class);
// Route::apiResource('paises', PaisController::class);
// Route::apiResource('departamentos', DepartamentoController::class);
// Route::apiResource('provincias', ProvinciaController::class);
// Route::apiResource('agenda-usuarios', AgendaUsuarioController::class);




Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('update', [AuthController::class, 'update']);
        Route::post('changestatus', [AuthController::class, 'changeStatus']);
    });
});

Route::prefix('category')->group(function () {
        Route::middleware('auth:api')->group(function () {
            Route::get('list', [CategoriaServicioController::class, 'getListCategory']);
            Route::post('register', [CategoriaServicioController::class, 'registerCategory']);
            Route::post('getCategory/{id}', [CategoriaServicioController::class, 'showCategory']);
            Route::post('updateCategory/{id}', [CategoriaServicioController::class, 'updateCategory']);
            Route::post('changeStatus/{id}', [CategoriaServicioController::class, 'changeStatus']);
        });
});
Route::prefix('subcategory')->group(function () {
        Route::middleware('auth:api')->group(function () {
            Route::get('list', [SubServicioController::class, 'getListSubCategory']);
            Route::post('register', [SubServicioController::class, 'registerSubCategory']);
            Route::post('getSubCategory/{id}', [SubServicioController::class, 'showSubCategory']);
            Route::post('updateSubCategory/{id}', [SubServicioController::class, 'updateSubCategory']);
            Route::post('changeStatus/{id}', [SubServicioController::class, 'changeStatus']);
        });
});
Route::prefix('departamento')->group(function () {
        Route::middleware('auth:api')->group(function () {
            Route::get('list', [DepartamentoController::class, 'getListDepartamento']);
            Route::post('register', [DepartamentoController::class, 'registerDepartamento']);
            Route::post('getDepartamento/{id}', [DepartamentoController::class, 'showDepartamento']);
            Route::post('updateDepartamento/{id}', [DepartamentoController::class, 'updateDepartamento']);
            Route::post('changeStatus/{id}', [DepartamentoController::class, 'changeStatus']);
        });
});
Route::prefix('provincia')->group(function () {
        Route::middleware('auth:api')->group(function () {
            Route::get('list', [DepartamentoController::class, 'getListProvincia']);
            Route::post('register', [DepartamentoController::class, 'registerProvincia']);
            Route::post('getProvincia/{id}', [DepartamentoController::class, 'showProvincia']);
            Route::post('updateProvincia/{id}', [DepartamentoController::class, 'updateProvincia']);
            Route::post('changeStatus/{id}', [DepartamentoController::class, 'changeStatus']);
        });
});


