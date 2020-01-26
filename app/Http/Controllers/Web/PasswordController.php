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
   			
   			return response(null, 200);
            // return back()->with('success', 'Password has been updated');
		   }

         return response(null, 400);
		   // return back()->with('warning', "Password doesn't match");
   	}
}
