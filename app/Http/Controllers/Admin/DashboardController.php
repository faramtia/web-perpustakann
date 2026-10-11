<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Buku;
use App\Models\Denda;
use App\Models\DetailPeminjaman;
use App\Models\Event;
use App\Models\Feedback;
use App\Models\Peminjaman;
use App\Models\Reservasi;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // Grafik peminjaman: Senin sampai Minggu pada minggu ini.
        $awal = now()->startOfWeek();
        $grafik = [];
        foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $i => $hari) {
            $grafik[] = [
                'hari' => $hari,
                'jumlah' => Peminjaman::whereDate('tanggal_pinjam', $awal->copy()->addDays($i))->count(),
            ];
        }

        $stokTersedia = (int) Buku::sum('stok');
        $sedangDipinjam = (int) DetailPeminjaman::whereHas('peminjaman', fn ($q) => $q->where('status', Peminjaman::DIPINJAM))->sum('jumlah');

        return view('admin.dashboard', [
            'totalBuku' => $stokTersedia,
            'judulBuku' => Buku::count(),
            'totalAnggota' => User::whereHas('jenisUser', fn ($q) => $q->whereIn('nama_role', ['mahasiswa', 'dosen']))->count(),
            'peminjamanAktif' => Peminjaman::where('status', Peminjaman::DIPINJAM)->count(),
            'terlambat' => Peminjaman::where('status', Peminjaman::DIPINJAM)->whereDate('tanggal_jatuh_tempo', '<', today())->count(),
            'totalDenda' => Schema::hasTable('denda')
                ? (int) Denda::where('status_bayar', Denda::BELUM_LUNAS)->sum('jumlah_denda')
                : 0,
            'reservasiMenunggu' => Reservasi::where('status', Reservasi::MENUNGGU)->count(),
            'feedbackBelumDibalas' => Feedback::where('status', Feedback::BELUM_DIBALAS)->count(),
            'kunjunganHariIni' => Absensi::whereDate('tanggal', today())->count(),
            'grafik' => $grafik,
            'stokTersedia' => $stokTersedia,
            'sedangDipinjam' => $sedangDipinjam,
            'eventMendatang' => Event::withCount('peserta')
                ->whereDate('tanggal_mulai', '>=', today())
                ->orderBy('tanggal_mulai')
                ->first(),
            'peminjamanTerbaru' => Peminjaman::with(['user.jenisUser', 'detail.buku'])
                ->latest('id')
                ->take(5)
                ->get(),
        ]);
    }
}
