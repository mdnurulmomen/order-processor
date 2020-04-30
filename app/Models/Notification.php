<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as ImageIntervention;

class Notification extends Model
{
   	protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    public function setBannerAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/notification/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.$this->title.'.jpg');

            $this->attributes['banner'] = $directory.$this->title.'.jpg';
        }
    }
}
