@extends('layouts.app')

@section('title', 'Pengembalian Alat')
@section('header-title', 'Pengembalian Alat')

@section('content')

<div class="space-y-6">

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-800">
            Daftar Alat yang Sedang Dipinjam
        </h2>

        <p class="text-gray-500 mt-1">
            Berikut adalah alat yang masih kamu pinjam dan belum dikembalikan.
        </p>
    </div>

    {{-- DATA PEMINJAMAN --}}
    @if($peminjaman->count() > 0)

        @foreach($peminjaman as $item)

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- HEADER PEMINJAMAN --}}
                <div class="p-5 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                    <div>
                        <h3 class="font-bold text-gray-800">
                            Peminjaman #{{ $item->id }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Tanggal Pinjam:
                            {{ \Carbon\Carbon::parse($item->tgl_pinjam)->locale('id')->translatedFormat('d F Y, H:i') }}
                        </p>

                        <p class="text-sm text-gray-500">
                            Rencana Kembali:
                            {{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->locale('id')->translatedFormat('d F Y, H:i') }}
                        </p>
                    </div>

                    {{-- STATUS --}}
                    @if($item->status === 'telat')

                        <span class="inline-flex w-fit px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                            Terlambat
                        </span>

                    @else

                        <span class="inline-flex w-fit px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-700">
                            Sedang Dipinjam
                        </span>

                    @endif

                </div>

                {{-- DAFTAR ALAT --}}
                <div class="p-5">

                    <h4 class="font-semibold text-gray-800 mb-4">
                        Alat yang Dipinjam
                    </h4>

                    <div class="space-y-3">

                        @foreach($item->detailPinjams as $detail)

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">

                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ $detail->alat->nama_alat ?? 'Nama alat tidak tersedia' }}
                                    </p>

                                    @if(isset($detail->alat->kategori))
                                        <p class="text-sm text-gray-500">
                                            Kategori:
                                            {{ $detail->alat->kategori->nama_kategori ?? '-' }}
                                        </p>
                                    @endif
                                </div>

                                <div class="text-right">
                                    <p class="text-sm text-gray-500">
                                        Jumlah
                                    </p>

                                    <p class="font-bold text-gray-800">
                                        {{ $detail->jumlah }}
                                    </p>
                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

                {{-- INFORMASI --}}
                <div class="px-5 pb-5">

                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">

                        <p class="text-sm text-yellow-800">
                            <strong>Informasi:</strong>
                            Serahkan alat kepada Petugas untuk diperiksa dan dicatat.
                        </p>

                    </div>

                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        @if($item->pengembalian_diajukan_at)
                            <p class="text-sm font-medium text-amber-700">
                                Permintaan pengembalian sudah diajukan. Silakan serahkan alat kepada Petugas.
                            </p>
                        @else
                            <p class="text-sm text-gray-500">
                                Ajukan pengembalian sebelum menyerahkan alat kepada Petugas.
                            </p>

                            <form action="{{ route('peminjam.pengembalian.ajukan', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                                    Ajukan Pengembalian
                                </button>
                            </form>
                        @endif
                    </div>

                </div>

            </div>

        @endforeach

    @else

        {{-- BELUM ADA PEMINJAMAN --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-10 text-center">

            <div class="text-gray-400 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-16 h-16 mx-auto"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>

            <h3 class="text-lg font-semibold text-gray-700">
                Tidak Ada Alat yang Sedang Dipinjam
            </h3>

            <p class="text-gray-500 mt-2">
                Saat ini kamu tidak memiliki peminjaman yang harus dikembalikan.
            </p>

            <a href="{{ route('peminjam.katalog') }}"
               class="inline-block mt-5 px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Lihat Katalog Alat
            </a>

        </div>

    @endif

</div>

@endsection