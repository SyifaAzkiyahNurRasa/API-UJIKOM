<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PeminjamRiwayatTest extends TestCase
{
    use RefreshDatabase;

    public function test_peminjam_dapat_melihat_riwayat_peminjaman_miliknya(): void
    {
        $peminjam = User::create([
            'name' => 'Peminjam',
            'email' => 'peminjam@example.test',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
        ]);
        $peminjamLain = User::create([
            'name' => 'Peminjam Lain',
            'email' => 'peminjam-lain@example.test',
            'password' => Hash::make('password'),
            'role' => 'peminjam',
        ]);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alatMilikPeminjam = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Kamera Peminjam',
            'stok' => 2,
            'status_kondisi' => 'Baik',
        ]);
        $alatPeminjamLain = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Kamera Orang Lain',
            'stok' => 1,
            'status_kondisi' => 'Baik',
        ]);

        $peminjamanMilikUser = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => now()->addDay(),
            'status' => 'dikembalikan',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjamanMilikUser->id,
            'alat_id' => $alatMilikPeminjam->id,
            'jumlah' => 1,
        ]);

        $peminjamanMilikUserLain = Peminjaman::create([
            'user_id' => $peminjamLain->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => now()->addDay(),
            'status' => 'dipinjam',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjamanMilikUserLain->id,
            'alat_id' => $alatPeminjamLain->id,
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($peminjam)->get(route('peminjam.riwayat'));

        $response->assertOk()
            ->assertViewIs('peminjam.riwayat')
            ->assertSee('Kamera Peminjam')
            ->assertSee('Elektronik')
            ->assertSee('Dikembalikan')
            ->assertSee('Riwayat Peminjaman')
            ->assertDontSee('Kamera Orang Lain');
    }
}
