<?php

namespace App\Http\Controllers;

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
 *
 * @OA\SecurityScheme(
 *     securityScheme="BearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use the Bearer token for authentication"
 * )
 */

abstract class Controller
{
    //
}
