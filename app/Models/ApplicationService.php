<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as ImageIntervention;

class ApplicationService extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id' 
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public $timestamps = false;

    public function setLogoIconAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/application/services/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.$this->code.'-logo.png');

            $this->attributes['logo'] = $directory.$this->code.'-logo.png';
        }
    }
}
