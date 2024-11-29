<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="EndpointStat",
 *     type="object",
 *     title="EndpointStat",
 *     description="Model for storing endpoint statistics",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="endpoint", type="string", example="/api/products"),
 *     @OA\Property(property="hits", type="integer", example=150),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-29T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-29T10:00:00Z")
 * )
 */

class EndpointStat extends Model
{
    protected $fillable = ['endpoint', 'method', 'count', 'error_count', 'success_count'];
}
