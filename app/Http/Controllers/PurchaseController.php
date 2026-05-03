<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::all();
        $products = Product::orderBy('name', 'asc')->get();

        $query = Purchase::with(['supplier', 'items.product'])->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $purchases = $query->paginate(10);
        $totalPurchases = Purchase::sum('total_amount');
        
        return view('purchases.index', compact('purchases', 'suppliers', 'products', 'totalPurchases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['qty'] * $item['cost_price'];
            }

            $purchaseId = Str::uuid()->toString();
            $purchase = Purchase::create([
                'id' => $purchaseId,
                'supplier_id' => $request->supplier_id,
                'total_amount' => $totalAmount,
                'created_at' => now(),
            ]);

            foreach ($request->items as $item) {
                PurchaseItem::create([
                    'id' => Str::uuid()->toString(),
                    'purchase_id' => $purchaseId,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'cost_price' => $item['cost_price'],
                ]);

                // Update product stock and cost price
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->increment('stock', $item['qty']);
                    $product->update(['buy_price' => $item['cost_price']]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Pembelian berhasil dicatat!']);
            }

            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal mencatat pembelian: ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Gagal mencatat pembelian: ' . $e->getMessage()]);
        }
    }

    public function storeSupplier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        Supplier::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Supplier berhasil ditambahkan!', 'suppliers' => Supplier::all()]);
        }

        return redirect()->back()->with('success', 'Supplier berhasil ditambahkan!');
    }
}
