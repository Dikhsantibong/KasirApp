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

        // Total pengeluaran bulan ini
        $totalMonth = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        // Pengeluaran "Terbanyak" berdasarkan Kategori untuk UI card
        $topExpense = Expense::select('category', DB::raw('SUM(amount) as total_amount'), DB::raw('MAX(description) as sample_desc'))
            ->whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->first();

        // Paginated History
        $query = Expense::orderBy('expense_date', 'desc');
        if ($request->has('search') && $request->search != '') {
            $query->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
        }
        $expenses = $query->paginate(12);

        return view('expenses.index', compact('expenses', 'totalMonth', 'topExpense'));
    }
}
