<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'user'])->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil',
            'data' => $products
        ], 200);
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $validated = $request->validated();

            // User_id diambil otomatis dari token yang sedang login
            $validated['user_id'] = Auth::id();

            $product = Product::create($validated);

            Log::info('API: Menambah produk', ['data' => $product]);

            return response()->json([
                'message' => 'Produk berhasil ditambahkan!!',
                'data' => $product,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('API Error: Gagal tambah produk', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal simpan data'], 500);
        }
    }

    public function show(int $id)
    {
        $product = Product::with(['category', 'user'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => $product
        ], 200);
    }

    public function update(StoreProductRequest $request, $id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Produk tidak ditemukan'], 404);

        $validated = $request->validated();
        $product->update($validated);

        return response()->json([
            'message' => 'Produk berhasil diupdate',
            'data' => $product
        ], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Produk tidak ditemukan'], 404);

        $product->delete();
        return response()->json(['message' => 'Produk berhasil dihapus'], 200);
    }
}