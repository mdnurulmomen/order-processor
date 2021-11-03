<?php

namespace App\Models;

use App\Models\Cuisine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantCuisine extends Model
{
   	use SoftDeletes;

   	public $timestamps = false;

   	public function cuisine()
   	{
   		return $this->belongsTo(Cuisine::class, 'cuisine_id', 'id')->withTrashed();
   	}
}
