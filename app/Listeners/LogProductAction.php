<?php

namespace App\Listeners;

use App\Events\ProductUpdatedOrCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class LogProductAction
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductUpdatedOrCreated $event): void
    {
        $action = $event->action; // 'created' or 'updated'
        $product = $event->product;

        Log::info("Product {$action}: ", [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
        ]);
    }
}
