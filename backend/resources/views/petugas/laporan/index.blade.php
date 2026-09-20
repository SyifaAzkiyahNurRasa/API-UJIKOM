@extends('layouts.app')

@section('title', 'Cetak Laporan - Dashboard Petugas')
@section('header-title', 'Laporan Peminjaman Alat')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Rekap Data Peminjaman</h3>
                <p class="text-slate-500 text-sm">Cetak atau ekspor data riwayat peminjaman laboratorium.</p>
            </div>
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                Cetak Laporan
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                        <th class="py-3.5 px-4 font-semibold">No</th>
                        <th class="py-3.5 px-4 font-semibold">Peminjam</th>
                        <th class="py-3.5 px-4 font-semibold">Tgl Pinjam</th>
                        <th class="py-3.5 px-4 font-semibold">Rencana Kembali</th>
                        <th class="py-3.5 px-4 font-semibold">Alat</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($peminjaman as $index => $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-medium text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-medium text-slate-900">{{ $item->user->name ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $item->tgl_pinjam }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $item->tgl_kembali_plan }}</td>
                            <td class="py-3.5 px-4">
                                <ul class="list-disc list-inside text-xs text-slate-600">
                                    @foreach($item->detailPinjam as $detail)
                                        <li>{{ $detail->alat->nama_alat ?? 'Alat' }} ({{ $detail->jumlah }} pcs)</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                Belum ada data untuk laporan.
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