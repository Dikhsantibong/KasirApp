<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\Debt;
use App\Models\Transaction;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withSum('transactions as total_belanja', 'total_amount')
                         ->withSum('debts as total_hutang', 'amount');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        // we might sort by highest debt to match the "Hutang & Jatuh Tempo" intent
        $customers = $query->orderByDesc('total_hutang')->paginate(10);
        
        $totalPiutang = Debt::where('status', '!=', 'Lunas')->sum('amount');
        $jatuhTempoCount = Debt::where('due_date', '<', \Carbon\Carbon::now())
                                ->where('status', '!=', 'Lunas')
                                ->count();
        $totalCustomer = Customer::count();

        return view('customers.index', compact('customers', 'totalPiutang', 'jatuhTempoCount', 'totalCustomer'));
    }
}
