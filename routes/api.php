<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdenProduccionController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\LineaProduccionController;
use App\Http\Controllers\RegistroController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

route::get('/students', function(){
    return 'Student List';

});

// ENDPOINTS PARA GESTIONAR LAS ORDENES DE PRODUCCION
// Leer todas las órdenes
Route::get('/ordenes', [OrdenProduccionController::class, 'index']);

// Leer un orden específica por ID
Route::get('/ordenes/{id}', [OrdenProduccionController::class, 'show']);

// Crear un nueva orden
Route::post('/CrearOrden', [OrdenProduccionController::class, 'store']);

// Actualizar un orden existente
Route::put('/ordenes/{id}', [OrdenProduccionController::class, 'update']);

// Eliminar una orden
Route::delete('/ordenes/{id}', [OrdenProduccionController::class, 'destroy']);

//ENDPOINTS DE LOS MODELOS DE LOS PRODUCTOS

Route::get('/modelos', [ModeloController::class, 'index']);

// Leer un modelo específica por ID
Route::get('/modelos/{id}', [ModeloController::class, 'show']);

// Crear un nueva modelo
Route::post('/CrearModelo', [ModeloController::class, 'store']);

// Actualizar un modelo existente
Route::put('/modelos/{id}', [ModeloController::class, 'update']);

// Eliminar un modelo
Route::delete('/modelos/{id}', [ModeloController::class, 'destroy']);

//ENDPOINTS PARA LAS LINEAS DE PRODUCCION 

Route::get('/lineas', [LineaProduccionController::class, 'index']);

// Leer una Linea específica por ID
Route::get('/lineas/{id}', [LineaProduccionController::class, 'show']);

// Crear una nueva Linea
Route::post('/CrearLinea', [LineaProduccionController::class, 'store']);

// Actualizar una Linea existente
Route::put('/lineas/{id}', [LineaProduccionController::class, 'update']);

// Eliminar una Linea
Route::delete('/lineas/{id}', [LineaProduccionController::class, 'destroy']);

//ENDPOINTS PARA LA RUTA DE LOS REGISTROS DE PRODUCCION

Route::get('/registros', [RegistroController::class, 'index']);
Route::get('/registros/{idorden}', [RegistroController::class, 'RegistroOrden']);
Route::get('/registros/acumulado/{idorden}', [RegistroController::class, 'TiempoAcumuladoConPiezasRealizadas']); //Api para hacer el sacar el calculo acumulado de las piezas realizadas.


Route::post('/registros/iniciar', [RegistroController::class, 'iniciarProduccion']);
Route::post('/registros/finalizar', [RegistroController::class, 'finalizarProduccion']);
Route::get('/registros/productividad/{idorden}', [RegistroController::class, 'calcularProductividad']);
Route::delete('/registros/{id}', [RegistroController::class, 'destroy']);
Route::post('/actualizar-piezas', [RegistroController::class, 'actualizarPiezas']);



//Endpoints para el registro de tiempo de las pausas en la produccion
Route::post('/pausa/iniciar', [PausaProduccionController::class, 'iniciarPausa']);
Route::post('/pausa/finalizar/{idorden}', [PausaProduccionController::class, 'finalizarPausa']);


//Endpoits de prueba 


Route::post('/iniciar-tiempo', [RegistroController::class, 'iniciarTiempo']);
Route::post('/registrar-pieza', [RegistroController::class, 'registrarPieza']);
Route::post('/detener-tiempo', [RegistroController::class, 'detenerTiempo']);


Route::get('/tiempo-ensamble/{ordenId}', [RegistroController::class, 'obtenerTiempoEnsamble']);






