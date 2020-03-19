<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as ImageIntervention;

class ApplicationSetting extends Model
{
   	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id' 
    ];

    public function setFaviconIconAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/admin/images/application/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.$this->id.'.png');

            $this->attributes['favicon'] = $directory.$this->id.'.png';
        }
    }
}
