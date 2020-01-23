<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showAdminProfile()
    {
        return response(\Auth::guard('admin')->user(), 200);
    }

    /**
     * Update Admin Profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateAdminProfile(Request $request)
    {
        $adminToUpdate = \Auth::guard('admin')->user();
        
        $request->validate([
            'first_name'=>'nullable|string|max:50',
            'last_name'=>'nullable|string|max:50',
            'mobile'=>'string|required|max:13|bail|unique:admins,mobile,'.$adminToUpdate->id,
            'email'=>'email|required|bail|unique:admins,email,'.$adminToUpdate->id,
            'profile_picture'=>'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $adminToUpdate->first_name = $request->first_name;
        $adminToUpdate->last_name = $request->last_name;
        $adminToUpdate->mobile = $request->mobile;
        $adminToUpdate->email = $request->email;
        $adminToUpdate->profile_picture = $request->file('profile_picture');

        $adminToUpdate->save();

        return back()->with('success', 'Profile has been updated');
    }
}
