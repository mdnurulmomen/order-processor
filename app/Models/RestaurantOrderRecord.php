<?php

namespace App\Models;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrderRecord extends Model
{
   	public function restaurant()
	{
		return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
	}
}
