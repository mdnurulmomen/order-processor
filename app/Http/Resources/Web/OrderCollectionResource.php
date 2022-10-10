<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderCollectionResource extends JsonResource
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
            'merchant_id' => $this->merchant_id,
            'merchant_name' => $this->when($this->relationLoaded('merchant'), $this->merchant->name),
            'is_collected' => $this->is_collected,
            'collected_at' => $this->collected_at,
            'created_at' => $this->created_at,
        ];
    }
}
