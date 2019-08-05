<?php

use Illuminate\Http\Request;
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

$router->post('/products', function (Request $request) {

    $this->validate($request, [
        'name' => 'required'
    ]);

    $product = Product::create($request->only(['name', 'option1', 'option2']));

    publish('create', [
        'product' => $product->toArray()
    ]);
    
    return response()->json([
        'message' => 'product created',
        'product' => $product->toArray()
    ], 201);
});


$router->get('/products', function (Request $request) {

    $query = Product::query();

    $ids = $request->input('ids');
    if(is_array($ids) && count($ids)){
        $query->where('id', array_flatten($ids));
    }

    return response()->json($query->paginate(20), 201);
});

