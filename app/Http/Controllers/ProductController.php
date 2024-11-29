<?php

namespace App\Http\Controllers;

use App\Events\ProductUpdatedOrCreated;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdatedProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use DB;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Product Management API",
 *     description="API for managing products in an e-commerce platform."
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="E-Commerce API Server"
 * )
 */
class ProductController extends Controller
{

    /**
     * @OA\Get(
     *     path="/products",
     *     operationId="getProducts",
     *     tags={"Products"},
     *     summary="Get a list of products",
     *     description="Retrieve a paginated list of products with optional filtering and sorting.",
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter products by category name",
     *         required=false,
     *         @OA\Schema(type="string", example="Electronics")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort products by a specific field (e.g., name, price)",
     *         required=false,
     *         @OA\Schema(type="string", example="name")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Order of sorting (asc or desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of products per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Product::query()->with('categories');

        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) => $q->where('name', $request->category));
        }

        if ($request->filled('sort_by')) {
            $query->orderBy($request->sort_by, $request->get('order', 'asc'));
        }
        $products = $query->paginate($request->get('per_page', 5));

        return ProductResource::collection($products);
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     operationId="createProduct",
     *     tags={"Products"},
     *     summary="Create a new product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "categories"},
     *             @OA\Property(property="name", type="string", example="Smartphone"),
     *             @OA\Property(property="description", type="string", example="Latest model with advanced features"),
     *             @OA\Property(property="price", type="number", format="float", example=599.99),
     *             @OA\Property(property="stock", type="integer", example=50),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="integer", example=1))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function store(ProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $product = Product::create($request->toArray());
            $product->categories()->sync($request->categories);

            //NOTE - I have used event and listner as first solution, than i opted for Observer (ProductObserver) which is better for listening for many events on one model, Observer classes have method names which reflect the Eloquent events we wish to listen for (in this case created and updated)

            // event(new ProductUpdatedOrCreated($product, 'created'));

            return response()->json(['message' => 'Product added with success', 'product' => $product->load('categories')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred while saving the product', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     operationId="getProduct",
     *     tags={"Products"},
     *     summary="Get a product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $product = Product::with('categories')->findOrFail($id);
            return response()->json(['product' => $product]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Product not found', 'error' => $e->getMessage()], 404);
        }

    }

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     operationId="updateProduct",
     *     tags={"Products"},
     *     summary="Update an existing product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product to update",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price"},
     *             @OA\Property(property="name", type="string", example="Updated Smartphone"),
     *             @OA\Property(property="description", type="string", example="Improved version"),
     *             @OA\Property(property="price", type="number", format="float", example=699.99),
     *             @OA\Property(property="stock", type="integer", example=40),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="integer", example=1))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function update(UpdatedProductRequest $request, $product)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($product);
            $product->update($request->toArray());
            $product->categories()->sync($request->categories);

            // event(new ProductUpdatedOrCreated($product, 'updated'));

            return response()->json(['message' => 'Product updated with success', 'product' => $product->load('categories')], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred while updating the product', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     operationId="deleteProduct",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product to delete",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function destroy($product)
    {
        try {
            $product = Product::findOrFail($product);
            $product->delete();

            return response()->json(['message' => 'Product deleted with success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while deleting the product', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/products/search",
     *     operationId="searchProducts",
     *     tags={"Products"},
     *     summary="Search for products by name or description",
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search query",
     *         required=true,
     *         @OA\Schema(type="string", example="Smartphone")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of products per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */

    public function search(Request $request)
    {
        // Valider les paramètres de recherche
        $request->validate([
            'query' => 'required|string|max:255',
        ]);

        $query = $request->get('query');

        // Rechercher dans le nom ou la description
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->with('categories')
            ->paginate($request->get('per_page', 5));

        return ProductResource::collection($products);
    }
}
