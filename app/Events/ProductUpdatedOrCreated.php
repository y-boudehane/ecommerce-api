<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdatedOrCreated
{
    use Dispatchable, SerializesModels;

    public $product;
    public $action;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Product $product, string $action)
    {
        $this->product = $product;
        $this->action = $action; // 'created' or 'updated'
    }
}
