@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Peminjam')
@section('header-title', 'Riwayat Peminjaman')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    Riwayat Peminjaman
                </h2>

                <p class="text-gray-500 mt-1">
                    Lihat daftar alat yang sedang atau pernah kamu pinjam.
                </p>
            </div>

            <a href="{{ route('peminjam.katalog') }}"
               class="px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition text-center">

                + Ajukan Peminjaman

            </a>

        </div>

    </div>


    {{-- Daftar peminjaman --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 border-b">

                    <tr>

                        <th class="px-6 py-4 text-left font-semibold text-gray-700">
                            No
                        </th>

                        <th class="px-6 py-4 text-left font-semibold text-gray-700">
                            Alat
                        </th>

                        <th class="px-6 py-4 text-center font-semibold text-gray-700">
                            Jumlah
                        </th>

                        <th class="px-6 py-4 text-center font-semibold text-gray-700">
                            Tanggal Pinjam
                        </th>

                        <th class="px-6 py-4 text-center font-semibold text-gray-700">
                            Rencana Kembali
                        </th>

                        <th class="px-6 py-4 text-center font-semibold text-gray-700">
                            Tanggal Dikembalikan
                        </th>

                        <th class="px-6 py-4 text-center font-semibold text-gray-700">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse($peminjaman as $pinjam)

                        @foreach($pinjam->detailPinjam as $detail)

                            <tr class="hover:bg-gray-50">

                                {{-- Nomor --}}
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $loop->iteration }}
                                </td>


                                {{-- Nama alat --}}
                                <td class="px-6 py-4">

                                    <div class="font-semibold text-gray-800">
                                        {{ $detail->alat->nama_alat ?? '-' }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        Kategori:
                                        {{ $detail->alat->kategori->nama_kategori ?? '-' }}
                                    </div>

                                </td>


                                {{-- Jumlah --}}
                                <td class="px-6 py-4 text-center text-gray-700">
                                    {{ $detail->jumlah }}
                                </td>


                                {{-- Tanggal pinjam --}}
                                <td class="px-6 py-4 text-center text-gray-600">

                                    {{ $pinjam->tgl_pinjam
                                        ? \Carbon\Carbon::parse($pinjam->tgl_pinjam)->locale('id')->translatedFormat('d F Y, H:i')
                                        : '-' }}

                                </td>


                                {{-- Tanggal kembali --}}
                                <td class="px-6 py-4 text-center text-gray-600">

                                    {{ $pinjam->tgl_kembali_plan
                                        ? \Carbon\Carbon::parse($pinjam->tgl_kembali_plan)->locale('id')->translatedFormat('d F Y, H:i')
                                        : '-' }}

                                </td>

                                <td class="px-6 py-4 text-center text-gray-600">
                                    {{ $pinjam->pengembalian?->tgl_kembali
                                        ? \Carbon\Carbon::parse($pinjam->pengembalian->tgl_kembali)->locale('id')->translatedFormat('d F Y, H:i')
                                        : '-' }}
                                </td>


                                {{-- Status --}}
                                <td class="px-6 py-4 text-center">

                                    @if($pinjam->status === 'diajukan')

                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            Diajukan
                                        </span>

                                    @elseif($pinjam->status === 'disetujui')

                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            Disetujui
                                        </span>

                                    @elseif($pinjam->status === 'ditolak')

                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Ditolak
                                        </span>

                                    @elseif($pinjam->status === 'selesai')

                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            Selesai
                                        </span>

                                    @else

                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            {{ ucfirst($pinjam->status) }}
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    @empty

                        <tr>

                            <td colspan="7" class="px-6 py-12 text-center">

                                <div class="text-5xl mb-3">
                                    📋
                                </div>

                                <h3 class="text-lg font-semibold text-gray-700">
                                    Belum ada riwayat peminjaman
                                </h3>

                                <p class="text-gray-500 mt-1">
                                    Kamu belum pernah mengajukan peminjaman alat.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection