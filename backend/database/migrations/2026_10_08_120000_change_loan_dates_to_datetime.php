<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dateTime('tgl_pinjam')->change();
            $table->dateTime('tgl_kembali_plan')->change();
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dateTime('tgl_kembali')->change();
        });

        DB::table('peminjaman')
            ->select(['id', 'tgl_kembali_plan'])
            ->orderBy('id')
            ->get()
            ->each(function ($peminjaman) {
                $batasWaktu = (string) $peminjaman->tgl_kembali_plan;

                if (strlen($batasWaktu) === 10 || substr($batasWaktu, 11, 8) === '00:00:00') {
                    DB::table('peminjaman')
                        ->where('id', $peminjaman->id)
                        ->update([
                            'tgl_kembali_plan' => substr($batasWaktu, 0, 10) . ' 23:59:59',
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->date('tgl_kembali')->change();
        });

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->date('tgl_pinjam')->change();
            $table->date('tgl_kembali_plan')->change();
        });
    }
};
