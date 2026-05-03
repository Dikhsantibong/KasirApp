<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        // Sort by stock to easily find items that need restocking
        $products = $query->orderBy('stock', 'asc')->paginate(15);
        
        $totalItems = Product::sum('stock');
        $lowStockItems = Product::whereColumn('stock', '<=', 'min_stock')->count();
        $totalValue = Product::sum(\DB::raw('buy_price * stock'));

        return view('stocks.index', compact('products', 'totalItems', 'lowStockItems', 'totalValue'));
    }
}
