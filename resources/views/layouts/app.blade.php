<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Indomaret POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
                colors: { idm: { blue: '#0B3C8F', dark: '#072A66', red: '#E4002B', yellow: '#FFD100', soft: '#F2F5FB' } }
            } }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-idm-soft font-sans text-slate-800 antialiased">
@php
    $nav = [
        ['dashboard', 'Dashboard', 'M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
        ['pos.index', 'Kasir', 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z'],
        ['products.index', 'Produk', 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9'],
        ['transactions.index', 'Transaksi', 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
    ];
@endphp

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="print:hidden hidden lg:flex w-64 shrink-0 flex-col bg-idm-dark text-white sticky top-0 h-screen">
        <div class="px-6 pt-7 pb-6">
            <div class="flex flex-col w-11 rounded overflow-hidden mb-3">
                <span class="h-2 bg-blue-500"></span><span class="h-2 bg-idm-red"></span><span class="h-2 bg-idm-yellow"></span>
            </div>
            <p class="text-xl font-extrabold tracking-tight leading-none">Indomaret</p>
            <p class="text-sm text-blue-200 mt-1">Point of Sales</p>
        </div>
        <nav class="flex-1 px-3 space-y-1">
            @foreach ($nav as [$route, $label, $icon])
                @php $active = request()->routeIs(explode('.', $route)[0] . '*'); @endphp
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition
                          {{ $active ? 'bg-idm-yellow text-idm-dark' : 'text-blue-100 hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                    {{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="p-4 border-t border-white/10">
            <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
            <p class="text-xs text-blue-200 mb-3">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Kasir' }}</p>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="w-full rounded-lg bg-white/10 hover:bg-idm-red transition py-2 text-sm font-semibold">Keluar</button>
            </form>
        </div>
    </aside>

    <div class="flex-1 min-w-0 flex flex-col">
        {{-- Bar atas untuk layar kecil --}}
        <header class="print:hidden lg:hidden bg-idm-dark text-white">
            <div class="flex items-center justify-between px-4 py-3">
                <p class="font-extrabold">Indomaret <span class="text-idm-yellow">POS</span></p>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-sm text-blue-200">Keluar</button></form>
            </div>
            <nav class="flex gap-1 px-2 pb-2 overflow-x-auto">
                @foreach ($nav as [$route, $label])
                    <a href="{{ route($route) }}" class="rounded-md px-3 py-1.5 text-sm font-semibold whitespace-nowrap {{ request()->routeIs(explode('.', $route)[0] . '*') ? 'bg-idm-yellow text-idm-dark' : 'text-blue-100' }}">{{ $label }}</a>
                @endforeach
            </nav>
        </header>

        <main class="flex-1 p-4 sm:p-8">
            @if (session('success'))
                <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,3500)" class="print:hidden mb-5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="print:hidden mb-5 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                    @foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
