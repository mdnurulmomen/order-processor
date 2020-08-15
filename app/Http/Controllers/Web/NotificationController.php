<?php

namespace App\Http\Controllers\Web;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
  	public function showAllNotifications($perPage = false)
	{
		
		if ($perPage) {
			
			/*
			return response()->json([
			   'current' => Notification::paginate($perPage),
			   'trashed' => Notification::onlyTrashed()->paginate($perPage),

			], 200);
			*/
		
			return response(Notification::paginate($perPage), 200);
		}
		

		return response(Notification::get(), 200);
	}

	public function createNotification(Request $request, $perPage = false)
	{
		$request->validate([
			'title'=>'required|string|max:200',
			// 'banner'=>'required|string|min:8|max:100|confirmed',
			'description'=>'required|string|max:1000',
			'status'=>'nullable|boolean',
		]);

		$newNotification =  new Notification();

		$newNotification->title = $request->title;
		$newNotification->banner = $request->banner;
		$newNotification->description = $request->description;
		$newNotification->status = $request->status ?? false;
		$newNotification->editor_id = \Auth::guard('admin')->user()->id;

		$newNotification->save();

		return $this->showAllNotifications($perPage);
	}

	public function updateNotification(Request $request, $notification, $perPage)
	{
		$notificationToUpdate = Notification::find($notification);

		$request->validate([
			'title'=>'required|string|max:200',
			// 'banner'=>'required|string|min:8|max:100|confirmed',
			'description'=>'required|string|max:1000',
			'status'=>'nullable|boolean',
		]);

		$notificationToUpdate->title = $request->title;

		if ($request->banner) {
			$notificationToUpdate->banner = $request->banner;
		}

		$notificationToUpdate->description = $request->description;
		$notificationToUpdate->status = $request->status ?? false;
		$notificationToUpdate->editor_id = \Auth::guard('admin')->user()->id;

		$notificationToUpdate->save();

		return $this->showAllNotifications($perPage);
	}

	public function deleteNotification($notification, $perPage)
	{
		$notificationToDelete = Notification::find($notification);

		if ($notificationToDelete) {

			$notificationToDelete->delete();

		}

		return $this->showAllNotifications($perPage);
	}

/*
	public function restoreNotification($notification, $perPage)
	{
		$notificationToRestore = Notification::withTrashed()->find($notification);

		if ($notificationToRestore) {

			$notificationToRestore->restore();

		}

		return $this->showAllNotifications($perPage);
	}
*/

	public function searchAllNotifications($search, $perPage)
	{
		$query = Notification::where('title', 'like', "%$search%")
							 ->orWhere('description', 'like', "%$search%")
							 ->orWhere('status', 'like', "%$search%");

		return response()->json([
			'all' => $query->paginate($perPage),  
		], 200);
	}
}
