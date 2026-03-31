<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Total pengeluaran bulan ini (use created_at, the actual column)
        $totalMonth = Expense::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        // Pengeluaran "Terbanyak" — since there's no category column, group by name
        $topExpense = Expense::select('name', DB::raw('SUM(amount) as total_amount'))
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('name')
            ->orderByDesc('total_amount')
            ->first();

        // Paginated History
        $query = Expense::orderBy('created_at', 'desc');
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $expenses = $query->paginate(12);

        return view('expenses.index', compact('expenses', 'totalMonth', 'topExpense'));
    }
}
