<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cuisine extends Model
{
	use SoftDeletes;
	
	public $timestamps = false;   
    protected $guarded = ['id'];

    protected $casts = [
		'search_preference' => 'boolean',
	];
}
