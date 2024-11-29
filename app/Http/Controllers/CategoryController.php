<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="API endpoints for managing categories and fetching related products"
 * )
 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     tags={"Categories"},
     *     summary="List all categories",
     *     description="Fetch a list of all categories",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="categories",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Category")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        return response()->json(['categories' => CategoryResource::collection($categories)]);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{categoryId}/products",
     *     tags={"Categories"},
     *     summary="Get products by category",
     *     description="Fetch a paginated list of products associated with a specific category",
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         description="ID of the category",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while fetching products")
     *         )
     *     )
     * )
     */

    public function getProductsByCategory($categoryId)
    {
        try {
            $category = Category::find($categoryId);
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            $products = $category->products()->with('categories')->paginate();

            return response()->json(['products' => ProductResource::collection($products)], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching products'], 500);
        }


    }

}
