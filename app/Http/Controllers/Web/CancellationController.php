<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Models\CancellationReason;
use App\Http\Controllers\Controller;

class CancellationController extends Controller
{
    public function showAllReasons($perPage = false)
	{
	 	if ($perPage) {
		 	
		 	return response()->json([
				'current' => CancellationReason::paginate($perPage),
				'trashed' => CancellationReason::onlyTrashed()->paginate($perPage),

			], 200);

	 	}

	 	return response(CancellationReason::get(), 200);
	}

	public function createCancellationReason(Request $request, $perPage = false)
	{
	 	$request->validate([
	    	'reason'=>'required|max:65,500',
	 	]);

	 	$newCuisine = CancellationReason::create([
	 		'reason' => strtolower($request->reason)
	 	]);

	 	return $this->showAllReasons($perPage);
	}

	public function updateCancellationReason(Request $request, $reason, $perPage)
	{
	 	$reasonToUpdate = CancellationReason::find($reason);

	 	$request->validate([
	    	'reason'=>'required|max:65,500',
	 	]);

	 	$reasonUpdated = $reasonToUpdate->update([
	 		'reason' => strtolower($request->reason)
	 	]);

	 	return $this->showAllReasons($perPage);
	}

	public function deleteCancellationReason($reason, $perPage)
  	{
     	$reasonToDelete = CancellationReason::find($reason);

     	if ($reasonToDelete) {

     		$reasonToDelete->delete();

     	}

     	return $this->showAllReasons($perPage);
  	}

  	public function restoreCancellationReason($reason, $perPage)
  	{
     	$reasonToDelete = CancellationReason::onlyTrashed()->find($reason);

     	if ($reasonToDelete) {
     		
     		$reasonToDelete->restore();
     	
     	}
         
        return $this->showAllReasons($perPage);
  	}

  	public function searchAllReasons($search, $perPage)
	{
		$query = CancellationReason::withTrashed()->where('reason', 'like', "%$search%");

		return response()->json([

			'all' => $query->paginate($perPage),  
		
		], 200);
	}
}
