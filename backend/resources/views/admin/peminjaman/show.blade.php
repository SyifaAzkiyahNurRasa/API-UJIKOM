@extends('layouts.app')

@section('title', 'Detail Peminjaman - Panel Admin')
@section('header-title', 'Detail Transaksi Peminjaman')

@section('content')
    <div class="max-w-3xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
            <div>
                <p class="text-sm text-gray-500">Peminjam</p>
                <p class="font-semibold text-gray-900">{{ $peminjaman->user->name ?? 'User Dihapus' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-semibold text-gray-900">{{ ucfirst($peminjaman->status) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Pinjam</p>
                <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->locale('id')->translatedFormat('d F Y, H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Rencana Kembali</p>
                <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->locale('id')->translatedFormat('d F Y, H:i') }}</p>
            </div>
            @if($peminjaman->pengembalian?->tgl_kembali)
                <div>
                    <p class="text-sm text-gray-500">Tanggal Dikembalikan</p>
                    <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($peminjaman->pengembalian->tgl_kembali)->locale('id')->translatedFormat('d F Y, H:i') }}</p>
                </div>
            @endif
        </div>

        <h3 class="text-lg font-bold text-gray-800 mb-3">Alat yang Dipinjam</h3>
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm">
                        <th class="py-3 px-4 border-b">Nama Alat</th>
                        <th class="py-3 px-4 border-b">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjaman->detailPinjams as $detail)
                        <tr>
                            <td class="py-3 px-4 border-b">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</td>
                            <td class="py-3 px-4 border-b">{{ $detail->jumlah }} pcs</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="py-4 text-center text-gray-500">Belum ada alat pada transaksi ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('admin.peminjaman.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Kembali</a>
        </div>
    </div>
@endsection
