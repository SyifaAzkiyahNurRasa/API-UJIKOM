<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeminjamanResource;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LaporanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // 1. Validasi input parameter
        $validator = Validator::make($request->all(), [
            'start_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
            ],

            'end_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
            ],

            'status' => [
                'nullable',
                'string',
                'in:diajukan,dipinjam,dikembalikan,telat',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Parameter filter tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // 2. Eager loading untuk mencegah masalah N+1 Query
        $query = Peminjaman::with([
            'user',
            'detailPinjam.alat',
            'pengembalian.petugas',
        ]);

        // Filter: rentang tanggal pinjam
        $query->when(
            $request->filled('start_date') &&
            $request->filled('end_date'),
            function ($q) use ($request) {
                $q->whereBetween('tgl_pinjam', [
                    $request->start_date,
                    $request->end_date,
                ]);
            }
        );

        // Filter: status peminjaman
        $query->when(
            $request->filled('status'),
            function ($q) use ($request) {
                $q->where('status', $request->status);
            }
        );

        // Pagination
        $perPage = $request->input('per_page', 15);

        $laporan = $query
            ->latest()
            ->paginate($perPage);

        // API Resource untuk data pagination
        return PeminjamanResource::collection($laporan)
            ->additional([
                'message' => 'Laporan peminjaman berhasil ditarik.',
            ])
            ->response();
    }
}