<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        $totalPenjualan = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $totalPengeluaran = Expense::whereDate('expense_date', $today)->sum('amount');
        
        $keuntunganBersih = $totalPenjualan - $totalPengeluaran;
        
        $recentTransactions = Transaction::orderBy('created_at', 'desc')->take(10)->get();

        // For visual chart and top margin logic we'll provide mock/aggregated variables for simplicity
        
        return view('reports.index', compact(
            'totalPenjualan', 
            'totalPengeluaran', 
            'keuntunganBersih',
            'recentTransactions'
        ));
    }
}
