<?php

function publish($routingkey, $message)
{
    $segments = explode(':', $routingkey);

    if(count($segments) < 2){
        $queue = env('MESSAGE_QUEUE_NAME');
    }else{
        $queue = $segments[0]; 
        unset($segments[0]);
        $routingkey = implode(':', $segments);
    }

    return Amqp::publish($routingkey, is_string($message) ? $message : json_encode($message),[
        'queue' => $queue
    ]);
}

function lazyLoadOrderItemsProductsFromExternalService($order, $productService){

    $productIds = $order->items->pluck('product_id')->toArray();

    $products = collect($productService->findById($productIds)['data'])->keyBy('id');

    $order->items->each(function ($item) use ($products) {
        $item->product = $products->get($item->product_id);
    });
}

function lazyLoadOrdersItemsProductsFromExternalService($orders, $productService){

    $productIds = $orders->pluck('items.*.product_id')->flatten()->toArray();

    $products = collect($productService->findById($productIds)['data'])->keyBy('id');

    foreach ($orders as $order) {
        $order->items->each(function ($item) use ($products) {
            $item->product = $products->get($item->product_id);
        });
    }
}