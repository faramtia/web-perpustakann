@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
@php
    $go = fn ($n) => \Illuminate\Support\Facades\Route::has($n) ? route($n) : '#';
    $fmt = fn ($n) => number_format((int) $n, 0, ',', '.');
    $maks = max(1, collect($grafik)->max('jumlah'));
    $total = max(1, $stokTersedia + $sedangDipinjam);
    $pTersedia = round($stokTersedia / $total * 100);
    $pDipinjam = 100 - $pTersedia;
    $kartu = [
        ['Total Buku', $fmt($totalBuku), $fmt($judulBuku).' judul', 'from-gold-50', 'bg-gold-100 text-gold-700', 'M4 5a2 2 0 012-2h13v16H6a2 2 0 00-2 2V5z'],
        ['Anggota', $fmt($totalAnggota), 'mahasiswa & dosen', 'from-blue-50', 'bg-blue-100 text-blue-600', 'M16 20v-1a4 4 0 00-4-4H8a4 4 0 00-4 4v1M10 11a3 3 0 100-6 3 3 0 000 6'],
        ['Buku Dipinjam', $fmt($peminjamanAktif), 'transaksi aktif', 'from-green-50', 'bg-green-100 text-green-600', 'M4 8h14M14 4l4 4-4 4M20 16H6M10 12l-4 4 4 4'],
        ['Terlambat', $fmt($terlambat), 'lewat jatuh tempo', 'from-red-50', 'bg-red-100 text-red-600', 'M12 21a9 9 0 100-18 9 9 0 000 18zM12 7v5l3 2'],
        ['Total Denda', 'Rp '.$fmt($totalDenda), 'belum lunas', 'from-purple-50', 'bg-purple-100 text-purple-600', 'M12 21a9 9 0 100-18 9 9 0 000 18zM12 7v10M9 10h6M9 14h6'],
    ];
    $aksi = [
        ['Tambah Buku', $go('admin.buku.create'), 'bg-gold-50 text-gold-700', 'M4 5a2 2 0 012-2h13v16H6a2 2 0 00-2 2V5z'],
        ['Proses Pengembalian', $go('petugas.peminjaman.index'), 'bg-blue-50 text-blue-700', 'M4 8h14M14 4l4 4-4 4M20 16H6M10 12l-4 4 4 4'],
        ['Buat Event', $go('event.index'), 'bg-green-50 text-green-700', 'M5 5h14v15H5zM5 10h14M9 3v4M15 3v4'],
        ['Generate Laporan', $go('admin.laporan.index'), 'bg-purple-50 text-purple-700', 'M7 3h8l4 4v14H7zM15 3v4h4'],
    ];
    $notif = [
        [$terlambat, 'buku terlambat dikembalikan', 'bg-red-500', '!', $go('petugas.peminjaman.index')],
        [$reservasiMenunggu, 'reservasi menunggu', 'bg-amber-400', '⏳', $go('petugas.reservasi.index')],
        [$feedbackBelumDibalas, 'feedback belum dijawab', 'bg-blue-400', '✉', $go('petugas.feedback.index')],
        [$kunjunganHariIni, 'kunjungan hari ini', 'bg-green-500', '✓', $go('petugas.absensi.index')],
    ];
@endphp

