<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/kasir', [\App\Http\Controllers\KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir', [\App\Http\Controllers\KasirController::class, 'store'])->name('kasir.store');
    Route::get('/produk', [\App\Http\Controllers\ProductController::class, 'index'])->name('produk.index');
    Route::post('/produk', [\App\Http\Controllers\ProductController::class, 'store'])->name('produk.store');
    Route::post('/produk/category', [\App\Http\Controllers\ProductController::class, 'storeCategory'])->name('produk.category.store');
    Route::put('/produk/{id}', [\App\Http\Controllers\ProductController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{id}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('produk.destroy');
    Route::get('/transaksi', [\App\Http\Controllers\TransactionController::class, 'index'])->name('transaksi.index');
    Route::get('/pembelian', [\App\Http\Controllers\PurchaseController::class, 'index'])->name('pembelian.index');
    Route::get('/stock', [\App\Http\Controllers\StockController::class, 'index'])->name('stock.index');
    Route::get('/pelanggan', [\App\Http\Controllers\CustomerController::class, 'index'])->name('pelanggan.index');
    Route::get('/hutang', [\App\Http\Controllers\DebtController::class, 'index'])->name('hutang.index');
    Route::get('/pengeluaran', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('pengeluaran.index');
    Route::get('/laporan', [\App\Http\Controllers\ReportController::class, 'index'])->name('laporan.index');
    Route::get('/insight', [\App\Http\Controllers\InsightController::class, 'index'])->name('insight.index');
});


