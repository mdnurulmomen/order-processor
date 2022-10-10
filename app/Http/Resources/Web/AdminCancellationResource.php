<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminCancellationResource extends JsonResource
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
            'admin_id'=>$this->canceller_id, 
            'admin_name'=>$this->canceller->first_name.' '.$this->canceller->last_name, 
            'cancellation_reason_id'=>$this->cancellation_reason_id,  
            'cancellation_reason' => $this->whenLoaded('cancellationReason'),
        ];
    }
}
