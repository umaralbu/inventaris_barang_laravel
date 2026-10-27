<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('locations', LocationController::class);
        Route::resource('staff', StaffController::class)->except('show');
        Route::resource('users', UserController::class);
    });

    Route::get('/items/export', [ItemController::class, 'exportCsv'])->name('items.export');
    Route::get('/items/{item}/history', [ItemController::class, 'history'])->name('items.history');
    Route::resource('items', ItemController::class);

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/export', [TransactionController::class, 'exportCsv'])->name('transactions.export');
    Route::get('/transactions/borrow', [TransactionController::class, 'borrowForm'])->name('transactions.borrow');
    Route::post('/transactions/borrow', [TransactionController::class, 'borrowStore']);
    Route::get('/transactions/return/{item?}', [TransactionController::class, 'returnForm'])->name('transactions.return');
    Route::post('/transactions/return', [TransactionController::class, 'returnStore']);
    Route::get('/transactions/transfer', [TransactionController::class, 'transferForm'])->name('transactions.transfer');
    Route::post('/transactions/transfer', [TransactionController::class, 'transferStore']);
    Route::middleware('role:admin')->group(function () {
        Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
        Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');
    });
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
});
