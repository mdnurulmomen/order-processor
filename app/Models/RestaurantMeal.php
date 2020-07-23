<?php

namespace App\Models;

use App\Models\Meal;
use Illuminate\Database\Eloquent\Model;

class RestaurantMeal extends Model
{
	public function meal()
	{
		return $this->belongsTo(Meal::class, 'meal_id', 'id');
	}
}
