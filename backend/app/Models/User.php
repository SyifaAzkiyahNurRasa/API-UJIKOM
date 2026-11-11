<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users'; 

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role', 'no_hp', 'alamat', 'foto_profile'
    ];

    protected $hidden = [         
        'password', 'remember_token',     
    ]; 

    protected function casts(): array 
    {
        return [  
            'email_verified_at' => 'datetime',           
            'password' => 'hashed', 
        ]; 
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if ($user->username) {
                return;
            }

            $base = Str::slug($user->name, '_') ?: 'user';
            $username = $base;
            $suffix = 1;

            while (static::where('username', $username)->exists()) {
                $username = "{$base}_{$suffix}";
                $suffix++;
            }

            $user->username = $username;
        });
    }

    public function peminjaman(): HasMany {
        return $this->hasMany(Peminjaman::class); 
    }

    public function logAktivitas(): HasMany {
        return $this->hasMany(LogAktivitas::class);
    }

    public function scopeTersedia($query) 
    { 
        return $query->where('stok', '>', 0)->where('status_kondisi', 'Baik'); 
    } 
}
