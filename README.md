# Indomaret POS (Laravel)

Aplikasi Point of Sales sederhana bertema Indomaret: login, kasir, manajemen produk,
riwayat transaksi + struk, dan REST API (Laravel Sanctum).

Folder ini adalah **lapisan file** yang ditimpa ke proyek Laravel 11/12 baru.

## Instalasi

Butuh PHP 8.2+, Composer, dan SQLite (bawaan) atau MySQL.

```bash
composer create-project laravel/laravel indomaret-pos
cd indomaret-pos

# aktifkan REST API + Sanctum (jawab "no" jika diminta menjalankan migrasi)
php artisan install:api

# salin isi folder ini ke dalam proyek, timpa file yang sama
# (app/, database/, resources/, routes/)

# opsional: tanggal berbahasa Indonesia, tambahkan di .env
#   APP_LOCALE=id
#   APP_TIMEZONE=Asia/Jakarta   (ubah juga 'timezone' di config/app.php jika perlu)

php artisan migrate:fresh --seed
php artisan serve
```

Buka http://127.0.0.1:8000

### Memakai MySQL (opsional)
Ubah di `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=indomaret_pos
DB_USERNAME=root
DB_PASSWORD=
```
Buat database `indomaret_pos`, lalu jalankan `php artisan migrate:fresh --seed`.

## Akun uji coba (kata sandi: `password`)
| Peran | Email |
|---|---|
| Admin | admin@indomaret.test |
| Kasir | kasir@indomaret.test |
| Kasir | kasir2@indomaret.test |

Admin dapat tambah/ubah/hapus produk. Kasir hanya melihat produk dan melakukan transaksi.

## Data dummy
7 kategori, 32 produk (beberapa sengaja berstok rendah), 3 pengguna, dan 60 transaksi
selama 7 hari terakhir agar dashboard langsung terisi.

## Struktur penting
```
app/Http/Controllers/            Controller web (login, dashboard, kasir, produk, transaksi)
app/Http/Controllers/Api/        Controller REST API
app/Models/Transaction.php       Logika checkout (cek stok, kurangi stok, simpan) - dipakai web & API
database/migrations/             Skema database
database/seeders/                Data dummy
routes/web.php  routes/api.php   Routing
resources/views/                 Tampilan Blade (Tailwind CDN + Alpine.js)
```

## REST API
Base URL: `http://127.0.0.1:8000/api`. Selalu kirim header `Accept: application/json`.
Endpoint selain login butuh header `Authorization: Bearer <token>`.

| Method | Endpoint | Keterangan |
|---|---|---|
| POST | /login | Body: email, password. Mengembalikan token |
| GET | /me | Data pengguna login |
| POST | /logout | Hapus token saat ini |
| GET | /categories | Daftar kategori |
| GET | /products | Query opsional: q, category_id, per_page |
| GET | /products/{id} | Detail produk |
| POST | /products | Admin. Body: category_id, sku, name, price, stock |
| PUT | /products/{id} | Admin |
| DELETE | /products/{id} | Admin |
| GET | /transactions | Query opsional: date (YYYY-MM-DD), per_page |
| GET | /transactions/{id} | Detail transaksi + item |
| POST | /transactions | Body: items[{product_id, qty}], paid |

### Contoh dengan curl
```bash
# 1. Login
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"kasir@indomaret.test","password":"password"}'

# 2. Lihat produk
curl http://127.0.0.1:8000/api/products?q=indomie \
  -H "Accept: application/json" -H "Authorization: Bearer TOKEN_ANDA"

# 3. Buat transaksi
curl -X POST http://127.0.0.1:8000/api/transactions \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN_ANDA" \
  -d '{"items":[{"product_id":1,"qty":2},{"product_id":13,"qty":5}],"paid":50000}'
```

## Catatan
- Tailwind dan Alpine.js dimuat lewat CDN, jadi tidak perlu `npm install`. Butuh internet saat membuka halaman.
- Harga dianggap sudah termasuk PPN, seperti struk ritel pada umumnya.
- Proyek ini untuk belajar/uji coba. Untuk produksi, tambahkan pembatasan laju login, audit log, dan pengujian otomatis.
