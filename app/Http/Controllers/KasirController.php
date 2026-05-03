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
        // Fetch all products, we don't strictly filter by stock > 0 because stock depends on ingredients now
        $query = Product::with('category', 'recipes.ingredient');

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
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
            'items.*.customizations' => 'nullable|array',
            'payment_method' => 'required|string',
            'split_payment' => 'nullable|array',
            'customer_id' => 'nullable|string',
            'offline_id' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $items = $request->items;

            // Optional: Payment Gateway Integration Check
            $pgActive = \App\Models\Setting::where('key', 'payment_gateway_active')->value('value') === 'true';
            $transactionStatus = 'completed';
            
            if ($pgActive && $request->payment_method === 'QRIS') {
                // If payment gateway is active, we might set status to pending and return a payment URL
                $transactionStatus = 'pending_payment';
            }

            // Calculate total
            foreach ($items as $item) {
                $totalAmount += $item['price'] * $item['qty'];
            }

            // Create transaction
            $transactionId = $request->offline_id ?: Str::uuid()->toString();
            
            // Check if it's already synced
            $existing = Transaction::find($transactionId);
            if ($existing) {
                DB::rollBack();
                return response()->json(['success' => true, 'message' => 'Already synced']);
            }

            $transaction = Transaction::create([
                'id' => $transactionId,
                'user_id' => auth()->id() ?? \App\Models\User::first()->id, // Fallback for testing
                'customer_id' => $request->customer_id,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'split_payment' => $request->split_payment,
                'status' => $transactionStatus,
                'is_synced' => true,
                'created_at' => now(),
            ]);

            // Create items & deduct stock
            foreach ($items as $item) {
                $subtotal = $item['price'] * $item['qty'];
                $customizations = $item['customizations'] ?? [];

                TransactionItem::create([
                    'id' => Str::uuid()->toString(),
                    'transaction_id' => $transactionId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                    'customizations' => $customizations,
                    'status' => 'pending', // Send to barista
                ]);

                $product = Product::find($item['product_id']);
                if ($product) {
                    if ($product->is_recipe_based) {
                        $size = $customizations['size'] ?? 'Small';
                        $temp = $customizations['temperature'] ?? 'Hot';
                        
                        // Deduct ingredients based on recipe
                        $recipes = \App\Models\ProductRecipe::where('product_id', $product->id)
                            ->where(function($q) use ($size, $temp) {
                                $q->where('size', $size)->orWhereNull('size');
                            })
                            ->where(function($q) use ($temp) {
                                $q->where('temperature', $temp)->orWhereNull('temperature');
                            })->get();
                            
                        foreach ($recipes as $recipe) {
                            $totalQtyNeeded = $recipe->quantity * $item['qty'];
                            \App\Models\Ingredient::where('id', $recipe->ingredient_id)->decrement('stock', $totalQtyNeeded);
                        }
                    } else {
                        // Regular stock product (e.g. Snack)
                        Product::where('id', $item['product_id'])->decrement('stock', $item['qty']);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses!',
                'transaction_id' => $transactionId,
                'total' => $totalAmount,
                'payment_status' => $transactionStatus,
                // In real scenario, return QR URL here if $transactionStatus === 'pending_payment'
                'payment_url' => $transactionStatus === 'pending_payment' ? 'https://mock-pg.com/pay/'.$transactionId : null
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
