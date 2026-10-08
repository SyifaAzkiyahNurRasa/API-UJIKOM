@extends('layouts.app')

@section('title', 'Kelola Pengembalian - Panel Admin')
@section('header-title', 'Kelola Pengembalian')

@section('content')
    @if(session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 p-5">
            <h3 class="text-lg font-bold text-gray-800">Daftar Pengembalian Alat</h3>
            <p class="mt-1 text-sm text-gray-500">Kelola peminjaman yang masih berlangsung dan sudah melewati batas waktu.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-gray-100 text-sm uppercase tracking-wider text-gray-600">
                        <th class="border-b px-4 py-3">Peminjam</th>
                        <th class="border-b px-4 py-3">Alat</th>
                        <th class="border-b px-4 py-3">Tanggal Pinjam</th>
                        <th class="border-b px-4 py-3">Tanggal Kembali</th>
                        <th class="border-b px-4 py-3">Status</th>
                        <th class="border-b px-4 py-3 text-center">Aksi</th>
                    </tr>  
                </thead>
                <tbody class="text-sm text-gray-700">
                    @forelse($peminjaman as $item)
                        <tr class="align-top transition hover:bg-gray-50">
                            <td class="border-b px-4 py-4 font-medium text-gray-900">{{ $item->user->name ?? 'User Dihapus' }}</td>
                            <td class="border-b px-4 py-4">
                                <ul class="list-disc space-y-1 pl-5">
                                    @foreach($item->detailPinjams as $detail)
                                        <li>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} <span class="text-xs text-gray-500">({{ $detail->jumlah }} pcs)</span></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="border-b px-4 py-4">{{ \Carbon\Carbon::parse($item->tgl_pinjam)->locale('id')->translatedFormat('d F Y, H:i') }}</td>
                            <td class="border-b px-4 py-4 font-semibold">{{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->locale('id')->translatedFormat('d F Y, H:i') }}</td>
                            <td class="border-b px-4 py-4">
                                @if($item->status === 'telat')
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">Terlambat</span>
                                @else
                                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800">Dipinjam</span>
                                @endif
                            </td>
                            <td class="border-b px-4 py-4 text-center">
                                <a href="{{ route('admin.pengembalian.create', $item->id) }}" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">Proses Pengembalian</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada peminjaman yang menunggu pengembalian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection