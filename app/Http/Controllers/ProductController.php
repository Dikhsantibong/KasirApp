<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(10);
        $totalInventoryValue = Product::sum(\DB::raw('selling_price * stock'));
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->take(4)->get();
        
        return view('products.index', compact('products', 'totalInventoryValue', 'lowStockProducts'));
    }
}
