<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // 1. Menampilkan halaman form & daftar kategori
    public function index()
    {
        $categories = Category::latest()->get();
        return view('categories.index', compact('categories'));
    }

    // 2. Menyimpan data dari form ke database MySQL
    public function store(Request $request)
    {
        // Validasi: inputan 'name' wajib diisi
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Simpan ke database
        Category::create([
            'name' => $request->name,
        ]);

        // Kembalikan ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        // 1. Ambil data kategori berdasarkan ID
        $category = Category::findOrFail($id);

        // 2. Cek apakah masih ada produk yang memakai kategori ini
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Gagal menghapus! Masih ada produk yang terhubung dengan kategori ini.');
        }

        // 3. Hapus kategori
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}