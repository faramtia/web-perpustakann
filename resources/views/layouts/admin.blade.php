@php
    $u = auth()->user();
    $go = fn ($n) => \Illuminate\Support\Facades\Route::has($n) ? route($n) : '#';
    $ic = [
        'home'    => 'M3 12l9-9 9 9M5 10v10h14V10',
        'book'    => 'M4 5a2 2 0 012-2h13v16H6a2 2 0 00-2 2V5zM4 19a2 2 0 012-2h13',
        'folder'  => 'M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z',
        'pin'     => 'M12 21s7-6.2 7-11a7 7 0 10-14 0c0 4.8 7 11 7 11zM12 12a2 2 0 100-4 2 2 0 000 4z',
        'tag'     => 'M3 12V4h8l10 10-8 8L3 12zM7.5 8.5h.01',
        'refresh' => 'M4 4v5h5M20 20v-5h-5M5.5 9A8 8 0 0119 8M18.5 15A8 8 0 015 16',
        'undo'    => 'M9 14L4 9l5-5M4 9h11a5 5 0 010 10h-3',
        'clock'   => 'M12 21a9 9 0 100-18 9 9 0 000 18zM12 7v5l3 2',
        'coin'    => 'M12 21a9 9 0 100-18 9 9 0 000 18zM12 7v10M9 10h6M9 14h6',
        'check'   => 'M5 4h14v16H5zM9 12l2 2 4-4',
        'doc'     => 'M7 3h8l4 4v14H7zM15 3v4h4',
        'chat'    => 'M4 5h16v11H9l-5 4V5z',
        'cal'     => 'M5 5h14v15H5zM5 10h14M9 3v4M15 3v4',
        'edit'    => 'M5 4h11l3 3v13H5zM8 10h8M8 14h8',
        'cap'     => 'M2 9l10-5 10 5-10 5L2 9zM6 11v5c3 2 9 2 12 0v-5',
        'image'   => 'M4 5h16v14H4zM4 15l5-5 4 4 3-3 4 4',
        'chart'   => 'M5 20V10M12 20V4M19 20v-7',
        'cog'     => 'M12 8a4 4 0 100 8 4 4 0 000-8zM12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2',
        'search'  => 'M11 18a7 7 0 100-14 7 7 0 000 14zM20 20l-4-4',
        'menu'    => 'M4 6h16M4 12h16M4 18h16',
    ];
    $icon = fn ($k, $c = 'w-5 h-5') => '<svg class="'.$c.' flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="'.$ic[$k].'"/></svg>';
    $menu = [
        [null, [['admin.dashboard', 'Dashboard', 'home']]],
        ['DATA & KOLEKSI', [['katalog.index', 'Data Buku', 'book'], ['admin.kategori.index', 'Kategori', 'folder'], ['admin.lokasi.index', 'Lokasi', 'pin'], ['admin.tipe-koleksi.index', 'Tipe Koleksi', 'tag']]],
        ['LAYANAN SIRKULASI', [['petugas.peminjaman.index', 'Peminjaman', 'refresh'], ['petugas.peminjaman.index', 'Pengembalian', 'undo'], ['petugas.reservasi.index', 'Reservasi', 'clock'], [null, 'Denda', 'coin']]],
        ['LAYANAN PENGGUNA', [['petugas.absensi.index', 'Absensi', 'check'], ['ejurnal.index', 'E-Publication', 'doc'], ['petugas.feedback.index', 'Feedback / Ask a Librarian', 'chat'], ['event.index', 'Event', 'cal'], ['artikel.index', 'Artikel', 'edit'], ['petugas.tugas-akhir.index', 'Tugas Akhir', 'cap'], [null, 'Gallery', 'image']]],
        ['LAPORAN', [['admin.laporan.index', 'Laporan', 'chart']]],
    ];
    $topLinks = [['Home', $go('home')], ['Catalog', $go('katalog.index')], ['Services & Facilities', $go('home').'#layanan'], ['E-Journal', $go('ejurnal.index')], ['Event', $go('event.index')], ['News & Info', $go('artikel.index')]];
    $inisial = strtoupper(mb_substr($u->nama ?? 'A', 0, 2));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | Perpustakaan PNL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: {
            gold: { 50:'#fdf8e8', 100:'#faf0c8', 400:'#f2c94c', 500:'#e8b923', 600:'#c99a12', 700:'#9c780e' },
        } } } }
    </script>
    <style>body{font-family:ui-sans-serif,system-ui,"Segoe UI",Roboto,sans-serif}.topnav::-webkit-scrollbar{display:none}</style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

