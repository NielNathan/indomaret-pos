@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.'); @endphp

<div class="flex flex-wrap items-end justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-idm-dark">Halo, {{ explode(' ', auth()->user()->name)[0] }}</h1>
        <p class="text-slate-500 text-sm">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <a href="{{ route('pos.index') }}" class="rounded-lg bg-idm-red hover:bg-red-700 text-white font-bold px-5 py-2.5 transition">Mulai transaksi</a>
</div>

<div class="grid gap-4 sm:grid-cols-3 mb-6">
    <div class="rounded-xl bg-idm-blue text-white p-5">
        <p class="text-blue-100 text-sm">Penjualan hari ini</p>
        <p class="text-3xl font-extrabold mt-1">{{ $rp($salesToday) }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 border border-slate-200">
        <p class="text-slate-500 text-sm">Transaksi hari ini</p>
        <p class="text-3xl font-extrabold mt-1 text-idm-dark">{{ $trxToday }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 border border-slate-200">
        <p class="text-slate-500 text-sm">Jumlah produk</p>
        <p class="text-3xl font-extrabold mt-1 text-idm-dark">{{ $productCount }}</p>
    </div>
</div>

<div class="grid gap-4 lg:grid-cols-3">
    <section class="lg:col-span-2 rounded-xl bg-white border border-slate-200 p-5">
        <h2 class="font-bold text-idm-dark mb-5">Penjualan 7 hari terakhir</h2>
        <div class="flex items-end gap-3 h-52">
            @foreach ($week as $d)
                <div class="flex-1 flex flex-col items-center justify-end h-full gap-2" title="{{ $rp($d['total']) }}">
                    <span class="text-[11px] font-semibold text-slate-500">{{ $d['total'] ? number_format($d['total'] / 1000, 0, ',', '.') . 'rb' : '-' }}</span>
                    <div class="w-full rounded-t-md {{ $loop->last ? 'bg-idm-red' : 'bg-idm-blue' }}" style="height: {{ max(3, $d['total'] / $weekMax * 100) }}%"></div>
                    <span class="text-xs text-slate-500">{{ $d['label'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <div class="space-y-4">
        <section class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="font-bold text-idm-dark mb-3">Produk terlaris</h2>
            <ol class="space-y-2 text-sm">
                @forelse ($topProducts as $p)
                    <li class="flex justify-between gap-3"><span class="truncate">{{ $p->name }}</span><span class="font-bold text-idm-blue">{{ $p->qty }} pcs</span></li>
                @empty
                    <li class="text-slate-500">Belum ada penjualan.</li>
                @endforelse
            </ol>
        </section>
        <section class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="font-bold text-idm-dark mb-3">Stok menipis</h2>
            <ul class="space-y-2 text-sm">
                @forelse ($lowStock as $p)
                    <li class="flex justify-between gap-3"><span class="truncate">{{ $p->name }}</span>
                        <span class="shrink-0 rounded-full px-2 text-xs font-bold {{ $p->stock <= 5 ? 'bg-red-100 text-idm-red' : 'bg-yellow-100 text-yellow-800' }}">sisa {{ $p->stock }}</span></li>
                @empty
                    <li class="text-slate-500">Semua stok aman.</li>
                @endforelse
            </ul>
        </section>
    </div>
</div>
@endsection
