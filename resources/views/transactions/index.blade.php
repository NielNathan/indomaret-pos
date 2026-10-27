@extends('layouts.app')
@section('title', 'Transaksi')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-2xl font-extrabold text-idm-dark">Riwayat transaksi</h1>
    <form method="GET" class="flex items-center gap-2">
        <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border border-slate-300 bg-white px-3 py-2">
        <button class="rounded-lg bg-idm-blue text-white font-semibold px-4 py-2">Filter</button>
        @if (request('date'))<a href="{{ route('transactions.index') }}" class="text-sm text-idm-red font-semibold">Hapus filter</a>@endif
    </form>
</div>

<div class="rounded-xl bg-white border border-slate-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
                <th class="px-4 py-3 font-semibold">No. struk</th>
                <th class="px-4 py-3 font-semibold">Waktu</th>
                <th class="px-4 py-3 font-semibold">Kasir</th>
                <th class="px-4 py-3 font-semibold text-right">Item</th>
                <th class="px-4 py-3 font-semibold text-right">Total</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($transactions as $t)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-semibold">{{ $t->invoice }}</td>
                    <td class="px-4 py-3">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">{{ $t->user->name }}</td>
                    <td class="px-4 py-3 text-right">{{ $t->items_count }}</td>
                    <td class="px-4 py-3 text-right font-bold text-idm-blue">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right"><a href="{{ route('transactions.show', $t) }}" class="text-idm-blue font-semibold">Lihat struk</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">Belum ada transaksi pada tanggal ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $transactions->links() }}</div>
@endsection
