<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    /**
     * Menampilkan daftar pengajuan peminjaman.
     */
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjaman = Peminjaman::with([
            'user',
            'detailPinjams.alat'
        ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('petugas.peminjaman.index', compact(
            'peminjaman',
            'search'
        ));
    }

    /**
     * Menyetujui peminjaman.
     */
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjams')
                ->findOrFail($id);

            if ($peminjaman->status !== 'diajukan') {
                return redirect()->back()->with(
                    'error',
                    'Peminjaman tidak dapat disetujui karena status sudah berubah.'
                );
            }

            $peminjaman->update([
                'status' => 'dipinjam'
            ]);

            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);

                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();

            return redirect()->back()->with(
                'success',
                'Peminjaman disetujui dan stok alat dikurangi.'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(
                'error',
                'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    /**
     * Menampilkan daftar peminjaman yang dapat dikembalikan.
     */
    public function indexPengembalian(Request $request)
    {
        $pengembalian = Peminjaman::with([
            'user',
            'detailPinjams.alat',
            'pengembalian'
        ])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'petugas.pengembalian.index',
            compact('pengembalian')
        );
    }

    /**
     * Menampilkan form pengembalian.
     */
    public function createPengembalian($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detailPinjams.alat'
        ])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->findOrFail($id);

        $tglKembaliPlan = Carbon::parse(
            $peminjaman->tgl_kembali_plan
        );

        $tglSekarang = Carbon::now();

        $jumlahHariTerlambat = 0;
        $dendaKeterlambatan = 0;

        if ($tglSekarang->greaterThan($tglKembaliPlan)) {
            $jumlahHariTerlambat = $tglKembaliPlan
                ->copy()
                ->startOfDay()
                ->diffInDays($tglSekarang->copy()->startOfDay());

            $dendaKeterlambatan = $jumlahHariTerlambat * 5000;
        }

        return view('petugas.pengembalian.create', compact(
            'peminjaman',
            'jumlahHariTerlambat',
            'dendaKeterlambatan'
        ));
    }

    /**
     * Memproses pengembalian alat.
     */
    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjams')
                ->findOrFail($peminjamanId);

            if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
                return redirect()->back()->with(
                    'error',
                    'Peminjaman ini sudah tidak dapat diproses.'
                );
            }

            // Hitung denda keterlambatan otomatis
            $tglKembaliPlan = Carbon::parse(
                $peminjaman->tgl_kembali_plan
            );

            $tglSekarang = Carbon::now();

            $dendaKeterlambatan = 0;

            if ($tglSekarang->greaterThan($tglKembaliPlan)) {
                $jumlahHariTerlambat = $tglKembaliPlan
                    ->copy()
                    ->startOfDay()
                    ->diffInDays($tglSekarang->copy()->startOfDay());

                $dendaKeterlambatan = $jumlahHariTerlambat * 5000;
            }

            // Denda kerusakan yang diisi oleh petugas
            $dendaKerusakan = (int) ($request->denda ?? 0);

            // Total denda
            $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $totalDenda,
                'petugas_id' => auth()->id(),
            ]);

            // Ubah status menjadi dikembalikan
            $peminjaman->update([
                'status' => 'dikembalikan'
            ]);

            // Kembalikan stok alat
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);

                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();

            return redirect()
                ->route('petugas.pengembalian.index')
                ->with(
                    'success',
                    'Pengembalian berhasil dicatat. Total denda: Rp ' .
                    number_format($totalDenda, 0, ',', '.')
                );
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(
                'error',
                'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    /**
     * Menolak pengajuan peminjaman.
     */
    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            if ($peminjaman->status === 'diajukan') {
                $peminjaman->delete();

                return redirect()->back()->with(
                    'success',
                    'Pengajuan peminjaman berhasil ditolak.'
                );
            }

            return redirect()->back()->with(
                'error',
                'Status peminjaman sudah berubah.'
            );
        } catch (\Exception $e) {
            return redirect()->back()->with(
                'error',
                'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    /**
     * Menampilkan laporan peminjaman.
     */
    public function indexLaporan(Request $request)
    {
        $laporan = Peminjaman::with([
            'user',
            'detailPinjams.alat',
            'pengembalian',
        ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('petugas.laporan.index', [
            'peminjaman' => $laporan
        ]);
    }
}