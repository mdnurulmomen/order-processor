<?php

namespace App\Http\Controllers\Web;

use App\Models\Meal;
use App\Models\Addon;
use App\Models\Cuisine;
use App\Models\Variation;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;

class AssetController extends Controller
{
    // Meals
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
	    	'name'=>'required|unique:meals,name|max:50',
	    	'search_preference' => 'nullable|boolean'
	 	]);

	 	$newCuisine = Meal::create([
	 		'name' => strtolower($request->name),
	 		'search_preference' => $request->search_preference ?? false,
	 	]);

	 	return $this->showAllMeals($perPage);
	}

	public function updateMeal(Request $request, $meal, $perPage)
	{
	 	$mealToUpdate = Meal::findOrFail($meal);

	 	$request->validate([
	    	'name'=>'required|max:50|unique:meals,name,'.$mealToUpdate->id,
	    	'search_preference' => 'nullable|boolean'
	 	]);

	 	$mealUpdated = $mealToUpdate->update([
	 		'name' => strtolower($request->name),
	 		'search_preference' => $request->search_preference ?? false,
	 	]);

	 	return $this->showAllMeals($perPage);
	}

	public function deleteMeal($meal, $perPage)
  	{
     	$mealToDelete = Meal::findOrFail($meal);

     	if ($mealToDelete) {

     		$mealToDelete->delete();

     	}

     	return $this->showAllMeals($perPage);
  	}

  	public function restoreMeal($meal, $perPage)
  	{
     	$mealToRestore = Meal::onlyTrashed()->findOrFail($meal);

     	if ($mealToRestore) {
     		
     		$mealToRestore->restore();
     	
     	}
         
        return $this->showAllMeals($perPage);
  	}

  	public function searchAllMeals($search, $perPage)
	{
		return response()->json([
			'all' => Meal::withTrashed()->where('name', 'like', "%$search%")->paginate($perPage),  
		], 200);
	}


    // Product-Categories
	public function showAllProductCategories($perPage = false)
	{
	 	if ($perPage) {
		 	return response()->json([
				'current' => ProductCategory::paginate($perPage),
				'trashed' => ProductCategory::onlyTrashed()->paginate($perPage),

			], 200);
	 	}

	 	return response(ProductCategory::get(), 200);
	}

	public function createNewProductCategory(Request $request, $perPage = false)
	{
	 	$request->validate([
	    	'name'=>'required|unique:product_categories,name|max:50',
	    	'search_preference' => 'nullable|boolean'
	 	]);

	 	$newProductCategory = ProductCategory::create([
	 		'name' => strtolower($request->name),
	 		'search_preference' => $request->search_preference ?? false,
	 	]);

	 	return $this->showAllProductCategories($perPage);
	}

	public function updateProductCategory(Request $request, $productCategory, $perPage)
	{
	 	$productCategoryToUpdate = ProductCategory::findOrFail($productCategory);

	 	$request->validate([
	    	'name'=>'required|max:50|unique:product_categories,name,'.$productCategoryToUpdate->id,
	    	'search_preference' => 'nullable|boolean'
	 	]);

	 	$productCategoryUpdated = $productCategoryToUpdate->update([
	 		'name' => strtolower($request->name),
	 		'search_preference' => $request->search_preference ?? false,
	 	]);

	 	return $this->showAllProductCategories($perPage);
	}

	public function deleteProductCategory($productCategory, $perPage)
  	{
     	$productCategoryToDelete = ProductCategory::findOrFail($productCategory);

     	if ($productCategoryToDelete->merchantProductCategories->count()) {
     		
	     	// $productCategoryToDelete->merchantProductCategories()->delete();
	     	return response()->json(['errors' => ['inUse' => ['The category is in use.']]], 422);
     	
     	}
     	
     	// $productCategoryToDelete->delete();
     	
     	return $this->showAllProductCategories($perPage);
  	}

  	public function restoreProductCategory($productCategory, $perPage)
  	{
     	$productCategoryToRestore = ProductCategory::onlyTrashed()->findOrFail($productCategory);

     	$productCategoryToRestore->merchantProductCategories()->restore();
     	$productCategoryToRestore->restore();
         
        return $this->showAllProductCategories($perPage);
  	}

  	public function searchAllProductCategories($search, $perPage)
	{
		return response()->json([
			'all' => ProductCategory::withTrashed()->where('name', 'like', "%$search%")->paginate($perPage),  
		], 200);
	}

	// Cuisines
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
	    	'name'=>'required|unique:cuisines,name|max:50',
	    	'search_preference' => 'nullable|boolean'
	 	]);

	 	$newCuisine = Cuisine::create([
	 		'name' => strtolower($request->name),
	 		'search_preference' => $request->search_preference ?? false,
	 	]);

	 	return $this->showAllCuisines($perPage);
	}

	public function updateCuisine(Request $request, $cuisine, $perPage)
	{
	 	$cuisineToUpdate = Cuisine::findOrFail($cuisine);

	 	$request->validate([
	    	'name'=>'required|max:50|unique:cuisines,name,'.$cuisineToUpdate->id,
	    	'search_preference' => 'nullable|boolean'
	 	]);

	 	$cuisineUpdated = $cuisineToUpdate->update([
	 		'name' => strtolower($request->name),
	 		'search_preference' => $request->search_preference ?? false,
	 	]);

	 	return $this->showAllCuisines($perPage);
	}

	public function deleteCuisine($cuisine, $perPage)
  	{
     	$cuisineToDelete = Cuisine::findOrFail($cuisine);

     	if ($cuisineToDelete) {
     		
     		$cuisineToDelete->delete();

     	}

     	return $this->showAllCuisines($perPage);
  	}

  	public function restoreCuisine($cuisine, $perPage)
  	{
     	$cuisineToRestore = Cuisine::onlyTrashed()->findOrFail($cuisine);

     	if ($cuisineToRestore) {
     		
     		$cuisineToRestore->restore();
     	
     	}
         
        return $this->showAllCuisines($perPage);
  	}

  	public function searchAllCuisines($search, $perPage)
	{
		return response()->json([
			'all' => Cuisine::withTrashed()->where('name', 'like', "%$search%")->paginate($perPage),  
		], 200);
	}


	// Addons
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

	 	$newAddon = Addon::create(['name' => strtolower($request->name)]);

	 	return $this->showAllAddons($perPage);
	}

	public function updateAddon(Request $request, $addon, $perPage)
	{
	 	$addonToUpdate = Addon::findOrFail($addon);

	 	$request->validate([
	    	'name'=>'required|max:50|unique:addons,name,'.$addonToUpdate->id,
	 	]);

	 	$addonUpdated = $addonToUpdate->update([
	 		'name' => strtolower($request->name)
	 	]);

	 	return $this->showAllAddons($perPage);
	}

	public function deleteAddon($addon, $perPage)
  	{
     	$addonToDelete = Addon::findOrFail($addon);

     	if ($addonToDelete) {
     		
     		$addonToDelete->delete();

     	}

     	return $this->showAllAddons($perPage);
  	}

  	public function restoreAddon($addon, $perPage)
  	{
     	$addonToRestore = Addon::onlyTrashed()->findOrFail($addon);

     	if ($addonToRestore) {
     		
     		$addonToRestore->restore();

     	}

        return $this->showAllAddons($perPage);
  	}

  	public function searchAllAddons($search, $perPage)
	{
		return response()->json([
			'all' => Addon::withTrashed()->where('name', 'like', "%$search%")->paginate($perPage),  
		], 200);
	}


	// Variations
	public function showAllVariations($perPage = false)
	{
	 	if ($perPage) {
		 	return response()->json([
				'current' => Variation::paginate($perPage),
				'trashed' => Variation::onlyTrashed()->paginate($perPage),
			], 200);
	 	}

	 	return response(Variation::get(), 200);
	}

	public function createNewVariation(Request $request, $perPage = false)
	{
	 	$request->validate([
	    	'name'=>'required|unique:variations,name|max:50'
	 	]);

	 	$newVariation = Variation::create(['name' => strtolower($request->name)]);

	 	return $this->showAllVariations($perPage);
	}

	public function updateVariation(Request $request, $variation, $perPage)
	{
	 	$variationToUpdate = Variation::findOrFail($variation);

	 	$request->validate([
	    	'name'=>'required|max:50|unique:variations,name,'.$variationToUpdate->id,
	 	]);

	 	$variationToUpdate = $variationToUpdate->update([
	 		'name' => strtolower($request->name)
	 	]);

	 	return $this->showAllVariations($perPage);
	}

	public function deleteVariation($variation, $perPage)
  	{
     	$variationToDelete = Variation::findOrFail($variation);

     	if ($variationToDelete) {
     		
     		$variationToDelete->delete();

     	}

     	return $this->showAllVariations($perPage);
  	}

  	public function restoreVariation($variationToRestore, $perPage)
  	{
     	$variationToRestore = Variation::onlyTrashed()->findOrFail($variationToRestore);

     	if ($variationToRestore) {
     		
     		$variationToRestore->restore();

     	}

        return $this->showAllVariations($perPage);
  	}

  	public function searchAllVariations($search, $perPage)
	{
		return response()->json([
			'all' => Variation::withTrashed()->where('name', 'like', "%$search%")->paginate($perPage),  
		], 200);
	}
}
