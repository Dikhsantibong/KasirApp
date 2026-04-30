<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Monthly totals (more useful than daily)
        $totalPenjualan = Transaction::whereMonth('created_at', $currentMonth)
                                     ->whereYear('created_at', $currentYear)
                                     ->sum('total_amount');
        $totalPengeluaran = Expense::whereMonth('created_at', $currentMonth)
                                   ->whereYear('created_at', $currentYear)
                                   ->sum('amount');
        $keuntunganBersih = $totalPenjualan - $totalPengeluaran;

        $totalTransaksi = Transaction::whereMonth('created_at', $currentMonth)
                                     ->whereYear('created_at', $currentYear)
                                     ->count();

        // Top margin products
        $topMarginProducts = Product::whereNotNull('cost_price')
            ->where('cost_price', '>', 0)
            ->select('name', 'cost_price', 'selling_price',
                DB::raw('(selling_price - cost_price) as margin'),
                DB::raw('ROUND(((selling_price - cost_price) / cost_price) * 100, 1) as margin_pct'))
            ->orderByDesc('margin')
            ->take(3)
            ->get();

        // Daily sales for chart (last 7 days)
        $dailySales = [];
        $dailyExpenses = [];
        $dayLabels = ['SEN','SEL','RAB','KAM','JUM','SAB','MIN'];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailySales[] = Transaction::whereDate('created_at', $date)->sum('total_amount');
            $dailyExpenses[] = Expense::whereDate('created_at', $date)->sum('amount');
        }
        $maxChart = max(max($dailySales ?: [1]), max($dailyExpenses ?: [1]), 1);

        $recentTransactions = Transaction::with('user')->orderBy('created_at', 'desc')->take(10)->get();

        return view('reports.index', compact(
            'totalPenjualan', 'totalPengeluaran', 'keuntunganBersih',
            'totalTransaksi', 'topMarginProducts',
            'dailySales', 'dailyExpenses', 'maxChart', 'dayLabels',
            'recentTransactions'
        ));
    }

    public function export()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $totalPenjualan = Transaction::whereMonth('created_at', $currentMonth)
                                     ->whereYear('created_at', $currentYear)
                                     ->sum('total_amount');
        $totalPengeluaran = Expense::whereMonth('created_at', $currentMonth)
                                   ->whereYear('created_at', $currentYear)
                                   ->sum('amount');
        $keuntunganBersih = $totalPenjualan - $totalPengeluaran;
        $totalTransaksi = Transaction::whereMonth('created_at', $currentMonth)
                                     ->whereYear('created_at', $currentYear)
                                     ->count();

        $topMarginProducts = Product::whereNotNull('cost_price')
            ->where('cost_price', '>', 0)
            ->select('name', 'cost_price', 'selling_price',
                DB::raw('(selling_price - cost_price) as margin'),
                DB::raw('ROUND(((selling_price - cost_price) / cost_price) * 100, 1) as margin_pct'))
            ->orderByDesc('margin')
            ->take(5)
            ->get();

        $recentTransactions = Transaction::with('user')->orderBy('created_at', 'desc')->take(20)->get();
        
        $expenses = Expense::whereMonth('created_at', $currentMonth)
                            ->whereYear('created_at', $currentYear)
                            ->get();

        return view('reports.export', compact(
            'totalPenjualan', 'totalPengeluaran', 'keuntunganBersih',
            'totalTransaksi', 'topMarginProducts', 'recentTransactions', 'expenses'
        ));
    }
}
