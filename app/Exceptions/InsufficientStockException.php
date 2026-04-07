<?php

namespace App\Exceptions;

use App\Models\Product;
use Exception;

class InsufficientStockException extends Exception
{
    public Product $product;

    public int $requested;

    public int $available;

    public function __construct(Product $product, int $requested)
    {
        $this->product = $product;
        $this->requested = $requested;
        $this->available = $product->stock;

        parent::__construct(
            "Stock tidak mencukupi untuk {$product->name}. " .
            "Tersedia: {$product->stock}, diminta: {$requested}"
        );
    }

    /**
     * Get the product associated with this exception.
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * Get the requested quantity.
     */
    public function getRequested(): int
    {
        return $this->requested;
    }

    /**
     * Get the available stock.
     */
    public function getAvailable(): int
    {
        return $this->available;
    }
}
