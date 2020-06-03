<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ApplicationSetting;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\GeneralInfoResource;

class SettingController extends Controller
{
   	public function getGeneralInfo()
   	{
        return new GeneralInfoResource(ApplicationSetting::firstOrCreate(['id' => 1]));
   	}
}
