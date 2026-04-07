<?php

namespace App\Exceptions;

use App\Models\Product;
use Exception;

class InvalidOrderQuantityException extends Exception
{
    public Product $product;

    public int $quantity;

    public ?int $minOrder;

    public ?int $maxOrder;

    public function __construct(Product $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->minOrder = $product->min_order;
        $this->maxOrder = $product->max_order;

        $message = "Invalid order quantity for {$product->name}. ";

        if ($product->min_order && $quantity < $product->min_order) {
            $message .= "Minimal pembelian {$product->min_order} {$product->unit}";
        } elseif ($product->max_order && $quantity > $product->max_order) {
            $message .= "Maksimal pembelian {$product->max_order} {$product->unit}";
        } else {
            $message .= "Quantity {$quantity} is not valid.";
        }

        parent::__construct($message);
    }
}
