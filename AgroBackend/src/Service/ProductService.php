<?php

namespace App\Service;

use App\Entity\Product;

class ProductService
{
    /**
     * Maps a Product entity to a JSON response format.
     *
     * @param Product $product
     * @return array
     */
    public function mapToJson(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'price' => $product->getPrice(),
            'image' => $product->getImage(),
        ];
    }
}
