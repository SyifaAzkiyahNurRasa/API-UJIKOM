<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_roles_can_view_and_update_their_own_profile(): void
    {
        foreach (['admin', 'petugas', 'peminjam'] as $role) {
            $user = User::create([
                'name' => ucfirst($role).' User',
                'email' => $role.'-profile@example.com',
                'password' => 'password123',
                'role' => $role,
            ]);
            $updatedName = $user->name.' Updated';

            $this->actingAs($user)
                ->get(route('profile.show'))
                ->assertOk()
                ->assertSee('Profil Saya')
                ->assertSee($user->username);

            $this->put(route('profile.update'), [
                'name' => $updatedName,
                'username' => $user->username,
                'email' => $user->email,
                'no_hp' => '081234567890',
                'alamat' => 'Bandung',
            ])->assertRedirect(route('profile.show'));

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => $updatedName,
                'role' => $role,
                'no_hp' => '081234567890',
                'alamat' => 'Bandung',
            ]);
        }
    }

    public function test_profile_requires_authentication(): void
    {
        $this->get(route('profile.show'))->assertRedirect(route('login'));
    }
}
