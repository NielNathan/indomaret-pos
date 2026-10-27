<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Transaction extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Proses checkout: cek stok, kurangi stok, simpan transaksi.
     * Dipakai bersama oleh web (kasir) dan REST API.
     *
     * @param array $items [['product_id' => 1, 'qty' => 2], ...]
     */
    public static function checkout(User $user, array $items, int $paid): self
    {
        return DB::transaction(function () use ($user, $items, $paid) {
            $total = 0;
            $rows = [];

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) $item['qty'];

                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup (sisa {$product->stock}).",
                    ]);
                }

                $product->decrement('stock', $qty);
                $subtotal = $product->price * $qty;
                $total += $subtotal;

                $rows[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ];
            }

            if ($paid < $total) {
                throw ValidationException::withMessages([
                    'paid' => 'Uang pembayaran kurang dari total belanja.',
                ]);
            }

            $transaction = self::create([
                'invoice' => 'IDM' . now()->format('ymdHis') . random_int(10, 99),
                'user_id' => $user->id,
                'total' => $total,
                'paid' => $paid,
                'change' => $paid - $total,
            ]);

            $transaction->items()->createMany($rows);

            return $transaction->load('items', 'user');
        });
    }
}
