<?php

namespace App\Http\Resources\Api;

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
            'merchant_name' => $this->merchant->name,
            'collection_confirmation' => $this->collection_confirmation,
        ];
    }
}