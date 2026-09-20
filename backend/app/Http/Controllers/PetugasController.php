<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // 1. Menampilkan daftar pengajuan peminjaman (Status: diajukan)
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Mengirim $peminjamans dan $peminjaman agar View Blade tidak error
        return view('petugas.peminjaman.index', [
            'peminjamans' => $peminjamans,
            'peminjaman'  => $peminjamans,
            'search'      => $search
        ]);
    }

    // 2. Menyetujui peminjaman (Ubah status ke dipinjam & kurangi stok alat)
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();

        try {
            // Diubah dari detailPinjams ke detailPinjam
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // 3. Menolak peminjaman
    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            
            if ($peminjaman->status === 'diajukan') {
                $peminjaman->delete();
                return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
            }

            return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // 4. Menampilkan daftar alat yang sedang dipinjam (Status: dipinjam) untuk dipantau/dikembalikan
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'dipinjam')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Mengirimkan $peminjamans, $pengembalian, dan $peminjaman agar View Blade aman
        return view('petugas.pengembalian.index', [
            'peminjamans'  => $peminjamans,
            'pengembalian' => $peminjamans,
            'peminjaman'   => $peminjamans,
            'search'       => $search
        ]);
    }

    // 5. Memproses pengembalian alat
    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'nullable|string',
            'denda'           => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            // Diubah dari detailPinjams ke detailPinjam
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($peminjamanId);

            if ($peminjaman->status !== 'dipinjam') {
                return redirect()->back()->with('error', 'Status peminjaman ini tidak valid untuk dikembalikan.');
            }

            // Simpan riwayat data pengembalian
            Pengembalian::create([
                'peminjaman_id'   => $peminjaman->id,
                'tgl_kembali'     => now(),
                'kondisi_kembali' => $request->kondisi_kembali ?? 'Baik',
                'denda'           => $request->denda ?? 0,
                'petugas_id'      => auth()->id(),
            ]);

            // Update status peminjaman jadi selesai
            $peminjaman->update(['status' => 'selesai']);

            // Kembalikan stok alat ke inventaris
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // 6. Menampilkan halaman laporan
    public function indexLaporan()
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->latest()
            ->paginate(10);

        // Mengirimkan variabel dalam berbagai format penamaan agar Blade View tidak error
        return view('petugas.laporan.index', [
            'peminjamans' => $peminjamans,
            'peminjaman'  => $peminjamans,
            'laporan'     => $peminjamans,
            'laporans'    => $peminjamans,
        ]);
    }
}