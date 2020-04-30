<?php

namespace App\Http\Controllers\Web;

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
}
