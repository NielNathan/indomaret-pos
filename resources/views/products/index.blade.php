@extends('layouts.app')
@section('title', 'Produk')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp
<div x-data="{
        open: false, edit: null,
        form: { category_id: '', sku: '', name: '', price: '', stock: '' },
        create() { this.edit = null; this.form = { category_id: '', sku: '', name: '', price: '', stock: '' }; this.open = true; },
        change(p) { this.edit = p; this.form = { category_id: p.category_id, sku: p.sku, name: p.name, price: p.price, stock: p.stock }; this.open = true; }
     }">

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-2xl font-extrabold text-idm-dark">Produk</h1>
        <div class="flex gap-3">
            <form method="GET" class="flex">
                <input name="q" value="{{ request('q') }}" type="search" placeholder="Cari nama atau kode"
                       class="rounded-l-lg border border-slate-300 bg-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-idm-blue">
                <button class="rounded-r-lg bg-idm-blue text-white px-4 font-semibold">Cari</button>
            </form>
            @if ($isAdmin)
                <button @click="create()" class="rounded-lg bg-idm-red hover:bg-red-700 text-white font-bold px-4 py-2 transition">Tambah produk</button>
            @endif
        </div>
    </div>

    <div class="rounded-xl bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-semibold">Kode</th>
                    <th class="px-4 py-3 font-semibold">Nama produk</th>
                    <th class="px-4 py-3 font-semibold">Kategori</th>
                    <th class="px-4 py-3 font-semibold text-right">Harga</th>
                    <th class="px-4 py-3 font-semibold text-right">Stok</th>
                    @if ($isAdmin)<th class="px-4 py-3"></th>@endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-500">{{ $p->sku }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $p->name }}</td>
                        <td class="px-4 py-3">{{ $p->category->name }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $p->stock <= 5 ? 'bg-red-100 text-idm-red' : ($p->stock <= 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-slate-100 text-slate-700') }}">{{ $p->stock }}</span>
                        </td>
                        @if ($isAdmin)
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <button @click='change(@json($p->only(["id","category_id","sku","name","price","stock"])))' class="text-idm-blue font-semibold mr-3">Ubah</button>
                                <form method="POST" action="{{ route('products.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus {{ addslashes($p->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-idm-red font-semibold">Hapus</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">Produk tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>

    {{-- Modal tambah / ubah --}}
    @if ($isAdmin)
    <div x-cloak x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50" @keydown.escape.window="open = false">
        <form method="POST" :action="edit ? '{{ url('products') }}/' + edit.id : '{{ route('products.store') }}'"
              @click.outside="open = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            @csrf
            <template x-if="edit"><input type="hidden" name="_method" value="PUT"></template>
            <h2 class="text-lg font-extrabold text-idm-dark mb-4" x-text="edit ? 'Ubah produk' : 'Tambah produk'"></h2>

            <div class="space-y-3">
                <div>
                    <label class="text-sm font-semibold">Kategori</label>
                    <select name="category_id" x-model="form.category_id" required class="w-full mt-1 rounded-lg border border-slate-300 px-3 py-2">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-sm font-semibold">Kode barang</label>
                        <input name="sku" x-model="form.sku" required class="w-full mt-1 rounded-lg border border-slate-300 px-3 py-2"></div>
                    <div><label class="text-sm font-semibold">Stok</label>
                        <input name="stock" x-model="form.stock" type="number" min="0" required class="w-full mt-1 rounded-lg border border-slate-300 px-3 py-2"></div>
                </div>
                <div><label class="text-sm font-semibold">Nama produk</label>
                    <input name="name" x-model="form.name" required class="w-full mt-1 rounded-lg border border-slate-300 px-3 py-2"></div>
                <div><label class="text-sm font-semibold">Harga (Rp)</label>
                    <input name="price" x-model="form.price" type="number" min="0" required class="w-full mt-1 rounded-lg border border-slate-300 px-3 py-2"></div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" @click="open = false" class="rounded-lg px-4 py-2 font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button class="rounded-lg bg-idm-blue hover:bg-idm-dark text-white font-bold px-5 py-2">Simpan produk</button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
