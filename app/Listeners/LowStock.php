<?php

namespace App\Listeners;

use App\Events\ProductUpdatedOrCreated;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class LowStock
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
        $product = $event->product;
        if ($product->stock < 10) {
            User::all()->each(function ($user) use ($product) {
                $user->notify(new LowStockNotification($product));
            });

        }

    }
}
