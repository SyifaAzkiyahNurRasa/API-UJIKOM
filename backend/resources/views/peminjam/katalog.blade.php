@extends('layouts.app')

@section('title', 'Daftar Alat - Panel Peminjam')
@section('header-title', 'Daftar Alat')

@section('content')

    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        {{-- Header tabel --}}
        <div class="p-5 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Daftar Alat
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Pilih alat yang ingin kamu pinjam.
                </p>
            </div>

        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-gray-600 font-semibold">
                            No
                        </th>

                        <th class="py-3 px-4 text-gray-600 font-semibold">
                            Gambar
                        </th>

                        <th class="py-3 px-4 text-gray-600 font-semibold">
                            Nama Alat
                        </th>

                        <th class="py-3 px-4 text-gray-600 font-semibold">
                            Kategori
                        </th>

                        <th class="py-3 px-4 text-gray-600 font-semibold">
                            Stok
                        </th>

                        <th class="py-3 px-4 text-gray-600 font-semibold">
                            Kondisi
                        </th>

                        <th class="py-3 px-4 text-gray-600 font-semibold text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($alat as $item)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="py-3 px-4 border-b">
                                {{ $loop->iteration }}
                            </td>

                            <td class="py-3 px-4 border-b">

                                @if($item->gambar)
                                    <img
                                        src="{{ asset('storage/' . preg_replace('#^(?:public/)?storage/#', '', $item->gambar)) }}"
                                        alt="{{ $item->nama_alat }}"
                                        class="w-14 h-14 object-cover rounded-lg border border-gray-200"
                                    >
                                @else
                                    <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs">
                                        Tidak ada
                                    </div>
                                @endif

                            </td>

                            <td class="py-3 px-4 border-b font-medium text-gray-800">
                                {{ $item->nama_alat }}
                            </td>

                            <td class="py-3 px-4 border-b text-gray-600">
                                {{ $item->kategori->nama_kategori ?? '-' }}
                            </td>

                            <td class="py-3 px-4 border-b">

                                @if($item->stok > 0)

                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                        {{ $item->stok }} tersedia
                                    </span>

                                @else

                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                        Habis
                                    </span>

                                @endif

                            </td>

                            <td class="py-3 px-4 border-b">

                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                    {{ ucfirst($item->status_kondisi ?? '-') }}
                                </span>

                            </td>

                            <td class="py-3 px-4 border-b text-center">

                                @if($item->stok > 0)

                                    <a
                                        href="{{ route('peminjam.alat.detail', $item->id) }}"
                                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs font-semibold transition"
                                    >
                                        Detail Alat
                                    </a>

                                @else

                                    <span class="text-xs text-gray-400">
                                        Stok habis
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                Belum ada alat yang tersedia.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection