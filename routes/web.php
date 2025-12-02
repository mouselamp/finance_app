<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\PaylaterController;
use App\Http\Controllers\ReceiptController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/dashboard');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Finance App Routes (Protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions
    Route::resource('transactions', TransactionController::class);
    Route::get('/transactions/create/{type}', [TransactionController::class, 'create'])->name('transactions.create.type');

    // Accounts
    Route::resource('accounts', AccountController::class);
    Route::get('/accounts/{account}/transactions', [AccountController::class, 'transactions'])->name('accounts.transactions');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/summary', [ReportController::class, 'summary'])->name('reports.summary');
    Route::get('/reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash-flow');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Installments
    Route::resource('installments', InstallmentController::class);
    Route::get('/installments/{installment}/pay', [InstallmentController::class, 'pay'])->name('installments.pay');

    // Paylater
    Route::resource('paylater', PaylaterController::class);
    Route::get('/paylater/{paylater}/details', [PaylaterController::class, 'details'])->name('paylater.details');
    Route::get('/paylater/{paylater}/pay', [PaylaterController::class, 'pay'])->name('paylater.pay');
    Route::post('/paylater/{paylater}/pay', [PaylaterController::class, 'processPayment'])->name('paylater.processPayment');

    // Receipt Analyzer
    Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::post('/receipts/analyze', [ReceiptController::class, 'analyze'])->name('receipts.analyze');
    Route::post('/receipts/{receipt}/save', [ReceiptController::class, 'storeTransaction'])->name('receipts.save');

    // Groups
    Route::get('/groups', [App\Http\Controllers\GroupController::class, 'index'])->name('groups.index');
});

