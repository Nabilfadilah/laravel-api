<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    // fungsi ini akan otomatis dipanggil saat data Product diubah menjadi JSON
    public function toArray(Request $request): array
    {
        return [
            // ambil id produk
            'id' => $this->id,

            // ambil nama produk
            'name' => $this->name,

            // ambil deskripsi produk (bisa null)
            'description' => $this->description,

            // konversi harga jadi float biar lebih konsisten di frontend
            'price' => (float) $this->price,

            // kalau produk punya gambar, buat URL-nya ke folder 'storage/products', kalau nggak ada set null
            'image' => $this->image ? asset('storage/' . $this->image) : null,

            // ID user pemilik produk
            'user_id' => $this->user_id,

            // tanggal pembuatan produk dalam format ISO 8601 (standar waktu internasional)
            'created_at' => $this->created_at->toIso8601String(),

            // tanggal update terakhir juga pakai format ISO
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
