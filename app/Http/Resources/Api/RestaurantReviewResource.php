<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $customer = $this->customer;

        return [
            'rating' => $this->rating,
            'order_id' => $this->order_id,
            'customer_id' => $this->customer_id,
            'customer_name' => ($customer->first_name || $customer->last_name) ? ($customer->first_name.' '.$customer->last_name) : $customer->user_name ? $customer->user_name : 'User',
            'comment' => $this->when($this->comment, $this->comment),
        ];
    }
}
