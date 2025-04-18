<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Menampilkan produk hanya milik user yang sedang login
        // return response()->json(Product::where('user_id', auth()->user()->id)->get());
        // return response()->json(Product::where('user_id', $request->user()->id)->get());
        // return response()->json(Product::all());

        // ambil data produk milik user yang login dengan pagination
        $products = Product::where('user_id', $request->user()->id)->paginate(10);

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        // Tambahkan user_id yang berasal dari user yang sedang login
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'user_id' => $request->user()->id,
        ]);

        // return response()->json($product, 201);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan',
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        // $product = Product::find($id);
        // hanya ambil product milik user yang login
        $product = Product::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $product) {
            return response()->json([
                'message' => 'product tidak ditemukan.'
            ], 404);
        }

        // return response()->json($product);
        return response()->json([
            'status' => 'success',
            'message' => 'Detail produk berhasil diambil',
            'data' => new ProductResource($product)
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $product = Product::findOrFail($id);

        $product = Product::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan atau tidak punya akses'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $product->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil diperbarui',
            'data' => new ProductResource($product)
        ], 200);

        // $product->update($request->all());
        // return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        // Product::destroy($id);

        $product = Product::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan atau tidak punya akses'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil dihapus'
        ], 200);
    }
}
