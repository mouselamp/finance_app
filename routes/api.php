<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Api routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your Api!
|
*/

Route::middleware('auth.token')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name('api.auth.login');
    Route::post('logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('me', [App\Http\Controllers\Api\AuthController::class, 'me'])->name('api.auth.me');
});

// Protected Api Routes (require authentication)
Route::middleware('auth.token')->group(function () {
    // Api Routes for Transactions
    Route::prefix('transactions')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ApiTransactionController::class, 'index'])->name('api.transactions.index');
        Route::post('/', [App\Http\Controllers\Api\ApiTransactionController::class, 'store'])->name('api.transactions.store');
        Route::get('/statistics', [App\Http\Controllers\Api\ApiTransactionController::class, 'statistics'])->name('api.transactions.statistics');
        Route::get('/{id}', [App\Http\Controllers\Api\ApiTransactionController::class, 'show'])->name('api.transactions.show');
        Route::put('/{id}', [App\Http\Controllers\Api\ApiTransactionController::class, 'update'])->name('api.transactions.update');
        Route::delete('/{id}', [App\Http\Controllers\Api\ApiTransactionController::class, 'destroy'])->name('api.transactions.destroy');
    });

    // Api Routes for Accounts
    Route::prefix('accounts')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ApiAccountController::class, 'index'])->name('api.accounts.index');
        Route::post('/', [App\Http\Controllers\Api\ApiAccountController::class, 'store'])->name('api.accounts.store');
        Route::get('/{id}', [App\Http\Controllers\Api\ApiAccountController::class, 'show'])->name('api.accounts.show');
        Route::put('/{id}', [App\Http\Controllers\Api\ApiAccountController::class, 'update'])->name('api.accounts.update');
        Route::delete('/{id}', [App\Http\Controllers\Api\ApiAccountController::class, 'destroy'])->name('api.accounts.destroy');
    });

    // Api Routes for Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ApiCategoryController::class, 'index'])->name('api.categories.index');
        Route::post('/', [App\Http\Controllers\Api\ApiCategoryController::class, 'store'])->name('api.categories.store');
        Route::get('/{id}', [App\Http\Controllers\Api\ApiCategoryController::class, 'show'])->name('api.categories.show');
        Route::put('/{id}', [App\Http\Controllers\Api\ApiCategoryController::class, 'update'])->name('api.categories.update');
        Route::delete('/{id}', [App\Http\Controllers\Api\ApiCategoryController::class, 'destroy'])->name('api.categories.destroy');
    });
});