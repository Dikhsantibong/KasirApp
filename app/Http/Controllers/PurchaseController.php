<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::withCount('purchases')->get();

        $query = Purchase::with('supplier')->orderBy('created_at', 'desc');

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
        
        return view('purchases.index', compact('purchases', 'suppliers', 'totalPurchases'));
    }
}
