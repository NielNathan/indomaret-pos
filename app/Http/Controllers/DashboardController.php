<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Transaction::whereDate('created_at', today());

        $week = collect(range(6, 0))->map(function ($i) {
            $day = today()->subDays($i);
            return [
                'label' => $day->format('d/m'),
                'total' => (int) Transaction::whereDate('created_at', $day)->sum('total'),
            ];
        });

        return view('dashboard', [
            'salesToday' => (int) (clone $today)->sum('total'),
            'trxToday' => (clone $today)->count(),
            'productCount' => Product::count(),
            'week' => $week,
            'weekMax' => max(1, $week->max('total')),
            'lowStock' => Product::where('stock', '<=', 10)->orderBy('stock')->limit(6)->get(),
            'topProducts' => TransactionItem::select('name', DB::raw('SUM(qty) as qty'))
                ->groupBy('name')->orderByDesc('qty')->limit(5)->get(),
        ]);
    }
}
