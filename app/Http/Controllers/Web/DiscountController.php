<?php

namespace App\Http\Controllers\Web;

use App\Models\Coupon;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscountController extends Controller
{
	public function showAllDiscounts($perPage = false)
	{
		if ($perPage) {
			return response(Discount::paginate($perPage), 200);
		}

		return response(Discount::get(), 200);
	}

	public function createNewDiscount(Request $request, $perPage = false)
	{
		$request->validate([
			'rate'=>'required|numeric|unique:discounts,rate|min:1|max:100'
		]);

		$newCuisine = Discount::create(['rate' => $request->rate]);

		return $this->showAllDiscounts($perPage);
	}

	public function updateDiscount(Request $request, $discount, $perPage)
	{
		$discountToUpdate = Discount::find($discount);

		$request->validate([
		'rate'=>'required|numeric|min:1|max:100|unique:discounts,rate,'.$discountToUpdate->id,
		]);

		$discountUpdated = $discountToUpdate->update([
			'rate' => $request->rate
		]);

		return $this->showAllDiscounts($perPage);
	}

	public function searchAllDiscounts($search, $perPage)
	{
		return response()->json([
			'all' => Discount::where('rate', 'like', "%$search%")->paginate($perPage),  
		], 200);
	}


	public function showAllCoupons($perPage = false)
	{
		if ($perPage) {
			return response(Coupon::paginate($perPage), 200);
		}

		return response(Coupon::get(), 200);
	}

	public function createNewCoupon(Request $request, $perPage = false)
	{
		$request->validate([
			'code'=>'required|string|unique:coupons,code',
			'percentage'=>'required|numeric|min:1|max:100',
			'min_order'=>'required|numeric|min:100|max:255',
			'max_discount_per_order'=>'required|numeric|max:255',
			'durability'=>'nullable|boolean',
			'valid_from'=>'nullable|date',
			'valid_to'=>'nullable|date',
			'status'=>'nullable|boolean',
		]);

		$newCoupon = new Coupon();

		$newCoupon->code = strtolower($request->code);
		$newCoupon->percentage = $request->percentage;
		$newCoupon->min_order = $request->min_order;
		$newCoupon->max_discount_per_order = $request->max_discount_per_order;
		$newCoupon->durability = $request->durability ?? false;

		if ($request->durability) {
			$newCoupon->valid_from = $request->valid_from;
			$newCoupon->valid_to = $request->valid_to;
		}
		
		$newCoupon->status = $request->status ?? false;
		$newCoupon->editor_id = \Auth::guard('admin')->user()->id;

		$newCoupon->save();

		return $this->showAllCoupons($perPage);
	}

	public function updateCoupon(Request $request, $coupon, $perPage)
	{
		$couponToUpdate = Coupon::find($coupon);

		$request->validate([
			'code'=>'required|string|unique:coupons,code,'.$couponToUpdate->id,
			'percentage'=>'required|numeric|min:1|max:100',
			'min_order'=>'required|numeric|min:100|max:255',
			'max_discount_per_order'=>'required|numeric|max:255',
			'durability'=>'nullable|boolean',
			'valid_from'=>'nullable|date',
			'valid_to'=>'nullable|date',
			'status'=>'nullable|boolean',
		]);

		$couponToUpdate->code = strtolower($request->code);
		$couponToUpdate->percentage = $request->percentage;
		$couponToUpdate->min_order = $request->min_order;
		$couponToUpdate->max_discount_per_order = $request->max_discount_per_order;
		$couponToUpdate->durability = $request->durability ?? false;

		if ($request->durability) {
			$couponToUpdate->valid_from = $request->valid_from;
			$couponToUpdate->valid_to = $request->valid_to;
		}
		else {
			$couponToUpdate->valid_from = NULL;
			$couponToUpdate->valid_to = NULL;
		}
		
		$couponToUpdate->status = $request->status ?? false;
		$couponToUpdate->editor_id = \Auth::guard('admin')->user()->id;

		$couponToUpdate->save();

		return $this->showAllCoupons($perPage);
	}

	public function deleteCoupon(Request $request, $coupon, $perPage)
	{
		$couponToDelete = Coupon::find($coupon);

		if ($couponToDelete) {

			$couponToDelete->delete();

		}

		return $this->showAllCoupons($perPage);
	}

	public function searchAllCoupons($search, $perPage)
	{
		return response()->json([
			'all' => Coupon::where('code', 'like', "%$search%")
							->orWhere('percentage', 'like', "%$search%")
							->orWhere('min_order', 'like', "%$search%")
							->orWhere('max_discount_per_order', 'like', "%$search%")
							->orWhere('status', 'like', "%$search%")
							->paginate($perPage),  
		], 200);
	}
}
