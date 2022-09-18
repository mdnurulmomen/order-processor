<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantCancelationResource extends JsonResource
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
            'reason_id'=>$this->reason_id,  
            'merchant_id'=>$this->canceller_id, 
            'merchant_name'=>$this->canceller->name, 
        ];
    }
}
