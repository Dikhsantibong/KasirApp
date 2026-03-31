<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;

class InsightController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Dynamically find a low stock product to advise restocking
        $lowStockProduct = Product::where('stock', '<=', 10)->orderBy('stock', 'asc')->first();
        
        // Count today's transactions to compare vs target
        $todayTransactionsCount = Transaction::whereDate('created_at', $today)->count();
        $targetTransactions = 142; // arbitrary target for AI mock
        $remainingTarget = max(0, $targetTransactions - $todayTransactionsCount);

        // Fetch a dynamic top-selling product for "Optimasi Stok" promo suggestion
        $topProduct = Product::orderBy('stock', 'desc')->first();

        return view('insights.index', compact(
            'lowStockProduct',
            'todayTransactionsCount',
            'targetTransactions',
            'remainingTarget',
            'topProduct'
        ));
    }
}
