@extends('layouts.app')

@section('title', 'Pengembalian Alat - Panel Petugas')
@section('header-title', 'Proses Pengembalian Alat')

@section('content')

<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    {{-- Pesan Error --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Judul --}}
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-800">
            Konfirmasi Pengembalian
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Periksa kondisi alat sebelum mengonfirmasi pengembalian.
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
                {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->locale('id')->translatedFormat('d F Y, H:i') }}
            </div>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Rencana Kembali
            </label>

            <div class="px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm">
                {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->locale('id')->translatedFormat('d F Y, H:i') }}
            </div>
        </div>

    </div>

    {{-- Alat --}}
    <div class="mb-4">

        <label class="block text-gray-700 text-sm font-semibold mb-2">
            Alat yang Dipinjam
        </label>

        <div class="border border-gray-200 rounded-lg overflow-hidden">

            @foreach($peminjaman->detailPinjams as $detail)

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

    {{-- Perhitungan Denda Keterlambatan --}}
    @php

        $tanggalRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);
        $tanggalSekarang = \Carbon\Carbon::now();
        $terlambat = $tanggalSekarang->gt($tanggalRencana);

        if ($terlambat) {
            $hariTerlambat = $tanggalRencana->copy()->startOfDay()->diffInDays($tanggalSekarang->copy()->startOfDay());
        } else {
            $hariTerlambat = 0;
        }

        $dendaPerHari = 5000;
        $totalDendaKeterlambatan = $hariTerlambat * $dendaPerHari;

    @endphp

    {{-- Informasi Pengembalian --}}
    <div class="mb-6">

        <label class="block text-gray-700 text-sm font-semibold mb-2">
            Informasi Pengembalian
        </label>

        <div class="border rounded-lg p-4
            {{ $terlambat
                ? 'bg-red-50 border-red-200'
                : 'bg-emerald-50 border-emerald-200' }}">

            {{-- Tanggal Pengembalian --}}
            <div class="flex justify-between text-sm mb-2">

                <span class="text-gray-600">
                    Tanggal Pengembalian
                </span>

                <span class="font-semibold text-gray-800">
                    {{ $tanggalSekarang->locale('id')->translatedFormat('d F Y, H:i') }}
                </span>

            </div>

            {{-- Keterlambatan --}}
            <div class="flex justify-between text-sm mb-2">

                <span class="text-gray-600">
                    Keterlambatan
                </span>

                <span class="font-semibold
                    {{ $terlambat ? 'text-red-600' : 'text-emerald-600' }}">

                    {{ $hariTerlambat }} hari

                </span>

            </div>

            {{-- Denda Keterlambatan --}}
            <div class="flex justify-between text-sm mb-2">

                <span class="text-gray-600">
                    Denda Keterlambatan
                </span>

                <span class="font-semibold text-gray-800">
                    Rp {{ number_format($totalDendaKeterlambatan, 0, ',', '.') }}
                </span>

            </div>

            {{-- Denda Kerusakan --}}
            <div class="flex justify-between text-sm">

                <span class="text-gray-600">
                    Denda Kerusakan
                </span>

                <span class="font-semibold text-gray-800">
                    Diisi manual oleh Petugas
                </span>

            </div>

            @if($terlambat)

                <div class="mt-3 text-xs text-red-700">
                    Waktu pengembalian sudah melewati batas.
                    Denda Rp 5.000 dihitung per hari kalender (saat ini {{ $hariTerlambat }} hari).
                </div>

            @else

                <div class="mt-3 text-xs text-emerald-700">
                    Pengembalian tepat waktu. Tidak ada denda keterlambatan.
                </div>

            @endif

        </div>

    </div>

    {{-- FORM PROSES PENGEMBALIAN --}}
    <form
        action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}"
        method="POST"
        onsubmit="return confirm('Yakin ingin memproses pengembalian alat ini?')">

        @csrf

        {{-- Route Petugas menggunakan PUT --}}
        @method('PUT')

        {{-- Kondisi Alat --}}
        <div class="mb-4">

            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Kondisi Saat Dikembalikan
            </label>

            <select
                name="kondisi_kembali"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">

                <option value="">
                    -- Pilih Kondisi --
                </option>

                <option value="Baik">
                    Baik
                </option>

                <option value="Rusak Ringan">
                    Rusak Ringan
                </option>

                <option value="Rusak Berat">
                    Rusak Berat
                </option>

                <option value="Tidak Lengkap">
                    Tidak Lengkap
                </option>

            </select>

        </div>

        {{-- Denda Kerusakan --}}
        <div class="mb-6">

            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Denda Kerusakan
            </label>

            <input
                type="number"
                name="denda_kerusakan"
                id="denda_kerusakan"
                min="0"
                value="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                placeholder="Masukkan denda kerusakan"
                oninput="hitungTotalDenda(this.value)"
            >

            <p class="text-xs text-gray-500 mt-1">
                Isi 0 jika tidak ada kerusakan.
            </p>

        </div>

        {{-- TOTAL DENDA --}}
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">

            <div class="flex justify-between items-center">

                <span class="text-gray-700 font-semibold">
                    Total Denda
                </span>

                <span
                    id="total_denda"
                    class="text-lg font-bold text-blue-600">

                    Rp {{ number_format($totalDendaKeterlambatan, 0, ',', '.') }}

                </span>

            </div>

        </div>

        {{-- BUTTON --}}
        <div class="flex justify-end space-x-2">

            <a
                href="{{ route('petugas.pengembalian.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">

                Batal

            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">

                Konfirmasi Pengembalian

            </button>

        </div>

    </form>

</div>

<script>
    function hitungTotalDenda(nilaiKerusakan) {

        const dendaKeterlambatan = {{ $totalDendaKeterlambatan }};

        const dendaKerusakan = parseInt(nilaiKerusakan) || 0;

        const total = dendaKeterlambatan + dendaKerusakan;

        document.getElementById('total_denda').textContent =
            'Rp ' + total.toLocaleString('id-ID');
    }
</script>

@endsection