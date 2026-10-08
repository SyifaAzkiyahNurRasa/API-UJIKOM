<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id', 'tgl_pinjam', 'tgl_kembali_plan', 'status', 'pengembalian_diajukan_at'
    ];

    protected function casts(): array {
        return [
            'tgl_pinjam' => 'datetime',
            'tgl_kembali_plan' => 'datetime',
            'pengembalian_diajukan_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // Menggunakan bentuk jamak 'detailPinjams'
    public function detailPinjams(): HasMany {
        return $this->hasMany(DetailPinjam::class, 'peminjaman_id');
    }

    public function pengembalian(): HasOne {
        return $this->hasOne(Pengembalian::class);
    }
}