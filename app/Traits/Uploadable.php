<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * 
 */
trait Uploadable
{
	public function uploadImage(UploadedFile $uploadedFile, $filename = null, $folder = null, $disk = 'public')
	{
		$filename = !is_null($filename) ? $filename : Str::random(25);

		$path = $uploadedFile->storeAs(
		    $folder.'/', $filename.'.'.$uploadedFile->getClientOriginalExtension(), $disk
		);

		return $path;
	}
	
}
