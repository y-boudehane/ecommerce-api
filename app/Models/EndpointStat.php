<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EndpointStat extends Model
{
    protected $fillable = ['endpoint', 'method', 'count', 'error_count', 'success_count'];
}
