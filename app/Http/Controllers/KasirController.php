<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KasirController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category')->where('stock', '>', 0);

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->get();

        return view('kasir.index', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $items = $request->items;

            // Validate stock availability first
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Produk tidak ditemukan: ' . $item['product_id']
                    ], 404);
                }
                if ($product->stock < $item['qty']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak cukup untuk produk: ' . $product->name . ' (tersisa: ' . $product->stock . ')'
                    ], 422);
                }
            }

            // Calculate total
            foreach ($items as $item) {
                $totalAmount += $item['price'] * $item['qty'];
            }

            // Create transaction
            $transactionId = Str::uuid()->toString();
            $transaction = Transaction::create([
                'id' => $transactionId,
                'user_id' => auth()->id(),
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'Lunas',
                'created_at' => now(),
            ]);

            // Create transaction items & reduce stock
            foreach ($items as $item) {
                $subtotal = $item['price'] * $item['qty'];

                TransactionItem::create([
                    'id' => Str::uuid()->toString(),
                    'transaction_id' => $transactionId,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                // Reduce product stock
                Product::where('id', $item['product_id'])->decrement('stock', $item['qty']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses!',
                'transaction_id' => $transactionId,
                'total' => $totalAmount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
}
