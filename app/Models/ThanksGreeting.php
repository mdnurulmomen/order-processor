<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanksGreeting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

    public $timestamps = false;

    protected $casts = [
		'publish' => 'boolean',
	];
}
