<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\BookAuthor;
use App\Models\Book;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookAuthorsController extends Controller
{
    public function __construct()
    {
        // Semua pengguna wajib login dan memiliki token
        $this->middleware('auth:sanctum');
    }

    // ========== CREATE ==========
    public function store(Request $request)
    {
        $user = Auth::user();

        // Hanya admin dan manager
        if (!in_array($user->email, ['admin@gmail.com', 'manager@gmail.com'])) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'book_id' => 'required|string',
            'author_id' => 'required|string',
        ]);

        // Pastikan book_id dan author_id ada di tabel masing-masing
        if (!Book::where('book_id', $validated['book_id'])->exists()) {
            return response()->json(['message' => 'book_id tidak ditemukan'], 404);
        }

        if (!Author::where('author_id', $validated['author_id'])->exists()) {
            return response()->json(['message' => 'author_id tidak ditemukan'], 404);
        }

        $bookAuthor = BookAuthor::create($validated);

        return response()->json(['message' => 'Data berhasil ditambahkan', 'data' => $bookAuthor], 201);
    }

    // ========== INDEX (Read All) ==========
    public function index()
    {
        $user = Auth::user();

        // Semua pengguna bisa melihat semua data
        $bookAuthors = BookAuthor::with(['book', 'author'])->get();

        return response()->json($bookAuthors);
    }

    // ========== SHOW (Read One by id) ==========
    public function show($id)
    {
        $user = Auth::user();

        // Hanya admin dan manager
        if (!in_array($user->email, ['admin@gmail.com', 'manager@gmail.com'])) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $bookAuthor = BookAuthor::with(['book', 'author'])->find($id);

        if (!$bookAuthor) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($bookAuthor);
    }

    // ========== UPDATE ==========
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Hanya admin dan manager
        if (!in_array($user->email, ['admin@gmail.com', 'manager@gmail.com'])) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'book_id' => 'required|string',
            'author_id' => 'required|string',
        ]);

        // Pastikan book_id dan author_id valid
        if (!Book::where('book_id', $validated['book_id'])->exists()) {
            return response()->json(['message' => 'book_id tidak ditemukan'], 404);
        }

        if (!Author::where('author_id', $validated['author_id'])->exists()) {
            return response()->json(['message' => 'author_id tidak ditemukan'], 404);
        }

        $bookAuthor = BookAuthor::find($id);

        if (!$bookAuthor) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $bookAuthor->update($validated);

        return response()->json(['message' => 'Data berhasil diupdate', 'data' => $bookAuthor]);
    }

    // ========== DELETE ==========
    public function destroy($id)
    {
        $user = Auth::user();

        // Hanya admin dan manager
        if (!in_array($user->email, ['admin@gmail.com', 'manager@gmail.com'])) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $bookAuthor = BookAuthor::find($id);

        if (!$bookAuthor) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $bookAuthor->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}