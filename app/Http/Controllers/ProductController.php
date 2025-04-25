<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar produk (punya user yang login)
     */
    public function index(Request $request)
    {
        // Menampilkan produk hanya milik user yang sedang login
        // return response()->json(Product::where('user_id', auth()->user()->id)->get());
        // return response()->json(Product::where('user_id', $request->user()->id)->get());
        // return response()->json(Product::all());

        // ambil produk hanya yang dibuat oleh user yang sedang login, dengan pagination 10 per halaman
        $products = Product::where('user_id', $request->user()->id)->paginate(10);

        // balikin response JSON: daftar produk + data pagination
        return response()->json([
            'status' => 'success',
            'message' => 'Daftar produk berhasil diambil',
            'data' => [
                'products' => ProductResource::collection($products->items()),
                'pagination' => [
                    'currentPage' => $products->currentPage(),
                    'perPage' => $products->perPage(),
                    'total' => $products->total(),
                    'lastPage' => $products->lastPage()
                ]
            ]
        ], 200);
    }

    /**
     * Simpan produk baru ke database
     */
    public function store(Request $request)
    {
        // validasi input dari user
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048' // max 2MB
        ]);

        // kalau ada file gambar yang di-upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            // simpan file gambar ke folder "storage/app/public/products"
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // buat produk baru di database, termasuk user_id dari user yang login
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'user_id' => $request->user()->id,
        ]);

        // return response()->json($product, 201);

        // balikin response JSON: product berhasil dibuat
        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan',
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Tampilkan detail produk berdasarkan ID
     */
    public function show(Request $request, $id)
    {
        // cari produk berdasarkan id dan user_id (biar produk milik orang lain gak bisa diakses)
        $product = Product::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        // kalau produk gak ketemu, kirim response 404
        if (! $product) {
            return response()->json([
                'message' => 'product tidak ditemukan.'
            ], 404);
        }

        // kalau produk ketemu, balikin detail produk
        return response()->json([
            'status' => 'success',
            'message' => 'Detail produk berhasil diambil',
            'data' => new ProductResource($product)
        ], 200);
    }

    /**
     * Update data product
     */
    public function update(Request $request, $id)
    {
        // cari produk berdasarkan id (kalau gak ada, error 404 otomatis dari findOrFail)
        $product = Product::findOrFail($id);

        // validasi input baru dari user
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // kalau user upload gambar baru
        if ($request->hasFile('image')) {
            // hapus gambar lama dari storage
            if ($product->image) {
                Storage::delete($product->image);
            }

            // upload gambar baru
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath; // sama kayak di create
            // $validated['image'] = asset('storage/' . $imagePath); // sama kayak di create
        }

        // update data product di database
        $product->update($validated);

        // balikin response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil diperbarui',
            'data' => $product
        ]);
    }

    /**
     * Hapus product
     */
    public function destroy(Request $request, $id)
    {
        // cari produk berdasarkan id DAN user_id (hanya produk user sendiri yang bisa dihapus)
        $product = Product::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        // kalau produk tidak ditemukan atau bukan milik user
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan atau tidak punya akses'
            ], 404);
        }

        // hapus produk dari database
        $product->delete();

        // balikin response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil dihapus'
        ], 200);
    }
}


// public function update(Request $request, $id)
    // {
    //     $product = Product::findOrFail($id);

    //     $validated = $request->validate([
    //         'name' => 'required|string',
    //         'description' => 'nullable|string',
    //         'price' => 'required|numeric',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    //     ]);

    //     // Handle image upload
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $imageName = time() . '_' . $image->getClientOriginalName();
    //         $image->move(public_path('products'), $imageName);
    //         $validated['image'] = 'products/' . $imageName;
    //     }

    //     $product->update($validated);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Produk berhasil diperbarui',
    //         'data' => $product
    //     ]);
    // }

    // public function update(Request $request, $id)
    // {
    //     // $product = Product::findOrFail($id);

    //     $product = Product::where('id', $id)
    //         ->where('user_id', $request->user()->id)
    //         ->first();

    //     if (!$product) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Produk tidak ditemukan atau tidak punya akses'
    //         ], 404);
    //     }

    //     $validated = $request->validate([
    //         'name' => 'sometimes|required|string|max:255',
    //         'description' => 'nullable|string',
    //         'price' => 'sometimes|required|numeric|min:0',
    //         'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    //     ]);

    //     // handle image
    //     $updateData = $validated;

    //     if ($request->hasFile('image')) {
    //         // Hapus file lama jika ada
    //         if ($product->image && Storage::disk('public')->exists($product->image)) {
    //             Storage::disk('public')->delete($product->image);
    //         }

    //         $imagePath = $request->file('image')->store('products', 'public');
    //         $updateData['image'] = $imagePath;
    //     }

    //     $product->update($updateData);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Produk berhasil diperbarui',
    //         'data' => new ProductResource($product)
    //     ], 200);

    //     // $product->update($request->all());
    //     // return response()->json($product);
    // }