<?php

namespace App\Exceptions;

use Exception;

class PaymentVerificationException extends Exception
{
    public int $paymentProofId;

    public ?string $reason;

    public function __construct(int $paymentProofId, string $message, ?string $reason = null)
    {
        $this->paymentProofId = $paymentProofId;
        $this->reason = $reason;

        parent::__construct($message);
    }

    public function getPaymentProofId(): int
    {
        return $this->paymentProofId;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }
}
