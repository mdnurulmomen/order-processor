<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiderDelivery extends Model
{
    protected $guarded = [
		'id'
	];

	// public $timestamps = false;

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
		return $this->hasMany(MerchantOrder::class, 'order_id', 'order_id');
	}

	public function merchantsAccepted()
	{
		return $this->hasMany(MerchantOrder::class, 'order_id', 'order_id')->where('order_acceptance', 1);
	}

	public function collections()
	{
		return $this->hasMany(RiderCollection::class, 'order_id', 'order_id');
	}

	public function orderCancelations()
	{
		return $this->hasMany(OrderCancelation::class, 'order_id', 'order_id');
	}

	public function merchantOrderCancelations()
   	{
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Merchant');
   	}

   	public function riderOrderCancelations()
   	{
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Rider');
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