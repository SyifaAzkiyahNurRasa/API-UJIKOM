@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header-title', 'Ringkasan Aktivitas Sistem')

@section('content')
    <!-- Alert Selamat Datang -->
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">
        Selamat datang, <strong class="font-semibold">{{ auth()->user()->name }}</strong>! Anda login sebagai hak akses
        <span class="uppercase font-bold text-emerald-900">{{ auth()->user()->role }}</span>.
    </div>

    <!-- Tabel Log Aktivitas -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-gray-800">Log Aktivitas Terbaru</h3>
            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex w-full md:w-80">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari user / aktivitas..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                    Cari
                </button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Waktu</th>
                        <th class="py-3 px-4 border-b">User</th>
                        <th class="py-3 px-4 border-b">Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 border-b">{{ $log->created_at }}</td>
                            <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $log->user->name ?? 'Sistem' }}</td>
                            <td class="py-3 px-4 border-b">{{ $log->aktivitas }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">
                                {{ $search ? 'Tidak ada log aktivitas yang cocok.' : 'Belum ada log aktivitas.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-3 text-sm text-gray-600">
            <span>
                Menampilkan {{ $logs->firstItem() ?? 0 }}-{{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} log
            </span>
            {{ $logs->links() }}
        </div>
    </div>
@endsection