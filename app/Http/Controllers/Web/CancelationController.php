<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Models\CancelationReason;
use App\Http\Controllers\Controller;

class CancelationController extends Controller
{
   	public function showAllReasons($perPage = false)
	{
	 	if ($perPage) {
		 	
		 	return response(CancelationReason::paginate($perPage), 200);

	 	}

	 	return response(CancelationReason::get(), 200);
	}

	public function createCancelationReason(Request $request, $perPage = false)
	{
	 	$request->validate([
	    	'reason'=>'required|max:65,500',
	 	]);

	 	$newCuisine = CancelationReason::create([
	 		'reason' => $request->reason
	 	]);

	 	return $this->showAllReasons($perPage);
	}

	public function updateCancelationReason(Request $request, $reason, $perPage)
	{
	 	$reasonToUpdate = CancelationReason::find($reason);

	 	$request->validate([
	    	'reason'=>'required|max:65,500',
	 	]);

	 	$reasonUpdated = $reasonToUpdate->update([
	 		'reason' => $request->reason
	 	]);

	 	return $this->showAllReasons($perPage);
	}

	public function deleteCancelationReason($reason, $perPage)
  	{
     	$reasonToDelete = CancelationReason::find($reason);

     	if ($reasonToDelete) {

     		$reasonToDelete->delete();

     	}

     	return $this->showAllReasons($perPage);
  	}

  	public function searchAllReasons($search, $perPage)
	{
		$query = CancelationReason::where('reason', 'like', "%$search%");

		return response()->json([
			'all' => $query->paginate($perPage),  
		], 200);
	}
}
