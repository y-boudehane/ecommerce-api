<?php

namespace App;

use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;

class LowStockNotifier
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function __invoke()
    {
        $products = Product::where('stock', '<', 10)->get();
        if ($products) {
            User::all()->each(function ($user) use ($products) {
                $user->notify(new LowStockNotification($products));
            });

        }
    }

}
