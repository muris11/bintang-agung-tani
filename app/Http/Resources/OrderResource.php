<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'subtotal' => $this->subtotal,
            'shipping_cost' => $this->shipping_cost,
            'total_amount' => $this->total_amount,
            'shipping_address' => $this->shipping_address,
            'shipping_phone' => $this->shipping_phone,
            'notes' => $this->notes,
            'shipping_courier' => $this->shipping_courier,
            'tracking_number' => $this->tracking_number,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'user' => new UserResource($this->whenLoaded('user')),
            'status_histories' => OrderStatusHistoryResource::collection($this->whenLoaded('statusHistories')),
        ];
    }
}
