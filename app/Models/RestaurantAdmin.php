<?php

namespace App\Models;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantAdmin extends Model
{
    use SoftDeletes;
    
   	protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'restaurant_admins_id', 'id')->withTrashed();
    }
}
