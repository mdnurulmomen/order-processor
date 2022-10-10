<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantCancellationResource extends JsonResource
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
            'merchant_id'=>$this->canceller_id, 
            'merchant_name'=>$this->canceller->name, 
            'cancellation_reason_id'=>$this->cancellation_reason_id,  
            'cancellation_reason' => $this->whenLoaded('cancellationReason'),
        ];
    }
}
