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
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-4 font-semibold">Kondisi Alat</th> 
                        <th class="py-3.5 px-4 font-semibold">Aksi</th> 
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($pengembalian as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-medium text-slate-900">
                                {{ $item->user->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                @foreach($item->detailPinjams as $detail)
                                    <div>
                                        {{ $detail->alat->nama_alat ?? '-' }}
                                        <span class="text-xs text-slate-400">
                                            ({{ $detail->jumlah }} pcs)
                                        </span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->locale('id')->translatedFormat('d F Y, H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                               @if($item->pengembalian_diajukan_at)
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                        Permintaan Pengembalian
                                    </span>
                                @elseif($item->status === 'diajukan')
                                    <span class="rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-800">
                                        Diajukan
                                    </span>
                                @elseif($item->status === 'dipinjam')
                                    @if(\Carbon\Carbon::parse($item->tgl_kembali_plan)->isPast())
                                        <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">
                                            Terlambat
                                        </span>
                                    @else
                                        <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800">
                                            Dipinjam
                                        </span>
                                    @endif
                                @elseif($item->status === 'telat')
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">
                                        Terlambat
                                    </span>
                                @elseif($item->status === 'dikembalikan')
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                        Dikembalikan
                                    </span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-800">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                    {{ $item->kondisi_kembali ?? 'Baik' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                @if($item->status === 'dipinjam' || $item->status === 'telat')
                                    <a href="{{ route('petugas.pengembalian.create', $item->id) }}" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                        Proses Pengembalian
                                    </a>
                                @elseif($item->status === 'dikembalikan')
                                    <span class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-500">
                                        Selesai
                                    </span>
                                @elseif($item->status === 'diajukan')
                                    <span class="rounded-lg bg-yellow-50 px-3 py-2 text-xs font-semibold text-yellow-700">
                                        Menunggu Persetujuan
                                    </span>
                                @endif
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
    </div>
@endsection