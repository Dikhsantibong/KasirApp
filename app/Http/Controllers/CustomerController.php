<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\Debt;
use App\Models\Transaction;
use Illuminate\Support\Str;

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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->name,
            'phone' => $request->phone,
            'created_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan!',
                'customer' => $customer
            ]);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan!');
    }
}
