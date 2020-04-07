<?php

namespace App\Http\Controllers\Web;

use App\Models\Meal;
use App\Models\Addon;
use App\Models\Cuisine;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FoodController extends Controller
{
   	public function showAllMeals($perPage = false)
	{
	 	if ($perPage) {
		 	return response()->json([
				'current' => Meal::paginate($perPage),
				'trashed' => Meal::onlyTrashed()->paginate($perPage),

			], 200);
	 	}

	 	return response(Meal::get(), 200);
	}

	public function createNewMeal(Request $request, $perPage = false)
	{
	 	$request->validate([
	    	'name'=>'required|unique:meals,name|max:50'
	 	]);

	 	$newCuisine = Meal::create(['name' => $request->name]);

	 	return $this->showAllMeals($perPage);
	}

	public function updateMeal(Request $request, $meal, $perPage)
	{
	 	$mealToUpdate = Meal::find($meal);

	 	$request->validate([
	    	'name'=>'required|max:50|unique:meals,name,'.$mealToUpdate->id,
	 	]);

	 	$mealUpdated = $mealToUpdate->update([
	 		'name' => $request->name
	 	]);

	 	return $this->showAllMeals($perPage);
	}

	public function deleteMeal($mealToDelete, $perPage)
  	{
     	Meal::destroy($mealToDelete);
     	return $this->showAllMeals($perPage);
  	}

  	public function restoreMeal($mealToRestore, $perPage)
  	{
     	$mealToStore = Meal::onlyTrashed()->find($mealToRestore);
     	$mealToStore->restore();
         
        return $this->showAllMeals($perPage);
  	}

  	public function searchAllMeals($search, $perPage)
	{
		$columnsToSearch = ['name'];

		$query = Meal::withTrashed();

		foreach($columnsToSearch as $column)
		{
			$query->orWhere($column, 'like', "%$search%");
		}

		return response()->json([
			'all' => $query->paginate($perPage),  
		], 200);
	}

	public function showAllMenuCategories($perPage = false)
	{
	 	if ($perPage) {
		 	return response()->json([
				'current' => MenuCategory::paginate($perPage),
				'trashed' => MenuCategory::onlyTrashed()->paginate($perPage),

			], 200);
	 	}

	 	return response(MenuCategory::get(), 200);
	}

	public function createNewMenuCategory(Request $request, $perPage = false)
	{
	 	$request->validate([
	    	'name'=>'required|unique:menu_categories,name|max:50'
	 	]);

	 	$newMenuCategory = MenuCategory::create(['name' => $request->name]);

	 	return $this->showAllMenuCategories($perPage);
	}

	public function updateMenuCategory(Request $request, $menuCategory, $perPage)
	{
	 	$menuCategoryToUpdate = MenuCategory::find($menuCategory);

	 	$request->validate([
	    	'name'=>'required|max:50|unique:menu_categories,name,'.$menuCategoryToUpdate->id,
	 	]);

	 	$menuCategoryUpdated = $menuCategoryToUpdate->update([
	 		'name' => $request->name
	 	]);

	 	return $this->showAllMenuCategories($perPage);
	}

	public function deleteMenuCategory($menuCategoryToDelete, $perPage)
  	{
     	$expectedMenuCategory = MenuCategory::find($menuCategoryToDelete);
     	$expectedMenuCategory->restaurantMenuCategories()->delete();
     	$expectedMenuCategory->delete();
     	
     	return $this->showAllMenuCategories($perPage);
  	}

  	public function restoreMenuCategory($menuCategoryToRestore, $perPage)
  	{
     	$menuCategoryToStore = MenuCategory::onlyTrashed()->find($menuCategoryToRestore);
     	$menuCategoryToStore->restaurantMenuCategories()->restore();
     	$menuCategoryToStore->restore();
         
        return $this->showAllMenuCategories($perPage);
  	}

  	public function searchAllMenuCategories($search, $perPage)
	{
		$columnsToSearch = ['name'];

		$query = MenuCategory::withTrashed();

		foreach($columnsToSearch as $column)
		{
			$query->orWhere($column, 'like', "%$search%");
		}

		return response()->json([
			'all' => $query->paginate($perPage),  
		], 200);
	}

	public function showAllCuisines($perPage = false)
	{
	 	if ($perPage) {
		 	return response()->json([
				'current' => Cuisine::paginate($perPage),
				'trashed' => Cuisine::onlyTrashed()->paginate($perPage),

			], 200);
	 	}

	 	return response(Cuisine::get(), 200);
	}

	public function createNewCuisine(Request $request, $perPage = false)
	{
	 	$request->validate([
	    	'name'=>'required|unique:cuisines,name|max:50'
	 	]);

	 	$newCuisine = Cuisine::create(['name' => $request->name]);

	 	return $this->showAllCuisines($perPage);
	}

	public function updateCuisine(Request $request, $cuisine, $perPage)
	{
	 	$cuisineToUpdate = Cuisine::find($cuisine);

	 	$request->validate([
	    	'name'=>'required|max:50|unique:cuisines,name,'.$cuisineToUpdate->id,
	 	]);

	 	$cuisineUpdated = $cuisineToUpdate->update([
	 		'name' => $request->name
	 	]);

	 	return $this->showAllCuisines($perPage);
	}

	public function deleteCuisine($cuisineToDelete, $perPage)
  	{
     	Cuisine::destroy($cuisineToDelete);
     	return $this->showAllCuisines($perPage);
  	}

  	public function restoreCuisine($cuisineToRestore, $perPage)
  	{
     	$cuisineToStore = Cuisine::onlyTrashed()->find($cuisineToRestore);
     	$cuisineToStore->restore();
         
        return $this->showAllCuisines($perPage);
  	}

  	public function searchAllCuisines($search, $perPage)
	{
		$columnsToSearch = ['name'];

		$query = Cuisine::withTrashed();

		foreach($columnsToSearch as $column)
		{
			$query->orWhere($column, 'like', "%$search%");
		}

		return response()->json([
			'all' => $query->paginate($perPage),  
		], 200);
	}

	public function showAllAddons($perPage = false)
	{
	 	if ($perPage) {
		 	return response()->json([
				'current' => Addon::paginate($perPage),
				'trashed' => Addon::onlyTrashed()->paginate($perPage),

			], 200);
	 	}

	 	return response(Addon::get(), 200);
	}

	public function createNewAddon(Request $request, $perPage = false)
	{
	 	$request->validate([
	    	'name'=>'required|unique:addons,name|max:50'
	 	]);

	 	$newAddon = Addon::create(['name' => $request->name]);

	 	return $this->showAllAddons($perPage);
	}

	public function updateAddon(Request $request, $addon, $perPage)
	{
	 	$addonToUpdate = Addon::find($addon);

	 	$request->validate([
	    	'name'=>'required|max:50|unique:addons,name,'.$addonToUpdate->id,
	 	]);

	 	$addonUpdated = $addonToUpdate->update([
	 		'name' => $request->name
	 	]);

	 	return $this->showAllAddons($perPage);
	}

	public function deleteAddon($addonToDelete, $perPage)
  	{
     	Addon::destroy($addonToDelete);
     	return $this->showAllAddons($perPage);
  	}

  	public function restoreAddon($addonToRestore, $perPage)
  	{
     	$addonToStore = Addon::onlyTrashed()->find($addonToRestore);
     	$addonToStore->restore();
         
        return $this->showAllAddons($perPage);
  	}

  	public function searchAllAddons($search, $perPage)
	{
		$columnsToSearch = ['name'];

		$query = Addon::withTrashed();

		foreach($columnsToSearch as $column)
		{
			$query->orWhere($column, 'like', "%$search%");
		}

		return response()->json([
			'all' => $query->paginate($perPage),  
		], 200);
	}

}
