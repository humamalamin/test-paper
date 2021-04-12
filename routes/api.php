<?php

use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'jsonResponse'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('verified', [AuthController::class, 'verified'])->name('verified');
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh')->middleware('auth:api');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:api');
    });

    Route::group(['middleware' => 'auth:api', 'prefix' => 'admin', 'as' => 'admin.'], function () {
        // Account
        Route::group(['prefix' => 'accounts', 'as' => 'accounts.'], function() {
            Route::get('/', [AccountController::class, 'index'])->name('index');
            Route::post('/', [AccountController::class, 'store'])->name('store');
            Route::put('{accountID}', [AccountController::class, 'update'])->name('update');
            Route::delete('{accountID}', [AccountController::class, 'delete'])->name('delete');
            Route::get('{accountID}', [AccountController::class, 'show'])->name('show');
        });

        // Transaction
        Route::group(['prefix' => 'transactions', 'as' => 'transactions.'], function () {
            Route::get('/month', [TransactionController::class, 'month'])->name('month');
            Route::get('/daily', [TransactionController::class, 'daily'])->name('daily');
            Route::get('/', [TransactionController::class, 'index'])->name('index');
            Route::post('/', [TransactionController::class, 'store'])->name('store');
            Route::put('{transactionID}', [TransactionController::class, 'update'])->name('update');
            Route::delete('{transactionID}', [TransactionController::class, 'delete'])->name('delete');
            Route::get('{transactionID}', [TransactionController::class, 'show'])->name('show');
        });

        // User
        Route::get('{userID}', [UserController::class, 'show'])->name('show');
    });
});
