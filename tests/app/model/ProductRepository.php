<?php

namespace App\Model;


class ProductRepository
{

    const PRODUCT_DEFAULT_ID = 1,
        PRODUCT_DEFAULT_PRICE = 45;

    public function getProduct($id = 1, $price = 10)
    {
        $product = new \stdClass();
        $product->id = $id;
        $product->price = $price;
        $product->title = 'Product ' . $id . '- title';
        $product->description = 'Product ' . $id . ' - description';

        return $product;
    }

}