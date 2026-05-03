<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @OA\Info(title="API Produk UTP", version="1.0")
     */

    private $products = [
        ['id' => 1, 'name' => 'Produk A', 'price' => 10000, 'stock' => 50],
        ['id' => 2, 'name' => 'Produk B', 'price' => 20000, 'stock' => 30],
        ['id' => 3, 'name' => 'Produk C', 'price' => 15000, 'stock' => 20],
    ];

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     @OA\Response(response=200, description="List of products")
     * )
     */
    public function index() {
        return response()->json($this->products);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get product by ID",
     *     @OA\Parameter(
     *         name="id", in="path", required=true, @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Product found"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function show($id) {
        $product = collect($this->products)->firstWhere('id', (int)$id);
        if (!$product) {
            return response()->json(['error' => "Item dengan ID $id tidak Ditemukan"], 404);
        }
        return response()->json($product);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create new product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","stock"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="stock", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer'
        ]);

        $newProduct = [
            'id' => count($this->products) + 1,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock
        ];

        $this->products[] = $newProduct;
        return response()->json($newProduct, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update full product",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","stock"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="stock", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer'
        ]);

        foreach ($this->products as &$product) {
            if ($product['id'] == $id) {
                $product['name'] = $request->name;
                $product['price'] = $request->price;
                $product['stock'] = $request->stock;
                return response()->json($product);
            }
        }
        return response()->json(['error' => "Item dengan ID $id tidak Ditemukan"], 404);
    }

    /**
     * @OA\Patch(
     *     path="/api/products/{id}",
     *     summary="Partial update product",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="stock", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function partialUpdate(Request $request, $id) {
        foreach ($this->products as &$product) {
            if ($product['id'] == $id) {
                $product = array_merge($product, $request->only(['name', 'price', 'stock']));
                return response()->json($product);
            }
        }
        return response()->json(['error' => "Item dengan ID $id tidak Ditemukan"], 404);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete product",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product deleted"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function destroy($id) {
        foreach ($this->products as $key => $product) {
            if ($product['id'] == $id) {
                unset($this->products[$key]);
                return response()->json(['message' => "Produk dengan ID $id berhasil dihapus"]);
            }
        }
        return response()->json(['error' => "Item dengan ID $id tidak Ditemukan"], 404);
    }
}