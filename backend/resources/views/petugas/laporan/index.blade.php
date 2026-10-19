@extends('layouts.app')

@section('title', 'Cetak Laporan - Dashboard Petugas')
@section('header-title', 'Laporan Peminjaman Alat')

@section('content')
    <style>
        #report-print-title {
            display: none;
        }

        @media print {
            @page {
                margin: 12mm;
            }

            body {
                background: #fff !important;
                overflow: visible !important;
            }

            body > div {
                display: block !important;
                height: auto !important;
                width: auto !important;
            }

            body > div > aside,
            body > div > div > header,
            main > *:not(#printable-report),
            #report-pagination {
                display: none !important;
            }

            body > div > div,
            body > div > div > main {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
                padding: 0 !important;
            }

            body * {
                visibility: hidden !important;
            }

            #printable-report,
            #printable-report * {
                visibility: visible !important;
            }

            #printable-report {
                border: 0 !important;
                box-shadow: none !important;
                color: #111827 !important;
                overflow: visible !important;
                width: 100% !important;
            }

            #report-print-title {
                display: block !important;
            }

            #printable-report .overflow-x-auto {
                overflow: visible !important;
            }

            #printable-report table {
                font-size: 9pt;
                width: 100%;
            }

            #printable-report th,
            #printable-report td {
                padding: 6px 5px !important;
            }

            #printable-report tr {
                break-inside: avoid;
            }
        }
    </style>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h3 class="text-lg font-bold text-slate-800">Rekap Data Peminjaman</h3>
                <p class="text-slate-500 text-sm">Cetak data riwayat peminjaman laboratorium sesuai status.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('petugas.laporan.index') }}" class="flex flex-wrap items-center gap-2">
                    <label for="status" class="text-sm font-medium text-slate-600">Status</label>
                    <select id="status" name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-blue-100 hover:bg-slite-200 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Terapkan
                    </button>
                </form>

                <button type="button" onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-print"></i>
                    Cetak Laporan
                </button>
            </div>
        </div>
    </div>

    <div id="printable-report" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div id="report-print-title" class="border-b border-slate-200 px-4 py-5">
            <h1 class="text-xl font-bold text-slate-900">Laporan Peminjaman Alat</h1>
            @if($status)
                <p class="mt-1 text-sm text-slate-600">Status: {{ $statusOptions[$status] }}</p>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                        <th class="py-3.5 px-4 font-semibold">No</th>
                        <th class="py-3.5 px-4 font-semibold">Peminjam</th>
                        <th class="py-3.5 px-4 font-semibold">Tgl Pinjam</th>
                        <th class="py-3.5 px-4 font-semibold">Rencana Kembali</th>
                        <th class="py-3.5 px-4 font-semibold">Tanggal Dikembalikan</th>
                        <th class="py-3.5 px-4 font-semibold">Alat</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($peminjaman as $index => $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-medium text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-medium text-slate-900">{{ $item->user->name ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ \Carbon\Carbon::parse($item->tgl_pinjam)->locale('id')->translatedFormat('d F Y, H:i') }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->locale('id')->translatedFormat('d F Y, H:i') }}</td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $item->pengembalian?->tgl_kembali
                                    ? \Carbon\Carbon::parse($item->pengembalian->tgl_kembali)->locale('id')->translatedFormat('d F Y, H:i')
                                    : '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <ul class="list-disc list-inside text-xs text-slate-600">
                                    @foreach($item->detailPinjams as $detail)
                                        <li>{{ $detail->alat->nama_alat ?? 'Alat' }} ({{ $detail->jumlah }} pcs)</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                Belum ada data untuk laporan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="report-pagination" class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $peminjaman->links() }}
        </div>
    </div>
@endsection