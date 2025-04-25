<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    // trait untuk membuat data dummy (seperti faker), biasanya dipakai di seeder/test
    use HasFactory;

    // field yang boleh diisi secara mass-assignment (dari form, request, dll)
    protected $fillable = ['name', 'description', 'price', 'image', 'user_id'];

    // relasi: Setiap produk dimiliki oleh satu user
    // ini berguna kalau kita pengen akses user dari sebuah produk, misalnya $product->user->name
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
