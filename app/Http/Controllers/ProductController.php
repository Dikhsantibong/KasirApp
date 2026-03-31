<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(10);
        $categories = Category::all();
        $totalInventoryValue = Product::sum(\DB::raw('selling_price * stock'));
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->take(4)->get();
        
        return view('products.index', compact('products', 'categories', 'totalInventoryValue', 'lowStockProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'barcode' => 'nullable|string|max:100',
        ]);

        Product::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->name,
            'category_id' => $request->category_id,
            'barcode' => $request->barcode,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'stock' => $request->stock,
            'min_stock' => $request->min_stock,
            'created_at' => now(),
        ]);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'barcode' => 'nullable|string|max:100',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'barcode' => $request->barcode,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'stock' => $request->stock,
            'min_stock' => $request->min_stock,
        ]);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        \App\Models\Category::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => $request->name,
            // 'store_id' is nullable in the DB schema, so we can skip it or handle if needed.
        ]);

        return redirect()->route('produk.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }
}
