<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // GET /api/transactions?date=2026-10-19
    public function index(Request $request)
    {
        return Transaction::with('user:id,name')->withCount('items')
            ->when($request->date, fn ($q, $d) => $q->whereDate('created_at', $d))
            ->latest()->paginate(min((int) $request->get('per_page', 15), 100));
    }

    public function show(Transaction $transaction)
    {
        return response()->json(['data' => $transaction->load('items', 'user:id,name')]);
    }

    // POST /api/transactions  {"items":[{"product_id":1,"qty":2}],"paid":50000}
    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'paid' => ['required', 'integer', 'min:0'],
        ]);

        $trx = Transaction::checkout($request->user(), $data['items'], $data['paid']);

        return response()->json(['data' => $trx], 201);
    }
}
