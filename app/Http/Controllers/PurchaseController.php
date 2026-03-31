<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\Supplier;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::withCount(['purchases as total_transaksi' => function ($query) {
            // Optional: count transactions logic
        }])->get();

        $query = Purchase::with('supplier')->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $query->whereHas('supplier', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhere('id', 'like', '%' . $request->search . '%');
        }

        $purchases = $query->paginate(10);
        
        return view('purchases.index', compact('purchases', 'suppliers'));
    }
}
