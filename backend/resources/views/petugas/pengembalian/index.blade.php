@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Dashboard Petugas')
@section('header-title', 'Data & Transaksi Pengembalian Alat')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                        <th class="py-3.5 px-4 font-semibold">Peminjam</th>
                        <th class="py-3.5 px-4 font-semibold">Alat</th>
                        <th class="py-3.5 px-4 font-semibold">Tanggal Kembali</th>
                        <th class="py-3.5 px-4 font-semibold">Kondisi Alat</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($pengembalian as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-medium text-slate-900">
                                {{ $item->user->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-900">
                                @forelse($item->detailPinjam as $detail)
                                    <div>{{ $detail->alat->nama_alat ?? '-' }}</div>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $item->tgl_kembali ?? $item->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->kondisi_kembali == 'Baik' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-rose-50 text-rose-700 border border-rose-200/60' }}">
                                    {{ $item->kondisi_kembali ?? 'Baik' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                               @if($item->status === 'telat')
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">Terlambat</span>
                                @else
                                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800">Dipinjam</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                <a href="{{ route('admin.pengembalian.create', $item->id) }}" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">Proses Pengembalian</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-sm">
                                Belum ada riwayat pengembalian alat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $pengembalian->links() }}
        </div>
    </div>
@endsection