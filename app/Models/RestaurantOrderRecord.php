<?php

namespace App\Models;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrderRecord extends Model
{
	protected $guarded = [
		'id'
	];

	public $timestamps = false;

   	public function restaurant()
	{
		return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
	}
}
