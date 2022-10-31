<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RiderOrderResource extends JsonResource
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
            'is_asap_order' => $this->is_asap_order,
            'schedule' => $this->whenLoaded('schedule'),
            'has_cutlery' => $this->has_cutlery,
            'payment_method' => $this->payment_method=='cash' ? 'Not-paid' : 'Paid',
            'orderer' => $this->orderer,
            'created_at' => $this->created_at,
        ];
    }
}
