<?php

namespace App\Http\Resources\Web;

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
            'ringing_started_at' => $this->ringing_started_at,
            'answered_at' => $this->answered_at,
            'is_ready' => $this->is_ready,
            'ready_at' => $this->ready_at,
            'order_id' => $this->order_id,
            'net_price' => $this->net_price,
            'in_progress' => $this->in_progress,
            'is_completed' => $this->is_completed,
            'is_free_delivery' => $this->is_free_delivery,
            'is_self_delivery' => $this->is_self_delivery,
            'is_rider_available' => $this->is_rider_available,
            'applied_sale_percentage' => $this->applied_sale_percentage,
            'is_payment_settled' => $this->is_payment_settled,
            'self_delivery' => $this->whenLoaded('selfDelivery'),
            'order' => new OrderResource($this->whenLoaded('order')),
            'products' => ProductOrderResource::collection($this->whenLoaded('products')),
            'taking_confirmation' => /*$this->when($this->serve, $this->serve)*/ $this->whenLoaded('take'),
            'order_serve_confirmation' => /*$this->when($this->serve, $this->serve)*/ $this->whenLoaded('serve'),
            'created_at' => $this->created_at
            
        ];
    }
}
