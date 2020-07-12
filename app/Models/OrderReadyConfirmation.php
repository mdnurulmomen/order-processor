<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;

class OrderReadyConfirmation extends Model
{
	protected $guarded = [
		'id'
	];

	public function order()
	{
		return $this->belongsTo(Order::class, 'order_id', 'id');
	}

	public function restaurant()
	{
		return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
	}
}
