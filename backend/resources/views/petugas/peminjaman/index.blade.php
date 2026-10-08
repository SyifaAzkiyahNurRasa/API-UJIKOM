@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman - Petugas')
@section('header-title', 'Daftar Pengajuan Peminjaman Alat')

@section('content')

    {{-- Pesan Sukses --}}
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Pesan Error --}}
    @if(session('error'))
        <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        {{-- Header --}}
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">

            <h3 class="text-lg font-bold text-gray-800">
                Menunggu Verifikasi Persetujuan
            </h3>

            <form
                action="{{ route('petugas.peminjaman.index') }}"
                method="GET"
                class="flex w-full md:w-80">

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama peminjam..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                <button
                    type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">

                    Cari

                </button>

            </form>

        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse">

                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">

                        <th class="py-3 px-4 border-b">
                            Peminjam
                        </th>

                        <th class="py-3 px-4 border-b">
                            Tgl Pinjam
                        </th>

                        <th class="py-3 px-4 border-b">
                            Rencana Kembali
                        </th>

                        <th class="py-3 px-4 border-b">
                            Status
                        </th>

                        <th class="py-3 px-4 border-b">
                            Alat yang Dipinjam
                        </th>

                        <th class="py-3 px-4 border-b text-center">
                            Aksi
                        </th>

                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">

                    @forelse($peminjaman as $item)

                        <tr class="hover:bg-gray-50 transition">

                            {{-- Peminjam --}}
                            <td class="py-3.5 px-4 border-b font-medium text-gray-900">
                                {{ $item->user->name ?? '-' }}
                            </td>

                            {{-- Tanggal Pinjam --}}
                            <td class="py-3.5 px-4 border-b text-xs text-gray-600">

                                <span class="block">
                                    {{ \Carbon\Carbon::parse($item->tgl_pinjam)->locale('id')->translatedFormat('d F Y, H:i') }}
                                </span>

                            </td>

                            {{-- Rencana Kembali --}}
                            <td class="py-3.5 px-4 border-b text-xs text-gray-600">

                                <span class="block">
                                    {{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->locale('id')->translatedFormat('d F Y, H:i') }}
                                </span>

                            </td>

                            {{-- Status --}}
                            <td class="py-3.5 px-4 border-b">

                                @php
                                    $statusClass = match ($item->status) {
                                        'diajukan' => 'bg-amber-100 text-amber-800',
                                        'dipinjam' => 'bg-blue-100 text-blue-800',
                                        'dikembalikan' => 'bg-emerald-100 text-emerald-800',
                                        'telat' => 'bg-rose-100 text-rose-800',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <span
                                    class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">

                                    {{ ucfirst($item->status) }}

                                </span>

                            </td>

                            {{-- Alat --}}
                            <td class="py-3.5 px-4 border-b">

                                <ul class="list-disc list-inside space-y-1 text-xs text-gray-600">

                                    @foreach($item->detailPinjams as $detail)

                                        <li>

                                            <span class="font-medium text-gray-800">
                                                {{ $detail->alat->nama_alat ?? 'Alat' }}
                                            </span>

                                            ({{ $detail->jumlah }} pcs)

                                        </li>

                                    @endforeach

                                </ul>

                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-4 border-b text-center">

                                {{-- Pengajuan --}}
                                @if($item->status == 'diajukan')

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Setujui --}}
                                        <form
                                            action="{{ route('petugas.peminjaman.setujui', $item->id) }}"
                                            method="POST">

                                            @csrf

                                            <button
                                                type="submit"
                                                onclick="return confirm('Setujui peminjaman alat ini?')"
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition">

                                                Setujui

                                            </button>

                                        </form>

                                        {{-- Tolak --}}
                                        <form
                                            action="{{ route('petugas.peminjaman.tolak', $item->id) }}"
                                            method="POST">

                                            @csrf

                                            <button
                                                type="submit"
                                                onclick="return confirm('Yakin ingin menolak pengajuan ini?')"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition">

                                                Tolak

                                            </button>

                                        </form>

                                    </div>

                                {{-- Sedang Dipinjam --}}
                                @elseif($item->status == 'dipinjam')

                                    <a
                                        href="{{ route('petugas.pengembalian.create', $item->id) }}"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition inline-block">

                                        Terima Kembali

                                    </a>

                                {{-- Sudah Dikembalikan --}}
                                @elseif($item->status == 'dikembalikan')

                                    <span class="text-xs text-emerald-600 font-medium">
                                        Sudah Dikembalikan
                                    </span>

                                {{-- Terlambat --}}
                                @elseif($item->status == 'telat')

                                    <a
                                        href="{{ route('petugas.pengembalian.create', $item->id) }}"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition inline-block">

                                        Terima Kembali

                                    </a>

                                @else

                                    <span class="text-xs text-gray-400 font-medium">
                                        -
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="py-8 text-center text-gray-500 text-sm">

                                {{ $search
                                    ? 'Tidak ada peminjaman yang cocok.'
                                    : 'Belum ada data peminjaman.' }}

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-3 text-sm text-gray-600">

            <span>
                Menampilkan
                {{ $peminjaman->firstItem() ?? 0 }}-{{ $peminjaman->lastItem() ?? 0 }}
                dari
                {{ $peminjaman->total() }}
                peminjaman
            </span>

            {{ $peminjaman->links() }}

        </div>

    </div>

@endsection