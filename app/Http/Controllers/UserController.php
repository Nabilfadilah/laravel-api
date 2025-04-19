<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // get all users
    public function index()
    {
        // return User::all(); // mengembalikan semua data user

        $users = User::paginate(10); // atau pakai ->get() kalau tidak mau paginate

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar user berhasil diambil',
            'data' => [
                'users' => UserResource::collection($users->items()),
                'pagination' => [
                    'currentPage' => $users->currentPage(),
                    'perPage' => $users->perPage(),
                    'total' => $users->total(),
                    'lastPage' => $users->lastPage()
                ]
            ]
        ], 200);
    }

    // update user
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:225',
            'email' => 'sometimes|string|unique:users,email,' . $id,
            'role' => 'in:user,admin', // batasi pilihan role
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'email', 'role']));

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil diperbarui',
            'data' => new UserResource($user)
        ], 200);
    }

    // delete user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil dihapus'
        ], 200);
    }

    // Reset password
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => bcrypt($request->new_password)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil direset',
            'data' => new UserResource($user)
        ], 200);
    }
}
