<?php

namespace App\Services;

class Product {

    protected $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function findById($id){
        $products = $this->client->get($this->url('/products'),[
            'ids' => $id
        ]);

        return $products;
    }

    protected function url($path){
        return rtrim(env('PRODUCTS_SERIVCE_URL'),'/') . '/' . ltrim($path, '/');
    }
}