<?php

namespace App\Models;

use App\Models\Addon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuItemAddon extends Model
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

    public function addon()
    {
        return $this->belongsTo(Addon::class, 'addon_id', 'id')->withTrashed();
    }
}
