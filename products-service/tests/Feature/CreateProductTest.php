<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class CreateProductTest extends TestCase
{
    // use DatabaseMigrations;

    public function test_it_create_a_product()
    {
        $this->withoutExceptionHandling();
        
        $this->json('POST', '/products', [
            'name' => 'Tshirt'
        ])->seeStatusCode(201);

        $this->seeInDatabase('products', ['name' => 'Tshirt']);
    }
}