<nav class="text-sm text-gray-500 mb-1">Dashboard</nav>
<div class="flex flex-wrap items-end justify-between gap-3 mb-5">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Selamat Datang, {{ auth()->user()->nama }}!</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola koleksi, layanan pengguna, dan aktivitas perpustakaan secara real-time.</p>
    </div>
    <div class="text-sm text-right text-gray-600">
        <p class="font-semibold">{{ now()->translatedFormat('l, d F Y') }}</p>
        <p class="text-gray-400">Pukul {{ now()->format('H.i') }} WIB</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-5">
    @foreach ($kartu as [$l, $v, $s, $bg, $ik, $path])
        <div class="rounded-xl border bg-gradient-to-br {{ $bg }} to-white p-4">
            <div class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-lg {{ $ik }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-sm text-gray-500">{{ $l }}</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $v }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $s }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-xl border p-5 lg:col-span-1">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-800">Grafik Peminjaman Buku</h2>
            <span class="text-xs border rounded px-2 py-1 text-gray-500">Minggu Ini</span>
        </div>
        <div class="flex items-end justify-between gap-2 h-44 border-b border-l px-1">
            @foreach ($grafik as $g)
                <div class="flex-1 flex flex-col items-center justify-end h-full" title="{{ $g['jumlah'] }} peminjaman">
                    <span class="text-[10px] text-gray-400 mb-1">{{ $g['jumlah'] }}</span>
                    <div class="w-full rounded-t bg-gradient-to-t from-gold-500 to-gold-100" style="height: {{ max(3, $g['jumlah'] / $maks * 100) }}%"></div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-between gap-2 text-xs text-gray-500 mt-1 px-1">
            @foreach ($grafik as $g)<span class="flex-1 text-center">{{ $g['hari'] }}</span>@endforeach
        </div>
    </div>

    <div class="bg-white rounded-xl border p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Status Koleksi</h2>
        <div class="flex items-center gap-5">
            <div class="relative w-36 h-36 rounded-full flex-shrink-0" style="background: conic-gradient(#c99a12 0 {{ $pTersedia }}%, #374151 {{ $pTersedia }}% 100%)">
                <div class="absolute inset-4 bg-white rounded-full flex flex-col items-center justify-center">
                    <span class="text-xl font-bold">{{ $fmt($stokTersedia + $sedangDipinjam) }}</span>
                    <span class="text-xs text-gray-500">Buku</span>
                </div>
            </div>
            <ul class="text-sm space-y-3">
                <li class="flex items-start gap-2"><span class="w-3 h-3 rounded-full mt-1" style="background:#c99a12"></span><span><b>Tersedia</b> {{ $pTersedia }}%<br><span class="text-gray-400 text-xs">{{ $fmt($stokTersedia) }} buku</span></span></li>
                <li class="flex items-start gap-2"><span class="w-3 h-3 rounded-full mt-1" style="background:#374151"></span><span><b>Dipinjam</b> {{ $pDipinjam }}%<br><span class="text-gray-400 text-xs">{{ $fmt($sedangDipinjam) }} buku</span></span></li>
            </ul>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 gap-3">
            @foreach ($aksi as [$l, $url, $cls, $path])
                <a href="{{ $url }}" class="rounded-xl p-4 {{ $cls }} hover:shadow transition flex flex-col items-center justify-center gap-2 text-center text-sm font-medium h-24">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>{{ $l }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <div class="bg-white rounded-xl border overflow-hidden xl:col-span-2">
        <div class="px-5 py-4 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Transaksi Terbaru</h2>
            <a href="{{ $go('petugas.peminjaman.index') }}" class="text-sm border rounded-lg px-3 py-1 hover:bg-gray-50">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-left">
                    <tr>
                        <th class="px-5 py-3 font-medium">No</th>
                        <th class="px-3 py-3 font-medium">Tanggal</th>
                        <th class="px-3 py-3 font-medium">Jenis</th>
                        <th class="px-3 py-3 font-medium">Judul Buku</th>
                        <th class="px-3 py-3 font-medium">Nama Anggota</th>
                        <th class="px-3 py-3 font-medium">Status</th>
                        <th class="px-3 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($peminjamanTerbaru as $p)
                        <tr>
                            <td class="px-5 py-3">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 whitespace-nowrap">{{ optional($p->tanggal_pinjam)->format('d M Y') ?? '-' }}</td>
                            <td class="px-3 py-3">{{ $p->status === \App\Models\Peminjaman::DIKEMBALIKAN ? 'Pengembalian' : 'Peminjaman' }}</td>
                            <td class="px-3 py-3">{{ $p->detail->pluck('buku.judul')->join(', ') ?: '-' }}</td>
                            <td class="px-3 py-3">{{ $p->user->nama ?? '-' }}</td>
                            <td class="px-3 py-3"><x-status-badge :status="$p->status_tampil" /></td>
                            <td class="px-3 py-3"><a href="{{ $go('petugas.peminjaman.index') }}" class="inline-block border rounded px-2 text-gray-500 hover:bg-gray-50">···</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 italic">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl border p-5">
            <h2 class="font-semibold text-gray-800 mb-3">Notifikasi</h2>
            <ul class="divide-y">
                @foreach ($notif as [$n, $teks, $warna, $simbol, $url])
                    <li>
                        <a href="{{ $url }}" class="flex items-center gap-3 py-2.5 hover:bg-gray-50 rounded">
                            <span class="w-9 h-9 rounded-full {{ $warna }} text-white flex items-center justify-center font-bold flex-shrink-0">{{ $simbol }}</span>
                            <span class="text-sm flex-1"><b>{{ $n }}</b> {{ $teks }}</span>
                            <span class="text-gray-300">›</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white rounded-xl border p-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold text-gray-800">Event Mendatang</h2>
                <a href="{{ $go('event.index') }}" class="text-xs border rounded px-2 py-1 hover:bg-gray-50">Lihat Semua →</a>
            </div>
            @if ($eventMendatang)
                <div class="flex items-center gap-4">
                    <div class="w-14 text-center rounded-lg bg-gold-50 text-gold-700 py-2 flex-shrink-0">
                        <p class="text-xl font-bold leading-none">{{ $eventMendatang->tanggal_mulai->format('d') }}</p>
                        <p class="text-xs">{{ $eventMendatang->tanggal_mulai->translatedFormat('M') }}</p>
                    </div>
                    <div class="text-sm min-w-0">
                        <p class="font-semibold truncate">{{ $eventMendatang->judul }}</p>
                        <p class="text-gray-500 text-xs truncate">📍 {{ $eventMendatang->lokasi }}</p>
                        <span class="inline-block mt-1 text-xs bg-gold-100 text-gold-700 rounded-full px-2 py-0.5">{{ $eventMendatang->peserta_count }} Peserta</span>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Belum ada event mendatang.</p>
            @endif
        </div>
    </div>
</div>
@endsection
