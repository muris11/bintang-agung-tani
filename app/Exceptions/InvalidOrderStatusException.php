<?php

namespace App\Exceptions;

use App\Models\Order;
use Exception;

class InvalidOrderStatusException extends Exception
{
    public Order $order;

    public string $currentStatus;

    public string $attemptedStatus;

    public function __construct(Order $order, string $attemptedStatus)
    {
        $this->order = $order;
        $this->currentStatus = $order->status;
        $this->attemptedStatus = $attemptedStatus;

        parent::__construct(
            "Cannot change order status from '{$order->getStatusLabel()}' to '{$attemptedStatus}'. " .
            "Invalid status transition."
        );
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
