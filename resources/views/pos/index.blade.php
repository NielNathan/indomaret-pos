@extends('layouts.app')
@section('title', 'Kasir')

@section('content')
<div x-data="pos(@js($products))" class="grid gap-5 xl:grid-cols-[1fr_380px] xl:h-[calc(100vh-4rem)]">

    {{-- Daftar produk --}}
    <section class="flex flex-col min-h-0">
        <div class="flex flex-wrap gap-3 mb-4">
            <input x-model="q" type="search" placeholder="Cari nama produk atau kode barang"
                   class="flex-1 min-w-[220px] rounded-lg border border-slate-300 bg-white px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-idm-blue">
        </div>
        <div class="flex gap-2 overflow-x-auto pb-3 mb-1">
            <button @click="cat = 0" :class="cat == 0 ? 'bg-idm-blue text-white' : 'bg-white text-slate-600 border border-slate-200'" class="rounded-full px-4 py-1.5 text-sm font-semibold whitespace-nowrap">Semua</button>
            @foreach ($categories as $c)
                <button @click="cat = {{ $c->id }}" :class="cat == {{ $c->id }} ? 'bg-idm-blue text-white' : 'bg-white text-slate-600 border border-slate-200'" class="rounded-full px-4 py-1.5 text-sm font-semibold whitespace-nowrap">{{ $c->name }}</button>
            @endforeach
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 2xl:grid-cols-4 gap-3 overflow-y-auto content-start pr-1 pb-2">
            <template x-for="p in filtered" :key="p.id">
                <button @click="add(p)" class="text-left rounded-xl bg-white border border-slate-200 p-4 hover:border-idm-blue hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-idm-blue">
                    <p class="text-xs text-slate-400" x-text="p.sku"></p>
                    <p class="font-semibold leading-snug mt-1 min-h-[2.5rem]" x-text="p.name"></p>
                    <div class="flex items-end justify-between mt-3">
                        <p class="font-extrabold text-idm-blue" x-text="rp(p.price)"></p>
                        <p class="text-xs font-semibold" :class="p.stock <= 5 ? 'text-idm-red' : 'text-slate-400'" x-text="'stok ' + p.stock"></p>
                    </div>
                </button>
            </template>
            <p x-show="filtered.length === 0" class="col-span-full text-slate-500 py-10 text-center">Produk tidak ditemukan. Coba kata kunci lain.</p>
        </div>
    </section>

    {{-- Keranjang --}}
    <aside class="flex flex-col rounded-2xl bg-white border border-slate-200 overflow-hidden min-h-[420px]">
        <div class="flex h-1.5"><span class="flex-1 bg-idm-blue"></span><span class="flex-1 bg-idm-red"></span><span class="flex-1 bg-idm-yellow"></span></div>
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h2 class="font-extrabold text-idm-dark">Keranjang</h2>
            <button x-show="cart.length" @click="cart = []" class="text-sm text-idm-red font-semibold">Kosongkan</button>
        </div>

        <div class="flex-1 overflow-y-auto px-5 divide-y divide-slate-100">
            <template x-for="i in cart" :key="i.id">
                <div class="py-3">
                    <p class="text-sm font-semibold" x-text="i.name"></p>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center gap-2">
                            <button @click="dec(i)" class="w-7 h-7 rounded-md bg-slate-100 hover:bg-slate-200 font-bold" aria-label="Kurangi">-</button>
                            <span class="w-8 text-center font-bold" x-text="i.qty"></span>
                            <button @click="inc(i)" class="w-7 h-7 rounded-md bg-slate-100 hover:bg-slate-200 font-bold" aria-label="Tambah">+</button>
                            <span class="text-xs text-slate-400 ml-1" x-text="'x ' + rp(i.price)"></span>
                        </div>
                        <p class="font-bold" x-text="rp(i.price * i.qty)"></p>
                    </div>
                </div>
            </template>
            <p x-show="!cart.length" class="py-16 text-center text-slate-400 text-sm">Pilih produk di sebelah kiri untuk memulai transaksi.</p>
        </div>

        <div class="border-t border-slate-100 p-5 space-y-3 bg-slate-50">
            <div class="flex justify-between items-baseline">
                <span class="text-slate-500">Total</span>
                <span class="text-3xl font-extrabold text-idm-dark" x-text="rp(total)"></span>
            </div>
            <div>
                <label class="text-sm font-semibold" for="paid">Uang diterima</label>
                <input id="paid" x-model="paid" type="number" min="0" inputmode="numeric" placeholder="0"
                       class="w-full mt-1 rounded-lg border border-slate-300 px-4 py-2.5 text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-idm-blue">
                <div class="flex flex-wrap gap-2 mt-2">
                    <button @click="paid = total" :disabled="!total" class="rounded-md bg-white border border-slate-300 px-3 py-1 text-sm font-semibold disabled:opacity-40">Uang pas</button>
                    <template x-for="n in [20000, 50000, 100000]">
                        <button @click="paid = n" class="rounded-md bg-white border border-slate-300 px-3 py-1 text-sm font-semibold" x-text="rp(n)"></button>
                    </template>
                </div>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Kembalian</span>
                <span class="font-bold" :class="change < 0 ? 'text-idm-red' : 'text-emerald-600'" x-text="paid === '' ? '-' : (change < 0 ? 'Kurang ' + rp(-change) : rp(change))"></span>
            </div>
            <p x-show="error" x-text="error" class="text-sm text-idm-red font-medium"></p>
            <button @click="pay()" :disabled="!cart.length || change < 0 || paid === '' || loading"
                    class="w-full rounded-lg bg-idm-red hover:bg-red-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold py-3 transition focus:outline-none focus:ring-4 focus:ring-idm-yellow/60">
                <span x-text="loading ? 'Memproses...' : 'Bayar'"></span>
            </button>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
function pos(products) {
    return {
        products, q: '', cat: 0, cart: [], paid: '', loading: false, error: '',

        get filtered() {
            const s = this.q.toLowerCase();
            return this.products.filter(p =>
                (this.cat == 0 || p.category_id == this.cat) &&
                (p.name.toLowerCase().includes(s) || p.sku.includes(s)));
        },
        get total() { return this.cart.reduce((sum, i) => sum + i.price * i.qty, 0); },
        get change() { return (parseInt(this.paid) || 0) - this.total; },

        rp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); },

        add(p) {
            const found = this.cart.find(c => c.id === p.id);
            if (found) { if (found.qty < p.stock) found.qty++; }
            else this.cart.push({ id: p.id, name: p.name, price: p.price, stock: p.stock, qty: 1 });
        },
        inc(i) { if (i.qty < i.stock) i.qty++; },
        dec(i) { i.qty--; if (i.qty < 1) this.cart = this.cart.filter(c => c !== i); },

        async pay() {
            this.loading = true; this.error = '';
            try {
                const res = await fetch('{{ route('pos.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({
                        items: this.cart.map(c => ({ product_id: c.id, qty: c.qty })),
                        paid: parseInt(this.paid),
                    }),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.error = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Transaksi gagal.');
                    this.loading = false;
                    return;
                }
                window.location.href = data.redirect;
            } catch (e) {
                this.error = 'Tidak dapat terhubung ke server.';
                this.loading = false;
            }
        },
    };
}
</script>
@endpush
