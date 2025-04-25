<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** 
     * Trait bawaan Laravel:
     * - HasApiTokens: untuk fitur token authentication (Sanctum)
     * - HasFactory: untuk factory (digunakan saat testing/seeding)
     * - Notifiable: untuk fitur notifikasi
     */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Atribut yang boleh diisi secara massal (mass assignment).
     * Hanya field ini yang bisa diisi menggunakan create/update dari request.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Atribut yang disembunyikan saat model dikonversi ke array atau JSON.
     * Tujuannya agar data sensitif seperti password tidak ikut terkirim.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     * - email_verified_at di-cast menjadi objek DateTime
     * - password akan otomatis di-hash saat diset (fitur Laravel 10+)
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi antara User dan Product (One To Many).
     * Artinya: 1 user bisa punya banyak produk.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
