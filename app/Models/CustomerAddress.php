<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function favourites()
    {
    	return $this->hasMany(CustomerFavourite::class, 'customer_address_id', 'id');
    }
}
