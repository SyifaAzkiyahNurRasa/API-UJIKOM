@extends('layouts.app') {{-- Sesuaikan dengan nama layout utama Anda --}}

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Riwayat Peminjaman Saya</h2>

    @if($peminjamans->isEmpty())
        <div class="alert alert-info">
            Belum ada riwayat peminjaman.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal Pinjam</th>
                        <th>Daftar Alat</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($peminjamans as $peminjaman)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($peminjaman->created_at)->format('d M Y, H:i') }}</td>
                            <td>
                                <ul>
                                    @foreach($peminjaman->detailPinjams as $detail)
                                        <li>
                                            {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }} 
                                            (Qty: {{ $detail->jumlah ?? 1 }})
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ ucfirst($peminjaman->status ?? 'Diproses') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection