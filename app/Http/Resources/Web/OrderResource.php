<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'type' => $this->type,
            'is_asap_order' => $this->is_asap_order,
            'payment_method' => $this->payment_method,
            'has_cutlery' => $this->has_cutlery,
            'orderer' => $this->whenLoaded('orderer'),
            'customer_confirmation' => $this->customer_confirmation,
            'in_progress' => $this->in_progress,    // -1 for not confirmed, 1 for active orders, 0 for dismissed
            'is_completed' => $this->is_completed,    // -1 for not confirmed, 1 for active orders, 0 for dismissed
            'merchants' => MerchantOrderResource::collection($this->whenLoaded('merchants')),
            'payment' => /*$this->when($this->payment_method != 'cash', $this->payment ? $this->payment : 'cash')*/ $this->whenLoaded('payment'),
            'schedule' => $this->whenLoaded('schedule') /*$this->when(! $this->is_asap_order, $this->schedule ? $this->schedule->order_schedule : false)*/,
            'address' => $this->whenLoaded('address') /*$this->when($this->address, $this->address ? $this->address->additional_info : false)*/,
            // 'restaurants' => OrderedRestaurantResource::collection($this->restaurants) /*route('api.v1.ordered-restaurants.show', ['order' => $this->id])*/,
            'rider_assigned' => new RiderDeliveryResource($this->whenLoaded('riderAssigned')),
            'collections' => OrderCollectionResource::collection($this->whenLoaded('collections')),
            // 'rider_delivery_return' => $this->riderDeliveryReturn ? new RiderReturnResource($this->riderDeliveryReturn) : null,
            'order_serve_confirmation' => /*$this->when($this->serve, $this->serve)*/ $this->whenLoaded('serve'),
            'customer_order_cancellation' => $this->when($this->customer_confirmation==0, $this->customerOrderCancellation),
            'merchant_order_cancellations' => MerchantCancellationResource::collection($this->whenLoaded('merchantOrderCancellations')),
            'admin_order_cancellation' => new AdminCancellationResource($this->whenLoaded('adminOrderCancellation')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
