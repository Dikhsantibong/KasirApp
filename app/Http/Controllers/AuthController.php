<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Debt;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Percobaan login untuk: ' . $request->email);

            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            // Cek apakah email terdaftar
            $user = \App\Models\User::where('email', $request->email)->first();
            if (!$user) {
                \Illuminate\Support\Facades\Log::warning('Email tidak ditemukan: ' . $request->email);
                return back()->withErrors(['email' => 'E-mail tidak ditemukan.'])->onlyInput('email');
            }

            // Cek Password secara manual
            if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                \Illuminate\Support\Facades\Log::warning('Password salah untuk: ' . $request->email);
                return back()->withErrors(['password' => 'Password salah.'])->onlyInput('email');
            }

            // Lanjut ke Autentikasi Laravel
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                \Illuminate\Support\Facades\Log::info('Login Berhasil! Mengalihkan ke dashboard.');
                return redirect()->intended('/dashboard');
            }

            \Illuminate\Support\Facades\Log::error('Auth::attempt gagal tanpa alasan jelas.');
            return back()->withErrors(['email' => 'Gagal login.'])->onlyInput('email');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error Sistem: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Sistem Error: ' . $e->getMessage()])->onlyInput('email');
        }
    }

    public function dashboard()
    {
        $today = Carbon::today();
        
        // 1. Total Penjualan Hari Ini
        $todaySales = Transaction::whereDate('created_at', $today)->sum('total_amount');
        
        // 2. Keuntungan Hari Ini (Estimasi: Revenue - Cost dari items yang terjual)
        // Jika model TransactionItem belum diekspos, kita bisa pakai estimasi margin 30% atau hitung manual jika relasi ada
        $todayProfit = Transaction::whereDate('created_at', $today)
            ->with(['items.product' => function($query) {
                $query->withTrashed();
            }])
            ->get()
            ->sum(function($transaction) {
                return $transaction->items->sum(function($item) {
                    // Jika produk sudah dihapus (soft delete), kita tetap ambil datanya untuk hitung profit history
                    $product = $item->product;
                    if (!$product) return 0; // Fallback jika benar-benar tidak ada di DB
                    
                    $cost = $product->buy_price ?? ($product->selling_price * 0.7); 
                    return ($item->price - $cost) * $item->quantity;
                });
            });

        // 3. Stok Menipis (Di bawah min_stock)
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();

        // 4. Total Hutang Pelanggan Belum Lunas
        $totalDebt = Debt::where('status', '!=', 'Lunas')->sum('amount');

        // 5. Grafik Penjualan 7 Hari Terakhir
        $chartData = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->isoFormat('ddd');
            $chartData[] = Transaction::whereDate('created_at', $date)->sum('total_amount');
        }

        // 6. Produk Terlaris Hari Ini
        $topProduct = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereDate('transactions.created_at', $today)
            ->select('products.name', DB::raw('SUM(transaction_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->first();

        // 7. Jam Tersibuk
        $busyHour = Transaction::whereDate('created_at', $today)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderByDesc('count')
            ->first();

        // 8. Aktivitas Terakhir
        $recentActivities = Transaction::orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard', compact(
            'todaySales', 'todayProfit', 'lowStockCount', 'totalDebt',
            'chartData', 'chartLabels', 'topProduct', 'busyHour', 'recentActivities'
        ));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
