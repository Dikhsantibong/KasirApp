<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Debt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InsightController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;
        
        // Low stock product
        $lowStockProduct = Product::where('stock', '<=', 10)->orderBy('stock', 'asc')->first();
        
        // Today's transactions
        $todayTransactionsCount = Transaction::whereDate('created_at', $today)->count();
        $todayRevenue = Transaction::whereDate('created_at', $today)->sum('total_amount');
        
        // Monthly comparison
        $thisMonthRevenue = Transaction::whereMonth('created_at', $thisMonth)
                                       ->whereYear('created_at', $thisYear)
                                       ->sum('total_amount');
        $lastMonthRevenue = Transaction::whereMonth('created_at', Carbon::now()->subMonth()->month)
                                       ->whereYear('created_at', Carbon::now()->subMonth()->year)
                                       ->sum('total_amount');
        $revenueChange = $lastMonthRevenue > 0 ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;

        // Top stocked product for promo suggestion
        $topProduct = Product::orderBy('stock', 'desc')->first();

        // Total customers
        $totalCustomers = Customer::count();
        
        // Overdue debts
        $overdueDebts = Debt::where('status', '!=', 'Lunas')
                           ->whereDate('due_date', '<', $today)
                           ->count();

        // Monthly expenses
        $monthlyExpenses = Expense::whereMonth('created_at', $thisMonth)
                                  ->whereYear('created_at', $thisYear)
                                  ->sum('amount');

        // Total products
        $totalProducts = Product::count();

        // Peak hour (most transactions in which hour)
        $peakHour = Transaction::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('hour')
            ->orderByDesc('cnt')
            ->first();

        return view('insights.index', compact(
            'lowStockProduct', 'todayTransactionsCount', 'todayRevenue',
            'thisMonthRevenue', 'revenueChange', 'topProduct',
            'totalCustomers', 'overdueDebts', 'monthlyExpenses',
            'totalProducts', 'peakHour'
        ));
    }
}
