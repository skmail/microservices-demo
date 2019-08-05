<?php
use Illuminate\Http\Request;
use App\Services\Product as ProductService;
use App\Product;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

$router->post('/orders', function (Request $request) {

    $executionStartTime = microtime(true);

    $this->validate($request, [
        'items' => 'required|array|min:1',
        'items.*' => 'required|numeric|exists:products,id'
    ]);

    $order = \App\Order::create();

    foreach ($request->input('items') as $productId) {
        $order->items()->create([
            'product_id' => $productId
        ]);
    }

    return response()->json([
        'message' => 'Order Created',
        'order' => $order->load('items', 'items.product'),
        'time' => microtime(true) - $executionStartTime
    ], 201);
});

$router->post('/orders-http', function (Request $request, ProductService $productService) {

    $executionStartTime = microtime(true);

    $this->validate($request, [
        'items' => 'required|array|min:1',
        'items.*' => 'required|numeric'
    ]);

    $items = $request->input('items');

    $products = $productService->findById($items);

    if (count($products['data']) !== count($items)) {
        return response()->json([
            'errors' => [
                'items' => 'Invalid products'
            ]
        ], 422);
    }

    $order = \App\Order::create();

    foreach ($request->input('items') as $productId) {
        $order->items()->create([
            'product_id' => $productId
        ]);
    } 

    $order->load('items');

    lazyLoadOrderItemsProductsFromExternalService($order, $productService);

    return response()->json([
        'message' => 'Order Created',
        'order' => $order,
        'time' => microtime(true) - $executionStartTime
    ], 201);
});

$router->get('/orders', function (Request $request) {

    $executionStartTime = microtime(true);

    $orders = \App\Order::with(['items', 'items.product'])->paginate();

    $orders = $orders->toArray();

    $orders['time'] = microtime(true) - $executionStartTime;

    return response()->json($orders);
});

$router->get('/orders-http', function (Request $request, ProductService $productService) {

    $executionStartTime = microtime(true);

    $orders = \App\Order::with(['items'])->paginate();

    lazyLoadOrdersItemsProductsFromExternalService($orders, $productService);

    $orders = $orders->toArray();

    $orders['time'] = microtime(true) - $executionStartTime;

    return response()->json($orders);
});