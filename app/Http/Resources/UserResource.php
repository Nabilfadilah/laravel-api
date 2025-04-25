<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    // fungsi ini akan otomatis dipanggil saat data User diubah menjadi JSON
    public function toArray(Request $request): array
    {
        return [
            // ambil id user
            'id'       => $this->id,

            // ambil nama user
            'name'     => $this->name,

            // ambil email user
            'email'    => $this->email,

            // ambil rolenya
            'role'     => $this->role,

            // tanggal pembuatan produk dalam format ISO 8601 (standar waktu internasional)
            'created_at' => $this->created_at->toIso8601String(),

            // tanggal update terakhir juga pakai format ISO
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
