@extends('layouts.app')

@section('title', 'Detail Alat - Panel Peminjam')
@section('header-title', 'Detail Alat')

@section('content')
    <div class="max-w-3xl bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            @if($alat->gambar)
                <img
                    src="{{ asset('storage/' . preg_replace('#^(?:public/)?storage/#', '', $alat->gambar)) }}"
                    alt="{{ $alat->nama_alat }}"
                    class="w-full max-h-96 object-contain rounded-lg border border-gray-200 mb-6"
                >
            @endif

            <h2 class="text-2xl font-bold text-gray-800">{{ $alat->nama_alat }}</h2>

            <dl class="mt-5 space-y-4">
                <div>
                    <dt class="text-sm text-gray-500">Kategori</dt>
                    <dd class="font-medium text-gray-800">{{ $alat->kategori->nama_kategori ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Kondisi</dt>
                    <dd class="font-medium text-gray-800">{{ ucfirst($alat->status_kondisi ?? '-') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Stok tersedia</dt>
                    <dd class="font-medium text-gray-800">{{ $alat->stok }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Deskripsi</dt>
                    <dd class="text-gray-800 whitespace-pre-line">{{ $alat->deskripsi ?: 'Tidak ada deskripsi.' }}</dd>
                </div>
            </dl>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between gap-3">
            <a href="{{ route('peminjam.katalog') }}"
               class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                Kembali ke Daftar Alat
            </a>

            @if($alat->stok > 0)
                <a href="{{ route('peminjam.peminjaman') }}"
                   class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    Ajukan Peminjaman
                </a>
            @endif
        </div>
    </div>
@endsection
