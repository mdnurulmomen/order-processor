<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MyRegularResource extends JsonResource
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
            'id'=>$this->id,
            'package'=>$this->package,
            'time'=>$this->time,
            'days'=>$this->days,
            'delivery_address_id'=>$this->delivery_address_id,
            'merchants' => MyRegularMerchantResource::collection($this->merchants),
        ];
    }
}
