<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Indomaret POS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
            colors: { idm: { blue: '#0B3C8F', dark: '#072A66', red: '#E4002B', yellow: '#FFD100' } }
        } } }
    </script>
</head>
<body class="font-sans antialiased bg-white">
<div class="min-h-screen grid lg:grid-cols-2">
    <section class="relative hidden lg:flex flex-col justify-between bg-idm-dark text-white p-12 overflow-hidden">
        <div class="absolute -right-24 top-0 bottom-0 w-48 flex flex-col -skew-x-6">
            <span class="flex-1 bg-blue-600"></span><span class="flex-1 bg-idm-red"></span><span class="flex-1 bg-idm-yellow"></span>
        </div>
        <div>
            <p class="text-3xl font-extrabold tracking-tight">Indomaret</p>
            <p class="text-blue-200">Point of Sales</p>
        </div>
        <div class="max-w-md">
            <h1 class="text-5xl font-extrabold leading-[1.1]">Belanja cepat, antrean singkat.</h1>
            <p class="mt-5 text-blue-100 text-lg">Catat penjualan, pantau stok, dan lihat laporan harian toko dari satu layar.</p>
        </div>
        <p class="text-sm text-blue-300">Proyek demo. Data hanya untuk uji coba.</p>
    </section>

    <section class="flex items-center justify-center p-6 sm:p-12">
        <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm">
            @csrf
            <div class="lg:hidden flex flex-col w-10 rounded overflow-hidden mb-4">
                <span class="h-2 bg-idm-blue"></span><span class="h-2 bg-idm-red"></span><span class="h-2 bg-idm-yellow"></span>
            </div>
            <h2 class="text-3xl font-extrabold text-idm-dark">Masuk ke kasir</h2>
            <p class="text-slate-500 mt-1 mb-8">Gunakan akun yang diberikan admin toko.</p>

            @if ($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ $errors->first() }}</div>
            @endif

            <label class="block text-sm font-semibold mb-1.5" for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 mb-5 focus:outline-none focus:ring-2 focus:ring-idm-blue focus:border-idm-blue">

            <label class="block text-sm font-semibold mb-1.5" for="password">Kata sandi</label>
            <input id="password" name="password" type="password" required
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 mb-5 focus:outline-none focus:ring-2 focus:ring-idm-blue focus:border-idm-blue">

            <label class="flex items-center gap-2 text-sm text-slate-600 mb-6">
                <input type="checkbox" name="remember" class="rounded border-slate-300"> Tetap masuk di perangkat ini
            </label>

            <button class="w-full rounded-lg bg-idm-blue hover:bg-idm-dark text-white font-bold py-3 transition focus:outline-none focus:ring-4 focus:ring-idm-yellow/60">Masuk</button>

            <div class="mt-8 rounded-lg border-l-4 border-idm-yellow bg-amber-50 px-4 py-3 text-sm text-slate-700">
                <p class="font-semibold mb-1">Akun uji coba</p>
                <p>Admin: admin@indomaret.test</p>
                <p>Kasir: kasir@indomaret.test</p>
                <p>Kata sandi: <span class="font-semibold">password</span></p>
            </div>
        </form>
    </section>
</div>
</body>
</html>
