<?php

namespace App\Exceptions;

use Exception;

class CartOperationException extends Exception
{
    public ?int $cartId;

    public ?int $userId;

    public function __construct(string $message, ?int $cartId = null, ?int $userId = null)
    {
        $this->cartId = $cartId;
        $this->userId = $userId;

        parent::__construct($message);
    }
}
