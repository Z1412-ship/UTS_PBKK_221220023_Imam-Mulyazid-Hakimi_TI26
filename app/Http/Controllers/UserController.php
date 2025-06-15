<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    // Tampilkan semua user (hanya admin dan manager)
    public function index()
    {
        $user = Auth::user();

        // Jika admin atau manager, tampilkan semua user
        if ($user->email === 'admin@gmail.com' || $user->email === 'manager@gmail.com') {
            return response()->json(User::all(), 200);
        }

        // Jika bukan admin/manager (customer), tampilkan hanya data diri sendiri
        return response()->json([$user], 200); // bungkus dalam array agar bentuk respons tetap konsisten (array of users)
    }


    // Tampilkan user berdasarkan ID
    public function show($id)
    {
        $user = Auth::user();

        // Jika admin atau manager, izinkan akses user manapun berdasarkan ID
        if ($user->email === 'admin@gmail.com' || $user->email === 'manager@gmail.com') {
            return response()->json(User::findOrFail($id), 200);
        }

        // Jika user biasa, hanya boleh akses data dirinya sendiri
        if ($user->user_id === $id) {
            return response()->json($user, 200);
        }

        // Selain itu, tolak akses
        return response()->json(['message' => 'Akses ditolak'], 403);
    }


    // Update user berdasarkan ID
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Admin, Manager, atau user yang sesuai hanya bisa update
        if (
            $user->email === 'admin@gmail.com' ||
            $user->email === 'manager@gmail.com' ||
            $user->user_id === $id
        ) {
            $target = User::findOrFail($id);

            // Validasi opsional (tambahkan jika diperlukan)
            $validated = $request->validate([
                'name' => 'sometimes|required|string',
                'email' => 'sometimes|required|email',
                'password' => 'sometimes|required|string|min:6'
            ]);

            $target->update($validated);

            return response()->json(['message' => 'User berhasil diupdate'], 200);
        }

        return response()->json(['message' => 'Akses ditolak'], 403);
    }

    // Hapus user berdasarkan ID
    public function destroy($id)
    {
        $user = Auth::user();

        if (
            $user->email === 'admin@gmail.com' ||
            $user->email === 'manager@gmail.com' ||
            $user->user_id === $id // user biasa hanya bisa hapus dirinya sendiri
        ) {
            $target = User::findOrFail($id);
            $target->delete();

            return response()->json(['message' => 'User berhasil dihapus'], 200);
        }

        return response()->json(['message' => 'Akses ditolak'], 403);
    }

    // Tidak digunakan dalam apiResource kecuali kamu override
    public function store(Request $request)
    {
        return response()->json(['message' => 'Registrasi user tidak diizinkan di sini'], 403);
    }
}