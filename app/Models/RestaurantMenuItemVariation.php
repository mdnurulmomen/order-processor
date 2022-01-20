<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuItemVariation extends Model
{
   	use SoftDeletes;

   	public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

    public function orders()
    {
        return $this->hasMany(OrderItemVariation::class, 'restaurant_menu_item_variation_id', 'id');
    }

    public function restaurantMenuItem()
    {
        return $this->belongsTo(RestaurantMenuItem::class, 'restaurant_menu_item_id', 'id')->withTrashed();
    }

   	public function variation()
    {
        return $this->belongsTo(ItemVariation::class, 'variation_id', 'id')->withTrashed();
    }
}
