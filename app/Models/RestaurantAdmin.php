<?php

namespace App\Models;

use App\Models\Restaurant;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class RestaurantAdmin extends Authenticatable
{
    use Notifiable, SoftDeletes;
    
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
