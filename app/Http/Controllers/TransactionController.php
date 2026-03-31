<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user')->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $query->where('id', 'like', '%' . $request->search . '%')
                  ->orWhere('payment_method', 'like', '%' . $request->search . '%')
                  ->orWhere('status', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->paginate(10);
        
        // Calculate daily stats
        $todayTotal = Transaction::whereDate('created_at', \Carbon\Carbon::today())->sum('total_amount');
        $monthlyTotal = Transaction::whereMonth('created_at', \Carbon\Carbon::now()->month)->sum('total_amount');
        
        return view('transactions.index', compact('transactions', 'todayTotal', 'monthlyTotal'));
    }
}