<header class="bg-gold-500 text-gray-900 sticky top-0 z-30 shadow-md">
    <div class="flex items-center gap-4 px-4 lg:px-6 h-16">
        <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="lg:hidden" aria-label="Buka menu">{!! $icon('menu', 'w-6 h-6') !!}</button>
        <a href="{{ $go('home') }}" class="flex items-center gap-2 flex-shrink-0">
            <img src="{{ asset('images/logo-pnl.png') }}" alt="Logo PNL" class="h-9 w-9 object-contain bg-white rounded-full p-0.5">
            <span class="font-bold leading-tight text-sm sm:text-base">Perpustakaan<br class="sm:hidden"> Politeknik Negeri Lhokseumawe</span>
        </a>
        <nav class="topnav hidden xl:flex items-center gap-6 text-sm font-medium mx-auto overflow-x-auto whitespace-nowrap">
            @foreach ($topLinks as [$l, $url])
                <a href="{{ $url }}" class="hover:underline">{{ $l }}</a>
            @endforeach
        </nav>
        <div class="flex items-center gap-3 ml-auto">
            <span class="hidden sm:inline">{!! $icon('search') !!}</span>
            <div class="flex items-center gap-2 pl-3 border-l border-gray-900/25">
                <span class="w-9 h-9 rounded-full bg-gray-900 text-gold-400 font-bold flex items-center justify-center text-sm">{{ $inisial }}</span>
                <div class="hidden sm:block leading-tight">
                    <p class="font-semibold text-sm">{{ $u->nama }}</p>
                    <p class="text-xs text-gray-800">Administrator</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-xs sm:text-sm bg-gray-900/10 hover:bg-gray-900/20 px-3 py-1.5 rounded-lg">Keluar</button>
            </form>
        </div>
    </div>
</header>

<div class="flex">
    <aside id="sidebar" class="w-64 flex-shrink-0 bg-gray-900 text-white fixed lg:sticky top-16 left-0 z-20 h-[calc(100vh-4rem)] overflow-y-auto transform -translate-x-full lg:translate-x-0 transition-transform duration-200 flex flex-col">
        <nav class="flex-1 p-3 space-y-1 text-sm">
            @foreach ($menu as [$judul, $items])
                @if ($judul)
                    <p class="px-3 pt-4 pb-1 text-[11px] font-semibold tracking-wider text-gray-500">{{ $judul }}</p>
                @endif
                @foreach ($items as [$rn, $label, $ik])
                    @php $aktif = $rn && request()->routeIs($rn) && $label !== 'Pengembalian'; @endphp
                    <a href="{{ $rn ? $go($rn) : '#' }}" @if(!$rn) title="Segera tersedia" @endif
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $aktif ? 'bg-gold-600 text-white font-semibold shadow' : 'text-gray-300 hover:bg-gray-800' }} {{ $rn ? '' : 'opacity-60' }}">
                        {!! $icon($ik) !!}<span>{{ $label }}</span>
                    </a>
                @endforeach
            @endforeach
        </nav>
        <div class="p-3 border-t border-gray-700">
            <a href="#" title="Segera tersedia" class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 border border-gray-700">
                <span class="flex items-center gap-3">{!! $icon('cog') !!}Pengaturan</span><span class="text-gray-400">›</span>
            </a>
        </div>
    </aside>

    <div class="flex-1 min-w-0 flex flex-col min-h-[calc(100vh-4rem)]">
        <main class="flex-1 p-4 sm:p-6">
            @hasSection('page-title')
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">@yield('page-title')</h1>
            @endif
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-100 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
        <footer class="text-center text-xs text-gray-400 py-4 border-t bg-white">&copy; {{ date('Y') }} Perpustakaan Politeknik Negeri Lhokseumawe</footer>
    </div>
</div>

@stack('scripts')
</body>
</html>
