<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        return view('pos.index', [
            'products' => Product::where('stock', '>', 0)->orderBy('name')
                ->get(['id', 'category_id', 'sku', 'name', 'price', 'stock']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'paid' => ['required', 'integer', 'min:0'],
        ]);

        $trx = Transaction::checkout($request->user(), $data['items'], $data['paid']);

        return response()->json(['redirect' => route('transactions.show', $trx)]);
    }
}
