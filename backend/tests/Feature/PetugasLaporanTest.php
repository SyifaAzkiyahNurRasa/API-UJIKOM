<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasLaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_laporan_dapat_difilter_berdasarkan_status(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $peminjamDipinjam = User::factory()->create(['name' => 'Peminjam Dipinjam']);
        $peminjamDikembalikan = User::factory()->create(['name' => 'Peminjam Dikembalikan']);

        Peminjaman::create([
            'user_id' => $peminjamDipinjam->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => now()->addDay(),
            'status' => 'dipinjam',
        ]);
        Peminjaman::create([
            'user_id' => $peminjamDikembalikan->id,
            'tgl_pinjam' => now()->subDays(2),
            'tgl_kembali_plan' => now()->subDay(),
            'status' => 'dikembalikan',
        ]);

        $response = $this->actingAs($petugas)->get(route('petugas.laporan.index', [
            'status' => 'dipinjam',
        ]));

        $response->assertOk()
            ->assertSee('Peminjam Dipinjam')
            ->assertDontSee('Peminjam Dikembalikan')
            ->assertSee('Status: Dipinjam')
            ->assertViewHas('status', 'dipinjam');
    }
}
