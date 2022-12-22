<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiderDelivery extends Model
{
    protected $guarded = [
		'id'
	];

	public $timestamps = false;

	// protected $appends = ['acceptance_timeout'];

	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

	public function address()
	{
	 	return $this->hasOne(DeliveryAddress::class, 'order_id', 'order_id');
	}

	public function merchants()
	{
		return $this->hasMany(MerchantOrder::class, 'order_id', 'order_id')
		->where('is_self_delivery', 0);
	}

	public function merchantsAccepted()
	{
		return $this->hasMany(MerchantOrder::class, 'order_id', 'order_id')
		->where('is_self_delivery', 0)
		->where('is_accepted', 1);
	}

	public function collections()
	{
		return $this->hasMany(RiderCollection::class, 'order_id', 'order_id');
	}

	public function orderCancellations()
	{
		return $this->hasMany(OrderCancellation::class, 'order_id', 'order_id');
	}

	/*
	public function getAcceptanceTimeoutAttribute()
	{
	    $timeToDelay = ApplicationSetting::firstOrCreate(['id' => 1])->rider_call_receiving_time;
    	return $this->created_at->diffInSeconds(now()) > $timeToDelay ? true : false;
	}
	*/

	public function merchantOrderCancellations()
   	{
      	return $this->orderCancellations()->where('canceller_type', 'App\Models\Merchant')
      	->whereHas('order.merchants', function ($query) {
		 	$query->where('is_self_delivery', 0);
		});;
   	}

   	public function riderOrderCancellations()
   	{
      return $this->orderCancellations()->where('canceller_type', 'App\Models\Rider');
   	}

   	public function order()
	{
		return $this->belongsTo(Order::class, 'order_id', 'id');
	}

	public function rider()
	{
		return $this->belongsTo(Rider::class, 'rider_id', 'id');
	}
}
