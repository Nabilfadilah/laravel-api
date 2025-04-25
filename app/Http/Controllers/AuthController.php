<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // fungsi register user baru
    public function register(Request $request)
    {
        // validasi inputan register
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // simpan user baru dengan password terenkripsi dan role default 'user'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user', // default role nya user
        ]);

        // kembalikan data user sebagai respons dengan status 201 (created)
        return response()->json($user, 201);
    }

    // fungsi login user
    public function login(Request $request)
    {
        try {
            // cari user berdasarkan email
            $user = User::where('email', $request->email)->first();

            // cek password dan keberadaan user
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Email or password is incorrect'
                ], 401);
            }

            // generate token menggunakan Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            // kembalikan respons JSON
            return response()->json([
                'token' => $token,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // fungsi logout user (hapus token yang aktif)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout Success']);
    }

    // fungsi untuk ambil data user yang sedang login
    public function me(Request $request)
    {
        return $request->user();
    }

    // untuk frontend
    // login via blade
    public function handleWebLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Session regenerasi dan redirect
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/products')->with('success', 'Login berhasil');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // register via blade
    public function handleWebRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user'
        ]);

        Auth::login($user); // langsung login setelah register
        return redirect()->route('products.index');

        // return redirect('/products')->with('success', 'Registrasi berhasil');
    }

    // logout via blade
    public function handleWebLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Berhasil logout');
    }
}
