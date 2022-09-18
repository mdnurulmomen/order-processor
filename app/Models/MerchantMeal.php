<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantMeal extends Model
{
    use SoftDeletes;

	public $timestamps = false;

	public function meal()
	{
		return $this->belongsTo(Meal::class, 'meal_id', 'id')->withTrashed();
	}
}
