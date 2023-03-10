<?php

namespace App\Http\Controllers\Web;

use App\Models\Rider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class DeliveryController extends Controller
{
	public function showAllDeliveryMen($perPage = false)
	{
		if ($perPage) {
			return response()->json([
			   'current' => Rider::paginate($perPage),
			   'trashed' => Rider::onlyTrashed()->paginate($perPage),

			], 200);
		}

		return response(Rider::get(), 200);
	}

	public function createDeliveryMan(Request $request, $perPage = false)
	{
		$request->validate([
			'first_name'=>'nullable|string|max:50',
			'last_name'=>'nullable|string|max:50',
			'user_name'=>'required|unique:riders,user_name|string|max:50',
			'email'=>'required|unique:riders,email|email|max:50',
			'mobile'=>'required|unique:riders,mobile|max:13',
			'password'=>'required|string|min:8|max:100|confirmed',
			'birth_date'=>'required',
			'gender'=>'required|in:male,female',
			// 'profile_pic_preview'=>'required|string|min:8|max:100|confirmed',
			'present_address'=>'required|string|max:1000',
			'nid_number'=>'required|numeric|unique:riders,nid_number',
			// 'nid_front_preview'=>'required|string|min:8|max:100|confirmed',
			// 'nid_back_preview'=>'required|string|min:8|max:100|confirmed',
			'delivery_rate_per_kilometer'=>'required|numeric|min:0|max:1000',
         	'min_delivery_charge'=>'required|numeric|min:0|max:1000',
			'max_delivery_charge'=>'required|numeric|min:0|max:1000|gte:min_delivery_charge',
			'payment_method'=>'required|string|max:50',
			'payment_account_number'=>'required|string|min:6|max:100',
			// 'available'=>'required|boolean',
			'is_approved'=>'nullable|boolean',
			'is_available'=>'nullable|boolean',
			'is_engaged'=>'nullable|boolean',

		]);

		$newDeliveryMan =  new Rider();

		$newDeliveryMan->first_name = strtolower($request->first_name);
		$newDeliveryMan->last_name = strtolower($request->last_name);
		$newDeliveryMan->user_name = strtolower($request->user_name);
		$newDeliveryMan->email = strtolower($request->email);
		$newDeliveryMan->mobile = $request->mobile;
		$newDeliveryMan->password = Hash::make($request->password);
		$newDeliveryMan->birth_date = $request->birth_date;
		$newDeliveryMan->gender = $request->gender;

		if ($request->profile_pic_preview) {
			$newDeliveryMan->profile_picture = $request->profile_pic_preview;
		}

		$newDeliveryMan->present_address = strtolower($request->present_address);
		$newDeliveryMan->nid_number = $request->nid_number;

		if ($request->nid_front_preview) {
			$newDeliveryMan->nid_front_preview = $request->nid_front_preview;
		}

		if ($request->nid_back_preview) {
			$newDeliveryMan->nid_back_preview = $request->nid_back_preview;
		}

		$newDeliveryMan->delivery_rate_per_kilometer = $request->delivery_rate_per_kilometer;
      	$newDeliveryMan->min_delivery_charge = $request->min_delivery_charge;
      	$newDeliveryMan->max_delivery_charge = $request->max_delivery_charge;
		$newDeliveryMan->payment_method = strtolower($request->payment_method);
		$newDeliveryMan->payment_account_number = $request->payment_account_number;
		$newDeliveryMan->is_approved = $request->is_approved ?? false;
		$newDeliveryMan->is_available = $request->is_available ?? false;
		$newDeliveryMan->is_engaged = $request->is_engaged ?? false;

		$newDeliveryMan->save();

		return $this->showAllDeliveryMen($perPage);
	}

	public function updateDeliveryMan(Request $request, $deliveryMan, $perPage)
	{
		$deliveryManToUpdate = Rider::find($deliveryMan);

		$request->validate([
			'first_name'=>'nullable|string|max:50',
			'last_name'=>'nullable|string|max:50',
			'user_name'=>'required|string|max:50|unique:riders,user_name,'.$deliveryManToUpdate->id,
			'email'=>'required|email|max:50|unique:riders,email,'.$deliveryManToUpdate->id,
			'mobile'=>'required|max:13|unique:riders,mobile,'.$deliveryManToUpdate->id,
			'password'=>'nullable|string|min:8|max:100|confirmed',
			'birth_date'=>'required',
			'gender'=>'required|in:male,female',
			// 'profile_pic_preview'=>'required|string|min:8|max:100|confirmed',
			'present_address'=>'required|string|max:1000',
			'nid_number'=>'required|numeric|unique:riders,nid_number,'.$deliveryManToUpdate->id,
			// 'nid_front_preview'=>'required|string|min:8|max:100|confirmed',
			// 'nid_back_preview'=>'required|string|min:8|max:100|confirmed',
			'delivery_rate_per_kilometer'=>'required|numeric|min:0|max:1000',
         	'min_delivery_charge'=>'required|numeric|min:0|max:1000',
         	'max_delivery_charge'=>'required|numeric|min:0|max:1000|gte:min_delivery_charge',
			'payment_method'=>'required|string|max:50',
			'payment_account_number'=>'required|string|min:6|max:100',
			// 'available'=>'required|boolean',
			'is_approved'=>'nullable|boolean',
			'is_available'=>'nullable|boolean',
			'is_engaged'=>'nullable|boolean',
		]);

		$deliveryManToUpdate->first_name = strtolower($request->first_name);
		$deliveryManToUpdate->last_name = strtolower($request->last_name);
		$deliveryManToUpdate->user_name = strtolower($request->user_name);
		$deliveryManToUpdate->email = strtolower($request->email);
		$deliveryManToUpdate->mobile = $request->mobile;

		if ($request->password) {    
			$deliveryManToUpdate->password = Hash::make($request->password);
		}

		$deliveryManToUpdate->birth_date = $request->birth_date;
		$deliveryManToUpdate->gender = $request->gender;
		$deliveryManToUpdate->profile_picture = $request->profile_pic_preview;
		$deliveryManToUpdate->present_address = strtolower($request->present_address);
		$deliveryManToUpdate->nid_number = $request->nid_number;
		$deliveryManToUpdate->nid_front_preview = $request->nid_front_preview;
		$deliveryManToUpdate->nid_back_preview = $request->nid_back_preview;
		$deliveryManToUpdate->delivery_rate_per_kilometer = $request->delivery_rate_per_kilometer;
      	$deliveryManToUpdate->min_delivery_charge = $request->min_delivery_charge;
      	$deliveryManToUpdate->max_delivery_charge = $request->max_delivery_charge;
		$deliveryManToUpdate->payment_method = strtolower($request->payment_method);
		$deliveryManToUpdate->payment_account_number = $request->payment_account_number;
		$deliveryManToUpdate->is_approved = $request->is_approved ?? false;
		$deliveryManToUpdate->is_available = $request->is_available ?? false;
		$deliveryManToUpdate->is_engaged = $request->is_engaged ?? false;

		$deliveryManToUpdate->save();

		return $this->showAllDeliveryMen($perPage);
	}

	public function deleteDeliveryMan($deliveryMan, $perPage)
	{
		$deliveryManToDelete = Rider::find($deliveryMan);

		if ($deliveryManToDelete) {

			$deliveryManToDelete->delete();

		}

		return $this->showAllDeliveryMen($perPage);
	}

	public function restoreDeliveryMan($deliveryMan, $perPage)
	{
		$deliveryManToRestore = Rider::withTrashed()->find($deliveryMan);

		if ($deliveryManToRestore) {

			$deliveryManToRestore->restore();

		}

		return $this->showAllDeliveryMen($perPage);
	}

	public function searchAllDeliveryMen($search, $perPage)
	{
		$columnsToSearch = ['first_name', 'last_name', 'user_name', 'email', 'mobile', 'birth_date', 'gender', 'nid_number', 'payment_method', 'payment_account_number'];

		$query = Rider::withTrashed();

		foreach($columnsToSearch as $column)
		{
			$query->orWhere($column, 'like', "%$search%");
		}

		return response()->json([
			'all' => $query->paginate($perPage),  
		], 200);
	}
}
