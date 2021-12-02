<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRestaurant extends Model
{
	protected $guarded = [
		'id'
	];

	public $timestamps = false;

	public function items()
	{
		return $this->hasMany(OrderItem::class, 'order_restaurant_id', 'id');
	}

	public function order()
	{
		return $this->belongsTo(Order::class, 'order_id', 'id');
	}

	public function restaurant()
	{
		return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id')->withTrashed();
	}
}
