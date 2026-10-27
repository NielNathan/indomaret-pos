@extends('layouts.app')
@section('title', 'Struk ' . $transaction->invoice)

@section('content')
<div class="print:hidden flex gap-3 mb-5">
    <a href="{{ route('pos.index') }}" class="rounded-lg bg-idm-red hover:bg-red-700 text-white font-bold px-5 py-2.5">Transaksi baru</a>
    <button onclick="window.print()" class="rounded-lg bg-white border border-slate-300 font-semibold px-5 py-2.5">Cetak struk</button>
    <a href="{{ route('transactions.index') }}" class="rounded-lg px-4 py-2.5 font-semibold text-slate-600">Riwayat</a>
</div>

<div class="mx-auto max-w-sm rounded-xl bg-white border border-slate-200 print:border-0 overflow-hidden font-mono text-[13px]">
    <div class="flex h-2 print:hidden"><span class="flex-1 bg-idm-blue"></span><span class="flex-1 bg-idm-red"></span><span class="flex-1 bg-idm-yellow"></span></div>
    <div class="p-6">
        <div class="text-center mb-4">
            <p class="font-sans text-lg font-extrabold text-idm-dark">INDOMARET</p>
            <p class="text-slate-500">Jl. Pemuda No. 1, Semarang</p>
        </div>
        <div class="border-y border-dashed border-slate-300 py-2 mb-3 space-y-0.5">
            <div class="flex justify-between"><span>No.</span><span>{{ $transaction->invoice }}</span></div>
            <div class="flex justify-between"><span>Tanggal</span><span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="flex justify-between"><span>Kasir</span><span>{{ $transaction->user->name }}</span></div>
        </div>

        @foreach ($transaction->items as $i)
            <div class="mb-2">
                <p>{{ $i->name }}</p>
                <div class="flex justify-between text-slate-600">
                    <span>{{ $i->qty }} x {{ number_format($i->price, 0, ',', '.') }}</span>
                    <span>{{ number_format($i->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach

        <div class="border-t border-dashed border-slate-300 mt-3 pt-3 space-y-1">
            <div class="flex justify-between font-bold text-base"><span>TOTAL</span><span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>Tunai</span><span>Rp {{ number_format($transaction->paid, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>Kembali</span><span>Rp {{ number_format($transaction->change, 0, ',', '.') }}</span></div>
        </div>
        <p class="text-center mt-5 text-slate-500">Terima kasih telah berbelanja.<br>Barang yang sudah dibeli tidak dapat dikembalikan.</p>
    </div>
</div>
@endsection
