<?php

namespace App\Models;

use App\Models\Addon;
use App\Models\ItemVariation;
use App\Models\RestaurantMenuCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuItem extends Model
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'has_variation' => 'boolean',
        'has_addon' => 'boolean',
        'customizable' => 'boolean',
        'item_stock' => 'boolean',
    ];

    public function restaurantMenuCategory()
    {
        return $this->belongsTo(RestaurantMenuCategory::class, 'restaurant_menu_category_id', 'id');
    }

    // pivot tables doesn't follow soft delete rules
    public function restaurantMenuItemVariations()
    {
        return $this->belongsToMany(ItemVariation::class, 'restaurant_menu_item_variations', 'restaurant_menu_item_id', 'variation_id')->withPivot('price', 'deleted_at');
    }

    // pivot tables doesn't follow soft delete rules
    public function restaurantMenuItemAddons()
    {
        return $this->belongsToMany(Addon::class, 'restaurant_menu_item_addons', 'restaurant_menu_item_id', 'addon_id')->withPivot('price', 'deleted_at');
    }
}
