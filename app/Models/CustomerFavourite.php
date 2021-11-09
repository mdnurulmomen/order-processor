<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFavourite extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function restaurant()
    {
    	return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }
}
