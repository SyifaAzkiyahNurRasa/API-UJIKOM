<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LogAktivitasResource;
use App\Models\LogAktivitas;
use Illuminate\Http\JsonResponse;

class LogAktivitasController extends Controller
{
    public function index(): JsonResponse
    {
        $logs = LogAktivitas::with('user')
            ->latest()
            ->paginate(5);

        return response()->json([
            'message' => 'Seluruh catatan log aktivitas berhasil diambil.',
            'total_data' => $logs->total(),
            'data' => LogAktivitasResource::collection($logs),
        ]);
    }
}