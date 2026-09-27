@extends('layouts.app')

@section('title', 'Pengembalian Alat - Panel Admin')
@section('header-title', 'Proses Pengembalian Alat')

@section('content')

<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    {{-- Pesan Error --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-800">
            Konfirmasi Pengembalian
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Pastikan data alat dan peminjam sudah sesuai sebelum diproses.
        </p>
    </div>

    {{-- Data Peminjam --}}
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-semibold mb-2">
            Peminjam
        </label>

        <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg">
            <div class="font-semibold text-gray-800">
                {{ $peminjaman->user->name ?? 'User Dihapus' }}
            </div>

            @if($peminjaman->user)
                <div class="text-xs text-gray-500">
                    {{ $peminjaman->user->email }}
                </div>
            @endif
        </div>
    </div>

    {{-- Tanggal --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

        <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Tanggal Pinjam
            </label>

            <div class="px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm">
                {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d-m-Y') }}
            </div>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Rencana Kembali
            </label>

            <div class="px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm">
                {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d-m-Y') }}
            </div>
        </div>

    </div>

    {{-- Alat --}}
    <div class="mb-4">

        <label class="block text-gray-700 text-sm font-semibold mb-2">
            Alat yang Dipinjam
        </label>

        <div class="border border-gray-200 rounded-lg overflow-hidden">

            @foreach($peminjaman->detailPinjam as $detail)

                <div class="flex justify-between items-center px-4 py-3 border-b last:border-b-0">

                    <div>
                        <div class="font-semibold text-gray-800">
                            {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                        </div>

                        <div class="text-xs text-gray-500">
                            Jumlah dipinjam: {{ $detail->jumlah }} pcs
                        </div>
                    </div>

                    <div class="text-sm font-semibold text-gray-700">
                        {{ $detail->jumlah }} pcs
                    </div>

                </div>

            @endforeach

        </div>

    </div>

    {{-- Perhitungan Denda --}}
    @php

        $tanggalRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);
        $tanggalSekarang = \Carbon\Carbon::today();

        if ($tanggalSekarang->gt($tanggalRencana)) {
            $hariTerlambat = $tanggalRencana->diffInDays($tanggalSekarang);
        } else {
            $hariTerlambat = 0;
        }

        $dendaPerHari = 5000;
        $dendaKeterlambatan = $hariTerlambat * $dendaPerHari;

    @endphp

    <div class="mb-6">

        <label class="block text-gray-700 text-sm font-semibold mb-2">
            Informasi Pengembalian
        </label>

        <div class="border rounded-lg p-4
            {{ $hariTerlambat > 0
                ? 'bg-red-50 border-red-200'
                : 'bg-emerald-50 border-emerald-200' }}">

            {{-- Tanggal Pengembalian --}}
            <div class="flex justify-between text-sm mb-2">

                <span class="text-gray-600">
                    Tanggal Pengembalian
                </span>

                <span class="font-semibold text-gray-800">
                    {{ $tanggalSekarang->format('d-m-Y') }}
                </span>

            </div>

            {{-- Keterlambatan --}}
            <div class="flex justify-between text-sm mb-2">

                <span class="text-gray-600">
                    Keterlambatan
                </span>

                <span class="font-semibold
                    {{ $hariTerlambat > 0 ? 'text-red-600' : 'text-emerald-600' }}">

                    {{ $hariTerlambat }} hari

                </span>

            </div>

            {{-- Denda Keterlambatan --}}
            <div class="flex justify-between text-sm mb-4">

                <span class="text-gray-600">
                    Denda Keterlambatan
                </span>

                <span class="font-bold text-red-600">
                    Rp {{ number_format($dendaKeterlambatan, 0, ',', '.') }}
                </span>

            </div>

            @if($hariTerlambat > 0)

                <div class="mb-4 text-xs text-red-700">
                    Terlambat {{ $hariTerlambat }} hari.
                    Denda keterlambatan Rp 5.000 per hari.
                </div>

            @else

                <div class="mb-4 text-xs text-emerald-700">
                    Pengembalian tepat waktu. Tidak ada denda keterlambatan.
                </div>

            @endif

            {{-- Kondisi Saat Dikembalikan --}}
            <div class="mb-4">

                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Kondisi Saat Dikembalikan
                </label>

                <select
                    name="kondisi_kembali"
                    id="kondisi_kembali"
                    form="form-pengembalian"
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>

                    <option value="">-- Pilih Kondisi --</option>
                    <option value="baik">Baik</option>
                    <option value="rusak ringan">Rusak Ringan</option>
                    <option value="rusak berat">Rusak Berat</option>
                    <option value="tidak lengkap">Tidak Lengkap</option>

                </select>

            </div>

            {{-- Denda Kerusakan --}}
            <div class="mb-4">

                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Denda Kerusakan
                </label>

                <div class="relative">

                    <span class="absolute left-3 top-2 text-gray-500 text-sm">
                        Rp
                    </span>

                    <input
                        type="number"
                        name="denda_kerusakan"
                        id="denda_kerusakan"
                        form="form-pengembalian"
                        value="0"
                        min="0"
                        step="1"
                        class="w-full pl-10 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan denda kerusakan">

                </div>

                <p class="mt-1 text-xs text-gray-500">
                    Isi manual sesuai kondisi alat saat dikembalikan.
                </p>

            </div>

            {{-- Total Denda --}}
            <div class="border-t border-gray-200 pt-3">

                <div class="flex justify-between items-center">

                    <span class="text-gray-700 font-semibold">
                        Total Denda
                    </span>

                    <span
                        id="total_denda_tampilan"
                        class="font-bold text-lg text-red-600">

                        Rp {{ number_format($dendaKeterlambatan, 0, ',', '.') }}

                    </span>

                </div>

            </div>

            {{-- Nilai total yang dikirim ke controller --}}
            <input
                type="hidden"
                name="denda"
                id="denda"
                form="form-pengembalian"
                value="{{ $dendaKeterlambatan }}">

        </div>

    </div>

    {{-- Tombol --}}
    <div class="flex justify-end space-x-2">

        <a href="{{ route('admin.pengembalian.index') }}"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">

            Batal

        </a>

        <form
            id="form-pengembalian"
            action="{{ route('admin.pengembalian.kembalikan', $peminjaman->id) }}"
            method="POST"
            onsubmit="return confirm('Yakin ingin memproses pengembalian alat ini?')">

            @csrf
            @method('PUT')

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">

                Konfirmasi Pengembalian

            </button>

        </form>

    </div>

</div>

<script>
    const dendaKeterlambatan = {{ $dendaKeterlambatan }};

    const inputDendaKerusakan = document.getElementById('denda_kerusakan');
    const inputDenda = document.getElementById('denda');
    const tampilanTotalDenda = document.getElementById('total_denda_tampilan');

    function hitungTotalDenda() {
        const dendaKerusakan = parseInt(inputDendaKerusakan.value) || 0;

        const totalDenda = dendaKeterlambatan + dendaKerusakan;

        inputDenda.value = totalDenda;

        tampilanTotalDenda.textContent =
            'Rp ' + totalDenda.toLocaleString('id-ID');
    }

    inputDendaKerusakan.addEventListener('input', hitungTotalDenda);
</script>

@endsection