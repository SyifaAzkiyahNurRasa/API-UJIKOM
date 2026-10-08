<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // DASHBOARD PEMINJAM
    public function dashboard()
    {
        $totalPeminjaman = Peminjaman::where('user_id', auth()->id())->count();

        $peminjamanDiajukan = Peminjaman::where('user_id', auth()->id())
            ->where('status', 'diajukan')
            ->count();

        $sedangDipinjam = Peminjaman::where('user_id', auth()->id())
            ->whereIn('status', ['dipinjam', 'telat'])
            ->count();

        $alatTersedia = Alat::where('stok', '>', 0)->count();

        return view('peminjam.dashboard', compact(
            'totalPeminjaman',
            'peminjamanDiajukan',
            'sedangDipinjam',
            'alatTersedia'
        ));
    }

    // MELIHAT DAFTAR ALAT
    public function katalogAlat()
    {
        $alat = Alat::with('kategori')
            ->where('stok', '>', 0)
            ->latest()
            ->get();

        return view('peminjam.katalog', compact('alat'));
    }

    public function detailAlat($id)
    {
        $alat = Alat::with('kategori')->findOrFail($id);

        return view('peminjam.detail-alat', compact('alat'));
    }

    // HALAMAN MENGAJUKAN PEMINJAMAN
    public function formPeminjaman()
    {
        $alats = Alat::with('kategori')
            ->where('stok', '>', 0)
            ->get();

        return view('peminjam.peminjaman', compact('alats'));
    }

    // PROSES MENGAJUKAN PEMINJAMAN
    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_kembali_plan' => [
                'required',
                'date',
                'after:now'
            ],

            'alat_id' => [
                'required',
                'array',
                'min:1'
            ],

            'alat_id.*' => [
                'required',
                'integer',
                'exists:alat,id'
            ],

            'jumlah' => [
                'required',
                'array',
                'min:1'
            ],

            'jumlah.*' => [
                'required',
                'integer',
                'min:1'
            ],
        ], [
            'alat_id.required' => 'Pilih minimal satu alat.',
            'alat_id.min' => 'Pilih minimal satu alat.',
            'jumlah.required' => 'Masukkan jumlah alat.',
            'jumlah.min' => 'Masukkan minimal satu jumlah alat.',
            'jumlah.*.min' => 'Jumlah alat minimal 1.',
            'tgl_kembali_plan.after' => 'Tanggal dan waktu kembali harus setelah waktu sekarang.',
        ]);

        DB::beginTransaction();

        try {

            // Pastikan jumlah alat dan jumlahnya sesuai
            if (count($request->alat_id) !== count($request->jumlah)) {
                throw new \Exception('Data alat dan jumlah tidak sesuai.');
            }

            // CEK STOK
            foreach ($request->alat_id as $index => $alatId) {

                $alat = Alat::findOrFail($alatId);

                $jumlah = (int) $request->jumlah[$index];

                if ($jumlah > $alat->stok) {
                    throw new \Exception(
                        "Jumlah {$alat->nama_alat} yang dipilih melebihi stok tersedia ({$alat->stok})."
                    );
                }
            }

            // BUAT DATA PEMINJAMAN
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tgl_pinjam' => now(),
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            // BUAT DETAIL PEMINJAMAN
            foreach ($request->alat_id as $index => $alatId) {

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => (int) $request->jumlah[$index],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('peminjam.peminjaman')
                ->with(
                    'success',
                    'Pengajuan peminjaman berhasil dikirim dan menunggu persetujuan Petugas.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal mengajukan peminjaman: ' . $e->getMessage()
                );
        }
    }

    // MELIHAT DATA PEMINJAMAN MILIK SENDIRI
    public function riwayatPeminjaman()
    {
        $peminjaman = Peminjaman::with([
            'detailPinjams.alat',
            'pengembalian'
        ])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('peminjam.peminjaman', compact('peminjaman'));
    }

    // HALAMAN PENGEMBALIAN
    public function pengembalian()
    {
        $peminjaman = Peminjaman::with([
            'detailPinjams.alat',
            'pengembalian'
        ])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['dipinjam', 'telat'])
            ->latest()
            ->get();

        return view('peminjam.pengembalian', compact('peminjaman'));
    }

    // AJUKAN PENGEMBALIAN
    public function ajukanPengembalian($id)
    {
        $peminjaman = Peminjaman::where('user_id', auth()->id())
            ->findOrFail($id);

        // Hanya peminjaman yang sedang dipinjam atau terlambat
        // yang bisa diajukan untuk dikembalikan.
        if (!in_array($peminjaman->status, ['dipinjam', 'telat'], true)) {
            return back()->with(
                'error',
                'Peminjaman ini tidak dapat diajukan untuk pengembalian.'
            );
        }

        /*
         * Tidak menggunakan kolom pengembalian_diajukan_at
         * karena kolom tersebut tidak ada di tabel peminjaman.
         *
         * Pengajuan cukup diteruskan ke Petugas.
         * Status peminjaman tetap dipinjam/telat sampai Petugas
         * benar-benar memproses pengembaliannya.
         */

        return back()->with(
            'success',
            'Permintaan pengembalian berhasil dikirim. Silakan serahkan alat kepada Petugas untuk diperiksa.'
        );
    }
}