<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Pengguna ----
        User::create(['name' => 'Budi Santoso', 'email' => 'admin@indomaret.test', 'role' => 'admin', 'password' => 'password']);
        $kasir1 = User::create(['name' => 'Siti Rahayu', 'email' => 'kasir@indomaret.test', 'role' => 'kasir', 'password' => 'password']);
        $kasir2 = User::create(['name' => 'Agus Prasetyo', 'email' => 'kasir2@indomaret.test', 'role' => 'kasir', 'password' => 'password']);

        // ---- Kategori & produk ----
        $data = [
            'Minuman' => [
                ['8991001', 'Aqua Botol 600ml', 4000, 120],
                ['8991002', 'Teh Botol Sosro 450ml', 5500, 80],
                ['8991003', 'Le Minerale 600ml', 3500, 100],
                ['8991004', 'Pocari Sweat 500ml', 8500, 40],
                ['8991005', 'Floridina Orange 360ml', 3500, 3],
                ['8991006', 'Kopi Kapal Api Sachet', 1500, 150],
            ],
            'Makanan Ringan' => [
                ['8992001', 'Chitato Sapi Panggang 68g', 11500, 45],
                ['8992002', 'Taro Net Seaweed 36g', 6500, 60],
                ['8992003', 'Oreo Vanilla 133g', 9500, 50],
                ['8992004', 'Silverqueen Chunky Bar 58g', 17500, 25],
                ['8992005', 'Pocky Chocolate 40g', 8000, 5],
                ['8992006', 'Beng-Beng Wafer 20g', 2500, 200],
            ],
            'Mie & Makanan Instan' => [
                ['8993001', 'Indomie Goreng Original', 3500, 300],
                ['8993002', 'Indomie Soto Mie', 3300, 200],
                ['8993003', 'Mie Sedaap Goreng', 3400, 180],
                ['8993004', 'Pop Mie Ayam 75g', 6500, 70],
                ['8993005', 'Bubur Sun Ayam 40g', 4500, 40],
            ],
            'Sembako' => [
                ['8994001', 'Beras Pandan Wangi 5kg', 78000, 20],
                ['8994002', 'Minyak Goreng Bimoli 1L', 19500, 35],
                ['8994003', 'Gula Pasir Gulaku 1kg', 18000, 30],
                ['8994004', 'Telur Ayam 1/2 kg', 17500, 8],
                ['8994005', 'Kecap Manis ABC 275ml', 12500, 45],
            ],
            'Susu & Bayi' => [
                ['8995001', 'Susu Ultra Milk Coklat 250ml', 6500, 90],
                ['8995002', 'Susu Bendera Kental Manis 370g', 13500, 40],
                ['8995003', 'Pampers Active Baby M 8s', 24000, 18],
            ],
            'Kebersihan' => [
                ['8996001', 'Rinso Molto Sachet 42g', 1500, 100],
                ['8996002', 'Lifebuoy Sabun Mandi 75g', 4000, 70],
                ['8996003', 'Pepsodent Pasta Gigi 120g', 12500, 55],
                ['8996004', 'Sunsilk Shampoo 170ml', 23500, 30],
            ],
            'Roti & Kue' => [
                ['8997001', 'Roti Tawar Sari Roti Jumbo', 17500, 25],
                ['8997002', 'Roti Sobek Coklat', 14500, 20],
                ['8997003', 'Sari Roti Isi Coklat', 3500, 60],
            ],
        ];

        foreach ($data as $categoryName => $products) {
            $category = Category::create(['name' => $categoryName]);
            foreach ($products as [$sku, $name, $price, $stock]) {
                Product::create([
                    'category_id' => $category->id,
                    'sku' => $sku,
                    'name' => $name,
                    'price' => $price,
                    'stock' => $stock,
                ]);
            }
        }

        // ---- Riwayat transaksi 7 hari terakhir ----
        $products = Product::all();
        $cashiers = [$kasir1, $kasir2];

        for ($i = 0; $i < 60; $i++) {
            $date = now()->subDays(rand(0, 6))->setTime(rand(7, 21), rand(0, 59), rand(0, 59));
            $total = 0;
            $rows = [];

            foreach ($products->random(rand(1, 5)) as $p) {
                $qty = rand(1, 3);
                $subtotal = $p->price * $qty;
                $total += $subtotal;
                $rows[] = ['product_id' => $p->id, 'name' => $p->name, 'price' => $p->price, 'qty' => $qty, 'subtotal' => $subtotal];
            }

            $paid = (int) (ceil($total / 5000) * 5000);

            $trx = Transaction::create([
                'invoice' => 'IDM' . $date->format('ymdHis') . str_pad($i, 2, '0', STR_PAD_LEFT),
                'user_id' => $cashiers[array_rand($cashiers)]->id,
                'total' => $total,
                'paid' => $paid,
                'change' => $paid - $total,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $trx->items()->createMany($rows);
        }
    }
}
