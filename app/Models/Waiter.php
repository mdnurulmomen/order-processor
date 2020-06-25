<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Waiter extends Model
{
   	use SoftDeletes;

   	protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'admin_approval' => 'boolean',
    ];
    
   	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

   	public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

    /**
     * Get all of the order's.
     */
    public function orders()
    {
        return $this->morphMany(Order::class, 'orderer');
    }
}
