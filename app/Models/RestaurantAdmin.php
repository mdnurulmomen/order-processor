<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantAdmin extends Model
{
   	protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
