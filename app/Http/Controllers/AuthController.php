<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Untuk ULID
use Illuminate\Support\Facades\Auth; // Untuk akses user yang login
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:50|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'user_id' => Str::ulid(), // Generate ULID
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'membership_date' => now()->toDateString(),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Hapus token lama jika hanya ingin satu sesi login
        $user->tokens()->delete();

        $token = $user->createToken('access_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ]);
    }

    // ✅ Fungsi Logout: Hapus token yang sedang aktif
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete(); // Hapus token saat ini
            return response()->json([
                'message' => 'Logout berhasil, token dihapus.'
            ]);
        } else {
            return response()->json([
                'message' => 'Token tidak ditemukan atau sudah tidak aktif.'
            ], 400);
        }
    }
}
