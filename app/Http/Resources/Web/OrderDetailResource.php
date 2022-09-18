<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'order_type' => $this->order_type,
            'order_price' => $this->order_price,
            'vat' => $this->vat,
            'discount' => $this->discount,
            'delivery_fee' => $this->when($this->delivery_fee, $this->delivery_fee),
            'net_payable' => $this->net_payable,
            'payment_method' => $this->payment_method,
            'payment' => $this->when($this->payment_method != 'cash', $this->payment ? $this->payment : 'cash'),
            'orderer' => $this->orderer,
            'is_asap_order' => $this->is_asap_order,
            'schedule' => /*$this->whenLoaded('schedule')*/ $this->when($this->is_asap_order, $this->schedule ? $this->schedule->order_schedule : false),
            'has_cutlery' => /*$this->whenLoaded('cutlery')*/ $this->has_cutlery,
            'address' => $this->when($this->address, $this->address ? $this->address->additional_info : false),
            // 'reservation' => $this->reservation,
            'customer_confirmation' => $this->customer_confirmation,
            'in_progress' => $this->in_progress,
            'complete_order' => $this->complete_order,
            'merchants' => MerchantOrderResource::collection($this->merchants) /*route('api.v1.ordered-merchants.show', ['order' => $this->id])*/,
            'rider_assigned' => $this->riderAssigned,
            'collections' => OrderCollectionResource::collection($this->collections),
            // 'rider_delivery_return' => $this->riderDeliveryReturn ? new RiderReturnResource($this->riderDeliveryReturn) : null,
            'order_serve_confirmation' => $this->when($this->serve, $this->serve),
            'customer_order_cancelation' => $this->when($this->customer_confirmation==0, $this->customerOrderCancelation),
            'merchant_order_cancelations' => $this->when($this->merchantOrderCancelations->count(), MerchantCancelationResource::collection($this->merchantOrderCancelations)),
            'admin_order_cancelation' => $this->when($this->adminOrderCancelation()->exists(), $this->adminOrderCancelation),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
