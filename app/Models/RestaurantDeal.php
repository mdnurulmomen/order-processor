<?php

namespace App\Models;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantDeal extends Model
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
        'delivery_fee_addition' => 'boolean',
    ];

    public function restaurant()
    {
    	return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

}
