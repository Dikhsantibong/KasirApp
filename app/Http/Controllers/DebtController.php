<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Debt;
use Carbon\Carbon;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $overdueCount = Debt::where('status', '!=', 'Lunas')
            ->whereDate('due_date', '<', Carbon::now())->count();
            
        $overdueAmount = Debt::where('status', '!=', 'Lunas')
            ->whereDate('due_date', '<', Carbon::now())->sum('amount');
            
        $totalUnpaidAmount = Debt::where('status', '!=', 'Lunas')->sum('amount');

        $query = Debt::with('customer');

        if ($request->has('search') && $request->search != '') {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $allDebts = $query->orderBy('due_date', 'asc')->paginate(15);

        return view('debts.index', compact('allDebts', 'overdueCount', 'overdueAmount', 'totalUnpaidAmount'));
    }
}
