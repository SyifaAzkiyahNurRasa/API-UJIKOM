@extends('layouts.app')

@section('title', 'Mengajukan Peminjaman - Panel Peminjam')
@section('header-title', 'Mengajukan Peminjaman')

@section('content')

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Form Pengajuan Peminjaman
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Pilih alat yang ingin dipinjam dan tentukan tanggal pengembaliannya.
        </p>
    </div>

    @if(session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <h3 class="font-semibold text-slate-800">
                    Data Peminjaman
                </h3>
            </div>

            <div class="p-6">

                {{-- TANGGAL KEMBALI --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Rencana Tanggal Kembali
                    </label>

                    <input
                        type="datetime-local"
                        name="tgl_kembali_plan"
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        value="{{ old('tgl_kembali_plan') }}"
                        class="w-full md:w-1/2 border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required
                    >
                </div>

                {{-- PILIH ALAT --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-3">
                        Pilih Alat
                    </label>

                    <div class="space-y-3">

                        @forelse($alats as $alat)

                            <div class="border border-slate-200 rounded-xl p-4 hover:bg-slate-50">

                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                                    {{-- INFORMASI ALAT --}}
                                    <div class="flex items-center gap-4">

                                        @if($alat->gambar)
                                            <img
                                                src="{{ asset('storage/' . preg_replace('#^(?:public/)?storage/#', '', $alat->gambar)) }}"
                                                alt="{{ $alat->nama_alat }}"
                                                class="w-16 h-16 object-cover rounded-lg border border-slate-200"
                                            >
                                        @else
                                            <div class="w-16 h-16 rounded-lg bg-slate-100 flex items-center justify-center text-xs text-slate-400">
                                                Tidak ada
                                            </div>
                                        @endif

                                        <div>
                                            <h4 class="font-semibold text-slate-800">
                                                {{ $alat->nama_alat }}
                                            </h4>

                                            <p class="text-sm text-slate-500">
                                                {{ $alat->kategori->nama_kategori ?? '-' }}
                                            </p>

                                            <p class="text-xs text-slate-500 mt-1">
                                                Stok tersedia: {{ $alat->stok }}
                                            </p>
                                        </div>

                                    </div>

                                    {{-- PILIH DAN JUMLAH --}}
                                    <div class="flex items-center gap-3">

                                        <input
                                            type="checkbox"
                                            name="alat_id[]"
                                            value="{{ $alat->id }}"
                                            class="alat-checkbox w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
                                            data-target="jumlah-{{ $alat->id }}"
                                        >

                                        <label class="text-sm text-slate-600">
                                            Pilih
                                        </label>

                                        <input
                                            type="number"
                                            id="jumlah-{{ $alat->id }}"
                                            name="jumlah[]"
                                            min="1"
                                            max="{{ $alat->stok }}"
                                            value="1"
                                            disabled
                                            class="jumlah-input w-20 border border-slate-300 rounded-lg px-3 py-2 text-center bg-slate-100"
                                        >

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="text-center py-10 text-slate-500">
                                Tidak ada alat yang tersedia untuk dipinjam.
                            </div>

                        @endforelse

                    </div>
                </div>

            </div>

            {{-- BUTTON --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end">

                <button
                    type="submit"
                    class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition"
                >
                    Ajukan Peminjaman
                </button>

            </div>

        </div>

    </form>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const checkboxes = document.querySelectorAll('.alat-checkbox');

            checkboxes.forEach(function (checkbox) {

                checkbox.addEventListener('change', function () {

                    const jumlahInput = document.getElementById(
                        this.dataset.target
                    );

                    if (this.checked) {

                        jumlahInput.disabled = false;
                        jumlahInput.classList.remove('bg-slate-100');

                    } else {

                        jumlahInput.disabled = true;
                        jumlahInput.value = 1;
                        jumlahInput.classList.add('bg-slate-100');

                    }

                });

            });

        });
    </script>

@endsection