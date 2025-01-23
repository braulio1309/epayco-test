<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;

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

Route::post('/registerUser', [UserController::class, 'registerClient'])->name('registerUser');
Route::post('/wallet/load', [WalletController::class, 'loadWallet'])->name('loadWallet');
Route::post('/wallet/pay', [TransactionController::class, 'pay'])->name('loadWallet');
Route::post('/confirm-payment', [TransactionController::class, 'confirmPayment']);

