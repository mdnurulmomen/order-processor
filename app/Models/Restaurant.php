<?php

namespace App\Models;

use App\Models\Meal;
use App\Models\Waiter;
use App\Models\Cuisine;
use App\Models\Kitchen;
use App\Models\MenuCategory;
use App\Models\RestaurantDeal;
use App\Models\RestaurantAdmin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Intervention\Image\ImageManagerStatic as ImageIntervention;

class Restaurant extends Authenticatable
{
   	use Notifiable, SoftDeletes;

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
        'is_post_paid' => 'boolean',
        'is_self_service' => 'boolean',
        'has_parking' => 'boolean',
        'taking_order' => 'boolean',
        'admin_approval' => 'boolean',
    ];
    
   
    public function setBannerPreviewAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/restaurant/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.$this->id.'.jpg');

            $this->attributes['banner_preview'] = $directory.$this->id.'.jpg';
        }
    }

    public function restaurantAdmin()
    {
        return $this->belongsTo(RestaurantAdmin::class, 'restaurant_admins_id', 'id');
    }

    public function restaurantCuisines()
    {
        return $this->belongsToMany(Cuisine::class, 'restaurant_cuisines', 'restaurant_id', 'cuisine_id');
    }

    public function restaurantMenuCategories()
    {
        return $this->belongsToMany(MenuCategory::class, 'restaurant_menu_categories', 'restaurant_id', 'menu_category_id')->withPivot('id', 'serving_from', 'serving_to');
    }

    public function restaurantMealCategories()
    {
        return $this->belongsToMany(Meal::class, 'restaurant_meals', 'restaurant_id', 'meal_id');
    }

    public function kitchen()
    {
        return $this->hasOne(Kitchen::class, 'restaurant_id', 'id');
    }

    public function waiters()
    {
        return $this->hasMany(Waiter::class, 'restaurant_id', 'id');
    }

    public function deal()
    {
        return $this->hasOne(RestaurantDeal::class, 'restaurant_id', 'id');
    }   
}
