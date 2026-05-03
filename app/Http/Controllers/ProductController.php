<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
            'buy_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->name,
            'category_id' => $request->category_id,
            'sku' => $request->sku,
            'buy_price' => $request->buy_price,
            'selling_price' => $request->selling_price,
            'stock' => $request->stock ?? 0,
            'min_stock' => $request->min_stock ?? 0,
            'image' => $imagePath,
            'is_recipe_based' => $request->has('is_recipe_based'),
            'has_customization' => $request->has('has_customization'),
            'created_at' => now(),
        ]);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|string',
            'buy_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::findOrFail($id);
        
        $data = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'sku' => $request->sku,
            'buy_price' => $request->buy_price,
            'selling_price' => $request->selling_price,
            'stock' => $request->stock ?? 0,
            'min_stock' => $request->min_stock ?? 0,
            'is_recipe_based' => $request->has('is_recipe_based'),
            'has_customization' => $request->has('has_customization'),
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // On soft delete, we typically keep the image so it can be restored.
            // If you want to force delete, you'd handle that separately.
            $product->delete();

            return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus (arsip)!');
        } catch (\Exception $e) {
            return redirect()->route('produk.index')->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
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
