<?php

namespace App\Listeners;

use App\Events\ExampleEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Product;

class ProductCreated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    /**
     * Handle the event.
     *
     * @param  array  $payload
     * @return void
     */
    public function handle($payload)
    {
        $product = $payload['product'];

        Product::updateOrCreate(
            [
                'id' => $product['id']
            ],
            [
                'id' => $product['id'],
                'name' => $product['name']
            ]
        );
    }
}
