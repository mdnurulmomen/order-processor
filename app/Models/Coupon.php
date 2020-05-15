<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
   	protected $guarded = ['id'];

   	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'durability' => 'boolean',
        'status' => 'boolean',
    ];
}
