<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // ambil semua user (dengan pagination)
    public function index()
    {
        // ambil data user dengan pagination 10 per halaman
        $users = User::paginate(10);

        // balikan data dalam bentuk JSON dengan resource + info pagination
        return response()->json([
            'status' => 'success',
            'message' => 'Daftar user berhasil diambil',
            'data' => [
                'users' => UserResource::collection($users->items()), // Format data user
                'pagination' => [ // Info pagination untuk frontend
                    'currentPage' => $users->currentPage(),
                    'perPage' => $users->perPage(),
                    'total' => $users->total(),
                    'lastPage' => $users->lastPage()
                ]
            ]
        ], 200);
    }

    // update data user
    public function update(Request $request, $id)
    {
        // validasi data yang dikirimkan (opsional, tergantung field yang dikirim)
        $request->validate([
            'name' => 'sometimes|string|max:225',
            'email' => 'sometimes|string|unique:users,email,' . $id,
            'role' => 'in:user,admin', // Role hanya boleh user atau admin
        ]);

        // cari user berdasarkan ID, kalau tidak ketemu akan throw 404
        $user = User::findOrFail($id);

        // update data user dengan hanya field name, email, dan role
        $user->update($request->only(['name', 'email', 'role']));

        // kembalikan response sukses beserta data user yang telah diubah
        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil diperbarui',
            'data' => new UserResource($user)
        ], 200);
    }

    // hapus user
    public function destroy($id)
    {
        // cari user berdasarkan ID
        $user = User::findOrFail($id);

        // hapus user dari database
        $user->delete();

        // kembalikan response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil dihapus'
        ], 200);
    }

    // reset password user
    public function resetPassword(Request $request, $id)
    {
        // validasi input password baru
        $request->validate([
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password'
        ]);

        // cari user berdasarkan ID
        $user = User::findOrFail($id);

        // update password user (dienkripsi dengan bcrypt)
        $user->update([
            'password' => bcrypt($request->new_password)
        ]);

        // kembalikan response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil direset',
            'data' => new UserResource($user)
        ], 200);
    }
}
