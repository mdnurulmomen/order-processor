<?php

namespace App\Http\Controllers\Web;

use App\Models\Meal;
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
     	MenuCategory::destroy($menuCategoryToDelete);
     	return $this->showAllMenuCategories($perPage);
  	}

  	public function restoreMenuCategory($menuCategoryToRestore, $perPage)
  	{
     	$menuCategoryToStore = MenuCategory::onlyTrashed()->find($menuCategoryToRestore);
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


   	public function showAllRestaurantCuisines($perPage = false)
	{
	 	if ($perPage) {
		 	return response()->json([
				'current' => Cuisine::paginate($perPage),
				'trashed' => Cuisine::onlyTrashed()->paginate($perPage),

			], 200);
	 	}
	 	return response(Cuisine::get(), 200);
	}

	public function createRestaurantCuisine(Request $request, $perPage = false)
	{
	 	$request->validate([
	    	'name'=>'required|unique:cuisines,name|max:50'
	 	]);

	 	$newCuisine = Cuisine::create(['name' => $request->name]);

	 	return $this->showAllRestaurantCuisines($perPage ?? null);
	}
}
