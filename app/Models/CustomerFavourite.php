<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFavourite extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function merchant()
    {
    	return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }
}
