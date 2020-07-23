<?php

namespace App\Models;

use App\Models\Cuisine;
use Illuminate\Database\Eloquent\Model;

class RestaurantCuisine extends Model
{
   	public $timestamps = false;

   	public function cuisine()
   	{
   		return $this->belongsTo(Cuisine::class, 'cuisine_id', 'id');
   	}
}
