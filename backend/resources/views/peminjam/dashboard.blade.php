@extends('layouts.app')

@section('title', 'Dashboard Peminjam')
@section('header-title', 'Dashboard Peminjam')

@section('content')

<div class="space-y-6">

    {{-- SELAMAT DATANG --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Selamat Datang, {{ auth()->user()->name }}!
        </h2>

        <p class="text-gray-500 mt-2">
            Kelola peminjaman alat laboratorium kamu melalui halaman ini.
        </p>
    </div>

    {{-- STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

        {{-- Total Peminjaman --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">
                        Total Peminjaman
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $totalPeminjaman }}
                    </p>
                </div>

                <div class="bg-blue-100 text-blue-600 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Menunggu Persetujuan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">
                        Menunggu Persetujuan
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $peminjamanDiajukan }}
                    </p>
                </div>

                <div class="bg-yellow-100 text-yellow-600 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Sedang Dipinjam --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">
                        Sedang Dipinjam
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $sedangDipinjam }}
                    </p>
                </div>

                <div class="bg-green-100 text-green-600 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Alat Tersedia --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">
                        Alat Tersedia
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $alatTersedia }}
                    </p>
                </div>

                <div class="bg-purple-100 text-purple-600 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

@endsection