<?php

namespace App\Models;

use App\Models\RestaurantMenuCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuCategory extends Model
{
   	use SoftDeletes;
   	
   	public $timestamps = false;
   	protected $guarded = ['id'];

   	public function restaurantMenuCategories()
   	{
   		return $this->hasMany(RestaurantMenuCategory::class, 'menu_category_id', 'id');
   	}
}
