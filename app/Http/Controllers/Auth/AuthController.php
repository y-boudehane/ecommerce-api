<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     summary="Login a user and retrieve an API token",
     *     description="This endpoint allows a user to log in with their email and password, and returns an API token upon successful authentication.",
     *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="multipart/form-data",
     *                 @OA\Schema(
     *                     type="object",
     *                     required={"email", "password"},
     *                     @OA\Property(property="email", type="string", example="test@example.com"),
     *                     @OA\Property(property="password", type="string", example="password123")
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your-api-token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The email field is required.")
     *         )
     *     )
     * )
     */

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json(['token' => $user->createToken('api_token')->plainTextToken]);
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     summary="Register a new user and get an API token",
     *     description="This endpoint allows a new user to register by providing their name, email, and password. It returns an API token upon successful registration.",
     *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="multipart/form-data",
     *                 @OA\Schema(
     *                     type="object",
     *                     required={"name", "email", "password","password_confirmation" },
     *                     @OA\Property(property="name", type="string", example="user"),
     *                     @OA\Property(property="email", type="string", example="user@example.com"),
     *                     @OA\Property(property="password", type="string", example="password123"),
     *                     @OA\Property(property="password_confirmation", type="string", example="password123")
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your-api-token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The email has already been taken.")
     *         )
     *     )
     * )
     */

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        return response()->json(['token' => $user->createToken('api_token')->plainTextToken], 201);
    }
}
