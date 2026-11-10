<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;

// Auth Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Proteksi Halaman Harus Login
Route::middleware('auth')->group(function () {
    
    // Fitur Khusus Kasir & Admin (POS & Cetak Struk)
    Route::middleware('role:kasir,admin')->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{id}/print', [TransactionController::class, 'print'])->name('transactions.print');
    });

    // Fitur Khusus Admin Only (Master Data & Laporan)
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // Default Redirect
    Route::get('/', function () {
        return redirect()->route('transactions.index');
    });
});