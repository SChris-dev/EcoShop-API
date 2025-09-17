<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product_name' => $this->when($this->relationLoaded('product'), $this->product->name),
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'total_price' => (float) $this->getTotalPrice(),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
