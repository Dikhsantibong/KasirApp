<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        // Pengeluaran "Terbanyak" — group by category
        $topExpense = Expense::select('category', DB::raw('SUM(amount) as total_amount'))
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->first();

        // Paginated History
        $query = Expense::orderBy('created_at', 'desc');
        if ($request->has('search') && $request->search != '') {
            $query->where('category', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        $expenses = $query->paginate(12);

        return view('expenses.index', compact('expenses', 'totalMonth', 'topExpense'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:150',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $expense = Expense::create([
            'id' => Str::uuid()->toString(),
            'category' => $request->category,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil dicatat!',
                'expense' => $expense
            ]);
        }

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dicatat!');
    }
}
