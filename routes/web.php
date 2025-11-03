<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Finance App Routes (Protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    // Transactions
    Route::resource('transactions', 'TransactionController');
    Route::get('/transactions/create/{type}', 'TransactionController@create')->name('transactions.create.type');

    // Accounts
    Route::resource('accounts', 'AccountController');
    Route::get('/accounts/{account}/transactions', 'AccountController@transactions')->name('accounts.transactions');

    // Categories
    Route::resource('categories', 'CategoryController');

    // Reports
    Route::get('/reports', 'ReportController@index')->name('reports.index');
    Route::get('/reports/summary', 'ReportController@summary')->name('reports.summary');
    Route::get('/reports/cash-flow', 'ReportController@cashFlow')->name('reports.cash-flow');
    Route::get('/reports/export', 'ReportController@export')->name('reports.export');

    // Installments
    Route::resource('installments', 'InstallmentController');
    Route::get('/installments/{installment}/pay', 'InstallmentController@pay')->name('installments.pay');

    // Paylater
    Route::resource('paylater', 'PaylaterController');
    Route::get('/paylater/{paylater}/details', 'PaylaterController@details')->name('paylater.details');
    Route::get('/paylater/{paylater}/pay', 'PaylaterController@pay')->name('paylater.pay');
    Route::post('/paylater/{paylater}/pay', 'PaylaterController@processPayment')->name('paylater.processPayment');
});
