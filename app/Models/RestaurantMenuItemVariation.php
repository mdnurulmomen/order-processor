<?php

namespace App\Models;

use App\Models\ItemVariation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuItemVariation extends Model
{
   	use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

   	public $timestamps = false;

   	public function variation()
    {
        return $this->belongsTo(ItemVariation::class, 'variation_id', 'id');
    }
}
