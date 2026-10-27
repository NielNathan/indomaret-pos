<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with('user')->withCount('items')
            ->when($request->date, fn ($q, $d) => $q->whereDate('created_at', $d))
            ->latest()->paginate(12)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('items', 'user');
        return view('transactions.show', compact('transaction'));
    }
}
