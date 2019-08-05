<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Services\Product as ProductService;
use App\Product;

class CreateOrderTest extends TestCase
{
    use DatabaseMigrations;

    public function test_it_create_order_using_products_table()
    {

        $this->withoutExceptionHandling();

        $products = factory(\App\Product::class, 2)->create();

        $this->json('POST', '/orders', [
            'items' => $products->pluck('id')->toArray()
        ])->seeStatusCode(201);

        $this->seeInDatabase('order_items', [
            'order_id' => \App\Order::latest()->first()->id,
            'product_id' => $products->get(0)->id
        ]);

        $this->seeInDatabase('order_items', [
            'order_id' => \App\Order::latest()->first()->id,
            'product_id' => $products->get(1)->id
        ]);
    }

    public function test_it_create_order_using_products_service()
    {
        $mock = \Mockery::mock(ProductService::class);
        $mock->shouldReceive('findById')->with([1, 2])->andReturn([
            'data' => [
                [
                    'id' => 1,
                ],
                [
                    'id' => 2,
                ]
            ]
        ]);

        $this->app->instance(ProductService::class, $mock);

        $this->json('POST', '/orders-http', [
            'items' => [1, 2]
        ])->seeStatusCode(201);

        $this->seeInDatabase('order_items', [
            'order_id' => \App\Order::latest()->first()->id,
            'product_id' => 1
        ]);

        $this->seeInDatabase('order_items', [
            'order_id' => \App\Order::latest()->first()->id,
            'product_id' => 2
        ]);
    }

    public function test_it_return_orders_with_database_item_product()
    {
        $order = factory(\App\Order::class)->create();

        $product = factory(Product::class)->create([
            'id' => 1,
            'name' => 'T-shirt',
        ]);

        $orderItem = factory(\App\OrderItem::class)->make([
            'product_id' => $product->id
        ])->toArray();

        $orderItem = $order->items()->create($orderItem);

        $this->json('GET', '/orders')->seeStatusCode(200);

        $json = (array)@json_decode($this->response->getContent(), true);

        $this->assertEquals(1, array_get($json, 'data.0.id'));

        $this->assertEquals(1, array_get($json, 'data.0.items.0.id'));

        $this->assertEquals(1, array_get($json, 'data.0.items.0.product.id'));

        $this->assertEquals('T-shirt', array_get($json, 'data.0.items.0.product.name'));
    }

    public function test_it_return_orders_with_products_from_external_service()
    {
        $mock = \Mockery::mock(ProductService::class);
        $mock->shouldReceive('findById')->with([1])->andReturn([
            'data' => [
                [
                    'id' => 1,
                    'name' => 'T-shirt'
                ],
            ]
        ]);

        $this->app->instance(ProductService::class, $mock);
        
        $order = factory(\App\Order::class)->create();

        $orderItem = factory(\App\OrderItem::class)->make([
            'product_id' => 1
        ])->toArray();

        $orderItem = $order->items()->create($orderItem);

        $this->json('GET', '/orders-http')->seeStatusCode(200);

        $json = (array)@json_decode($this->response->getContent(), true);

        $this->assertEquals(1, array_get($json, 'data.0.id'));

        $this->assertEquals(1, array_get($json, 'data.0.items.0.id'));

        $this->assertEquals(1, array_get($json, 'data.0.items.0.product.id'));

        $this->assertEquals('T-shirt', array_get($json, 'data.0.items.0.product.name'));
    }

}
