<?php

namespace App\Http\Controllers\Web;

use App\Models\Meal;
use App\Models\Addon;
use App\Models\Cuisine;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Models\ItemVariation;
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

	public function deleteMeal($meal, $perPage)
  	{
     	$mealToDelete = Meal::find($meal);

     	if ($mealToDelete) {

     		$mealToDelete->delete();

     	}

     	return $this->showAllMeals($perPage);
  	}

  	public function restoreMeal($meal, $perPage)
  	{
     	$mealToStore = Meal::onlyTrashed()->find($meal);

     	if ($mealToStore) {
     		
     		$mealToStore->restore();
     	
     	}
         
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

	public function deleteMenuCategory($menuCategory, $perPage)
  	{
     	$menuCategoryToDelete = MenuCategory::find($menuCategory);

     	if ($menuCategoryToDelete) {
     		
	     	$menuCategoryToDelete->menuItems()->delete();
	     	$menuCategoryToDelete->restaurantMenuCategories()->delete();
	     	$menuCategoryToDelete->delete();
     	
     	}
     	
     	return $this->showAllMenuCategories($perPage);
  	}

  	public function restoreMenuCategory($menuCategory, $perPage)
  	{
     	$menuCategoryToStore = MenuCategory::onlyTrashed()->find($menuCategory);

     	if ($menuCategoryToStore) {

	     	$menuCategoryToStore->restaurantMenuCategories()->restore();
	     	$menuCategoryToStore->restore();

     	}
         
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

	public function deleteCuisine($cuisine, $perPage)
  	{
     	$cuisineToDelete = Cuisine::find($cuisine);

     	if ($cuisineToDelete) {
     		
     		$cuisineToDelete->delete();

     	}

     	return $this->showAllCuisines($perPage);
  	}

  	public function restoreCuisine($cuisine, $perPage)
  	{
     	$cuisineToStore = Cuisine::onlyTrashed()->find($cuisine);

     	if ($cuisineToStore) {
     		
     		$cuisineToStore->restore();
     	
     	}
         
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

	public function deleteAddon($addon, $perPage)
  	{
     	$addonToDelete = Addon::find($addon);

     	if ($addonToDelete) {
     		
     		$addonToDelete->delete();

     	}

     	return $this->showAllAddons($perPage);
  	}

  	public function restoreAddon($addon, $perPage)
  	{
     	$addonToStore = Addon::onlyTrashed()->find($addon);

     	if ($addonToStore) {
     		
     		$addonToStore->restore();

     	}

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



	public function showAllVariations($perPage = false)
	{
	 	if ($perPage) {
		 	return response()->json([
				'current' => ItemVariation::paginate($perPage),
				'trashed' => ItemVariation::onlyTrashed()->paginate($perPage),
			], 200);
	 	}

	 	return response(ItemVariation::get(), 200);
	}

	public function createNewVariation(Request $request, $perPage = false)
	{
	 	$request->validate([
	    	'variation_name'=>'required|unique:item_variations,variation_name|max:50'
	 	]);

	 	$newVariation = ItemVariation::create(['variation_name' => $request->variation_name]);

	 	return $this->showAllVariations($perPage);
	}

	public function updateVariation(Request $request, $variation, $perPage)
	{
	 	$variationToUpdate = ItemVariation::find($variation);

	 	$request->validate([
	    	'variation_name'=>'required|max:50|unique:item_variations,variation_name,'.$variationToUpdate->id,
	 	]);

	 	$variationToUpdate = $variationToUpdate->update([
	 		'variation_name' => $request->variation_name
	 	]);

	 	return $this->showAllVariations($perPage);
	}

	public function deleteVariation($variation, $perPage)
  	{
     	$variationToDelete = ItemVariation::find($variation);

     	if ($variationToDelete) {
     		
     		$variationToDelete->delete();

     	}

     	return $this->showAllVariations($perPage);
  	}

  	public function restoreVariation($variationToRestore, $perPage)
  	{
     	$variationToStore = ItemVariation::onlyTrashed()->find($variationToRestore);

     	if ($variationToStore) {
     		
     		$variationToStore->restore();

     	}

        return $this->showAllVariations($perPage);
  	}

  	public function searchAllVariations($search, $perPage)
	{
		$columnsToSearch = ['variation_name'];

		$query = ItemVariation::withTrashed();

		foreach($columnsToSearch as $column)
		{
			$query->orWhere($column, 'like', "%$search%");
		}

		return response()->json([
			'all' => $query->paginate($perPage),  
		], 200);
	}

}
