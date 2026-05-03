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
    // Bisa diakses Kasir & Owner
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/kasir', [\App\Http\Controllers\KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir', [\App\Http\Controllers\KasirController::class, 'store'])->name('kasir.store');
    
    // Transaksi: Kasir hanya bisa lihat index & cetak
    Route::get('/transaksi', [\App\Http\Controllers\TransactionController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{id}/cetak', [\App\Http\Controllers\TransactionController::class, 'print'])->name('transaksi.cetak');

    // Route untuk Owner & Manager
    Route::middleware(['role:Owner,Manager'])->group(function() {
        Route::get('/produk', [\App\Http\Controllers\ProductController::class, 'index'])->name('produk.index');
        Route::post('/produk', [\App\Http\Controllers\ProductController::class, 'store'])->name('produk.store');
        Route::post('/produk/category', [\App\Http\Controllers\ProductController::class, 'storeCategory'])->name('produk.category.store');
        Route::put('/produk/{id}', [\App\Http\Controllers\ProductController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{id}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('produk.destroy');
        
        Route::get('/pembelian', [\App\Http\Controllers\PurchaseController::class, 'index'])->name('pembelian.index');
        Route::post('/pembelian', [\App\Http\Controllers\PurchaseController::class, 'store'])->name('pembelian.store');
        Route::post('/pembelian/supplier', [\App\Http\Controllers\PurchaseController::class, 'storeSupplier'])->name('pembelian.supplier.store');
        
        Route::get('/stock', [\App\Http\Controllers\StockController::class, 'index'])->name('stock.index');
        
        Route::get('/pelanggan', [\App\Http\Controllers\CustomerController::class, 'index'])->name('pelanggan.index');
        Route::post('/pelanggan', [\App\Http\Controllers\CustomerController::class, 'store'])->name('pelanggan.store');
        
        Route::get('/laporan', [\App\Http\Controllers\ReportController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/download', [\App\Http\Controllers\ReportController::class, 'export'])->name('laporan.download');
        Route::get('/insight', [\App\Http\Controllers\InsightController::class, 'index'])->name('insight.index');
        
        // Bahan Baku (Ingredients)
        Route::get('/bahan-baku', [\App\Http\Controllers\IngredientController::class, 'index'])->name('ingredients.index');
        Route::post('/bahan-baku', [\App\Http\Controllers\IngredientController::class, 'store'])->name('ingredients.store');
        Route::put('/bahan-baku/{id}', [\App\Http\Controllers\IngredientController::class, 'update'])->name('ingredients.update');
        Route::delete('/bahan-baku/{id}', [\App\Http\Controllers\IngredientController::class, 'destroy'])->name('ingredients.destroy');
    });

    // Route khusus Owner (Keuangan & Pengaturan Kritikal)
    Route::middleware(['role:Owner'])->group(function() {
        Route::get('/hutang', [\App\Http\Controllers\DebtController::class, 'index'])->name('hutang.index');
        
        Route::get('/pengeluaran', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('pengeluaran.index');
        Route::post('/pengeluaran', [\App\Http\Controllers\ExpenseController::class, 'store'])->name('pengeluaran.store');
        
        // Pengaturan (Khusus Owner)
        Route::get('/pengaturan', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/pengaturan', [\App\Http\Controllers\SettingController::class, 'store'])->name('settings.store');
    });

    // Barista Queue (Bisa diakses Kasir & Owner, tapi logisnya Kasir/Barista)
    Route::get('/barista', [\App\Http\Controllers\BaristaController::class, 'index'])->name('barista.index');
    Route::post('/barista/update/{id}', [\App\Http\Controllers\BaristaController::class, 'updateStatus'])->name('barista.update');
});


