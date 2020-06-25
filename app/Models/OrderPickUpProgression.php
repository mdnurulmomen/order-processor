<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Rider;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;

class OrderPickUpProgression extends Model
{
	public function order()
	{
		return $this->belongsTo(Order::class, 'order_id', 'id');
	}

   	public function rider()
	{
		return $this->belongsTo(Rider::class, 'rider_id', 'id');
	}

	public function restaurant()
	{
		return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
	}
}
