@extends('layouts.app')

@section('title', 'Kelola Peminjaman - Petugas')
@section('header-title', 'Daftar Pengajuan & Transaksi Peminjaman')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                        <th class="py-3.5 px-4 font-semibold">Peminjam</th>
                        <th class="py-3.5 px-4 font-semibold">Tanggal Pinjam</th>
                        <th class="py-3.5 px-4 font-semibold">Rencana Kembali</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-4 font-semibold">Alat yang Dipinjam</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($peminjaman as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-medium text-slate-900">
                                {{ $item->user->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $item->tgl_pinjam }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $item->tgl_kembali_plan }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($item->status == 'diajukan')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60">
                                        Diajukan
                                    </span>
                                @elseif($item->status == 'dipinjam')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                        Dipinjam
                                    </span>
                                @elseif($item->status == 'selesai')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <ul class="list-disc list-inside space-y-1 text-xs text-slate-600">
                                    @foreach($item->detailPinjam as $detail)
                                        <li>
                                            <span class="font-medium text-slate-800">{{ $detail->alat->nama_alat ?? 'Alat' }}</span>
                                            ({{ $detail->jumlah }} pcs)
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->status == 'diajukan')
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('petugas.peminjaman.setujui', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Setujui peminjaman alat ini?')"
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition shadow-sm">
                                                Setujui
                                            </button>
                                        </form>

                                        <form action="{{ route('petugas.peminjaman.tolak', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Yakin ingin menolak pengajuan ini?')"
                                                class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition shadow-sm">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @elseif($item->status == 'dipinjam')
                                    <form action="{{ route('petugas.pengembalian.proses', $item->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="kondisi_kembali" value="Baik">
                                        <input type="hidden" name="denda" value="0">
                                        <button type="submit" onclick="return confirm('Proses pengembalian alat ini?')"
                                            class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition shadow-sm">
                                            Terima Kembali
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                Belum ada data peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $peminjaman->links() }}
        </div>
    </div>
@endsection