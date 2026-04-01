<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'items.product'])->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhere('payment_method', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('method') && $request->method != '') {
            $query->where('payment_method', $request->method);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        $transactions = $query->paginate(10);
        
        $todayTotal = Transaction::whereDate('created_at', Carbon::today())->sum('total_amount');
        $monthlyTotal = Transaction::whereMonth('created_at', Carbon::now()->month)
                                   ->whereYear('created_at', Carbon::now()->year)
                                   ->sum('total_amount');
        $todayCount = Transaction::whereDate('created_at', Carbon::today())->count();
        
        return view('transactions.index', compact('transactions', 'todayTotal', 'monthlyTotal', 'todayCount'));
    }

    public function print($id)
    {
        $transaction = Transaction::with(['user', 'items.product', 'customer'])->findOrFail($id);
        
        // Fetch store Info if possible, else use default
        $store = \DB::table('stores')->first(); 
        
        return view('transactions.receipt', compact('transaction', 'store'));
    }
}
