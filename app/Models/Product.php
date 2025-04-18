<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'user_id'];

    // relasi antara table user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
