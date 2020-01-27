<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class PasswordController extends Controller
{
   	public function updateAdminPassword(Request $request)
   	{
   		$request->validate([
   			'current_password'=>'string|required|max:255|bail',
   			'password'=>'string|required|confirmed|max:255|bail',
   		]);

   		$adminToUpdate = \Auth::guard('admin')->user();

   		if (Hash::check($request->current_password, $adminToUpdate->password)) {
		   	
		   	$adminToUpdate->update([
	            'password' => Hash::make($request->password)
	        ]);
   			
   			return response()->json([
               'success' => 'Password has been updated'
            ], 200);
		   }

         return response()->json([
            'warning' => "Password doesn't match"
         ], 401);
   	}
}
