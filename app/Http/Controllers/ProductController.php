<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $products = Product::with('category')->latest()->get();
        return view('products.index', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code'        => 'required|unique:products,code',
            'name'        => 'required',
            'buy_price'   => 'required|numeric',
            'sell_price'  => 'required|numeric',
            'stock'       => 'required|integer',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'category_id' => $request->category_id,
            'code'        => $request->code,
            'name'        => $request->name,
            'buy_price'   => $request->buy_price,
            'sell_price'  => $request->sell_price,
            'stock'       => $request->stock,
            'image'       => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code'        => 'required|unique:products,code,' . $product->id,
            'name'        => 'required',
            'buy_price'   => 'required|numeric',
            'sell_price'  => 'required|numeric',
            'stock'       => 'required|integer',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'code'        => $request->code,
            'name'        => $request->name,
            'buy_price'   => $request->buy_price,
            'sell_price'  => $request->sell_price,
            'stock'       => $request->stock,
        ];

        if ($request->hasFile('image')) {
            // Hapus foto lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus!');
    }
}