<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EndpointStat;

class StatController extends Controller
{
    /**
     * @OA\Get(
     *     path="/stats",
     *     operationId="getAllStats",
     *     tags={"Stats"},
     *     summary="Retrieve all endpoint statistics",
     *     description="Get a list of all statistics for endpoints.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/EndpointStat"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function index()
    {
        $stats = EndpointStat::all();
        return response()->json($stats);
    }

    /**
     * @OA\Get(
     *     path="/stats/search",
     *     operationId="searchStatsByEndpoint",
     *     tags={"Stats"},
     *     summary="Retrieve statistics for a specific endpoint",
     *     description="Get statistics for a specific endpoint by sending the endpoint name in the request body.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"endpoint"},
     *             @OA\Property(property="endpoint", type="string", example="api/products")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/EndpointStat"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No stats found for the given endpoint"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request: endpoint is required"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string|max:255',
        ]);

        $endpoint = $request->input('endpoint');

        $stats = EndpointStat::where('endpoint', 'LIKE', "%{$endpoint}%")->get();

        if ($stats->isEmpty()) {
            return response()->json(['message' => 'No stats found for the given endpoint'], 404);
        }

        return response()->json($stats);
    }

}
