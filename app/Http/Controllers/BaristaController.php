<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Models\Transaction;

class BaristaController extends Controller
{
    public function index(Request $request)
    {
        // For API (polling)
        if ($request->wantsJson()) {
            $pendingItems = TransactionItem::with(['product', 'transaction'])
                ->whereIn('status', ['pending', 'on_progress'])
                ->orderBy('id', 'asc') // Use created_at if we add it to items, or just rely on transaction time
                ->get();

            // Group by transaction
            $grouped = [];
            foreach ($pendingItems as $item) {
                $txId = $item->transaction_id;
                if (!isset($grouped[$txId])) {
                    $grouped[$txId] = [
                        'transaction_id' => $txId,
                        'order_time' => $item->transaction->created_at->format('H:i'),
                        'customer' => $item->transaction->customer->name ?? 'Tamu',
                        'items' => []
                    ];
                }
                $grouped[$txId]['items'][] = $item;
            }

            return response()->json(array_values($grouped));
        }

        // For View
        return view('barista.index');
    }

    public function updateStatus(Request $request, $id)
    {
        $item = TransactionItem::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,on_progress,ready'
        ]);

        $item->status = $request->status;
        $item->save();

        return response()->json(['success' => true, 'status' => $item->status]);
    }
}
