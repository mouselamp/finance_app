<?php

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

Route::middleware('auth.token')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('login', [App\Http\Controllers\API\AuthController::class, 'login'])->name('api.auth.login');
    Route::post('logout', [App\Http\Controllers\API\AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('me', [App\Http\Controllers\API\AuthController::class, 'me'])->name('api.auth.me');
});

// Protected API Routes (require authentication)
Route::middleware('auth.token')->group(function () {
    // API Routes for Transactions
    Route::prefix('transactions')->group(function () {
        Route::get('/', [App\Http\Controllers\API\ApiTransactionController::class, 'index'])->name('api.transactions.index');
        Route::post('/', [App\Http\Controllers\API\ApiTransactionController::class, 'store'])->name('api.transactions.store');
        Route::get('/statistics', [App\Http\Controllers\API\ApiTransactionController::class, 'statistics'])->name('api.transactions.statistics');
        Route::get('/{id}', [App\Http\Controllers\API\ApiTransactionController::class, 'show'])->name('api.transactions.show');
        Route::put('/{id}', [App\Http\Controllers\API\ApiTransactionController::class, 'update'])->name('api.transactions.update');
        Route::delete('/{id}', [App\Http\Controllers\API\ApiTransactionController::class, 'destroy'])->name('api.transactions.destroy');
    });

    // API Routes for Accounts
    Route::prefix('accounts')->group(function () {
        Route::get('/', [App\Http\Controllers\API\ApiAccountController::class, 'index'])->name('api.accounts.index');
        Route::post('/', [App\Http\Controllers\API\ApiAccountController::class, 'store'])->name('api.accounts.store');
        Route::get('/{id}', [App\Http\Controllers\API\ApiAccountController::class, 'show'])->name('api.accounts.show');
        Route::put('/{id}', [App\Http\Controllers\API\ApiAccountController::class, 'update'])->name('api.accounts.update');
        Route::delete('/{id}', [App\Http\Controllers\API\ApiAccountController::class, 'destroy'])->name('api.accounts.destroy');
    });

    // API Routes for Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [App\Http\Controllers\API\ApiCategoryController::class, 'index'])->name('api.categories.index');
        Route::post('/', [App\Http\Controllers\API\ApiCategoryController::class, 'store'])->name('api.categories.store');
        Route::get('/{id}', [App\Http\Controllers\API\ApiCategoryController::class, 'show'])->name('api.categories.show');
        Route::put('/{id}', [App\Http\Controllers\API\ApiCategoryController::class, 'update'])->name('api.categories.update');
        Route::delete('/{id}', [App\Http\Controllers\API\ApiCategoryController::class, 'destroy'])->name('api.categories.destroy');
    });
});