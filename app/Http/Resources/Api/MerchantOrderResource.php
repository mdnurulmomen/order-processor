<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [

            'id' => $this->id,
            'merchant_id' => $this->merchant_id,
            'merchant_name' => $this->merchant->name,
            'is_accepted' => $this->is_accepted,
            'accepted_at' => $this->accepted_at,
            'is_ready' => $this->is_ready,
            'ready_at' => $this->ready_at,
            'order_id' => $this->order_id,
            'net_price' => $this->net_price,
            'is_free_delivery' => $this->is_free_delivery,
            'is_self_delivery' => $this->is_self_delivery,
            'is_rider_available' => $this->is_rider_available,
            'applied_sale_percentage' => $this->applied_sale_percentage,
            'is_payment_settled' => $this->is_payment_settled,
            'order' => new OrderResource($this->whenLoaded('order')),
            'products' => ProductOrderResource::collection($this->whenLoaded('products')),
            'order_serve_confirmation' => /*$this->when($this->serve, $this->serve)*/ $this->whenLoaded('serve'),
            'created_at' => $this->created_at

        ];
    }
}
