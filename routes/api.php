<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BemController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\ModeloContratoController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\TipoBemController;
use App\Http\Controllers\TipoGastoController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;
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

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');

// Users
Route::get('/users', [UserController::class, 'index'])->middleware('auth:sanctum');
Route::post('/users', [UserController::class, 'store'])->middleware('auth:sanctum');
Route::post('/users/edit', [UserController::class, 'edit'])->middleware('auth:sanctum');

// Clientes
Route::get('/clientes', [ClienteController::class, 'index'])->middleware('auth:sanctum');
Route::get('/clientes/{id}', [ClienteController::class, 'client'])->middleware('auth:sanctum');
Route::post('/clientes/{id}', [ClienteController::class, 'edit'])->middleware('auth:sanctum');
Route::post('/clientes/address/{id}', [ClienteController::class, 'editAddress'])->middleware('auth:sanctum');
Route::post('/clientes', [ClienteController::class, 'store'])->middleware('auth:sanctum');
Route::post('/clientes/filter', [ClienteController::class, 'filter'])->middleware('auth:sanctum');

// Tipos de Bem
Route::get('/tipo_bems', [TipoBemController::class, 'index'])->middleware('auth:sanctum');
Route::post('/tipo_bems', [TipoBemController::class, 'store'])->middleware('auth:sanctum');
Route::post('/tipo_bems/edit', [TipoBemController::class, 'edit'])->middleware('auth:sanctum');

// Tipos de Gastos
Route::get('/tipo_gastos', [TipoGastoController::class, 'index'])->middleware('auth:sanctum');
Route::post('/tipo_gastos', [TipoGastoController::class, 'store'])->middleware('auth:sanctum');
Route::post('/tipo_gastos/edit', [TipoGastoController::class, 'edit'])->middleware('auth:sanctum');

// Gastos
Route::get('/gastos', [GastoController::class, 'index'])->middleware('auth:sanctum');
Route::post('/gastos', [GastoController::class, 'store'])->middleware('auth:sanctum');

// Bens
Route::get('/bems', [BemController::class, 'index'])->middleware('auth:sanctum');
Route::post('/bems', [BemController::class, 'store'])->middleware('auth:sanctum');

//Contrato
Route::get('/contratos', [ContratoController::class, 'index'])->middleware('auth:sanctum');
Route::post('/contratos', [ContratoController::class, 'store'])->middleware('auth:sanctum');
Route::post('/contratos/upload/{id}', [ContratoController::class, 'uploadAssinaContrato'])->middleware('auth:sanctum');
Route::post('/contratos/filter', [ContratoController::class, 'filter'])->middleware('auth:sanctum');
Route::post('/contratos/ativa/{id}', [ContratoController::class, 'ativa'])->middleware('auth:sanctum');
Route::post('/contratos/finaliza/{id}', [ContratoController::class, 'finaliza'])->middleware('auth:sanctum');
Route::post('/contratos/cancela/{id}', [ContratoController::class, 'cancela'])->middleware('auth:sanctum');
Route::post('/contratos/inadimplente/{id}', [ContratoController::class, 'inadimplente'])->middleware('auth:sanctum');
Route::post('/contratos/prolonga/{id}', [ContratoController::class, 'prolonga'])->middleware('auth:sanctum');
Route::put('/contratos/{id}/vencido', [ContratoController::class, 'marcarComoVencido']);

// Modelo de Contrato
Route::get('/modelo_contratos', [ModeloContratoController::class, 'index'])->middleware('auth:sanctum');
Route::post('/modelo_contratos', [ModeloContratoController::class, 'store'])->middleware('auth:sanctum');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware('auth:sanctum');

// Dashboard
Route::get('/saldo', [SaldoController::class, 'index'])->middleware('auth:sanctum');
Route::post('/saldo', [SaldoController::class, 'edit'])->middleware('auth:sanctum');

// Dashboard
Route::get('/transactions', [TransactionController::class, 'indexGroupedByDate'])->middleware('auth:sanctum');
Route::post('/transactions/filter', [TransactionController::class, 'indexGroupedByDateFilter'])->middleware('auth:sanctum');

// Notificações
Route::middleware('auth:sanctum')->get('/notifications', function (Request $request) {
    return $request->user()->notifications()->latest()->take(10)->get();
});
