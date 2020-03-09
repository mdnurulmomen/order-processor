<?php

namespace App\Http\Controllers\Web;

use App\Models\Meal;
use App\Models\Cuisine;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FoodController extends Controller
{
   	public function showAllMeals()
	{
	 return response(Meal::get(), 200);
	}

	public function createNewMeal(Request $request)
	{
	 $request->validate([
	    'name'=>'required|unique:meals,name|max:50'
	 ]);

	 $newCuisine = Meal::create(['name' => $request->name]);

	 return $this->showAllMeals();
	}

	public function showAllRestaurantCuisines()
      {
         return response(Cuisine::get(), 200);
      }

      public function createRestaurantCuisine(Request $request)
      {
         $request->validate([
            'name'=>'required|unique:cuisines,name|max:50'
         ]);

         $newCuisine = Cuisine::create(['name' => $request->name]);

         return $this->showAllRestaurantCuisines();
      }

   	public function showAllMenuCategories()
   	{
   		return response(MenuCategory::get(), 200);
   	}

   	public function createMenuCategory(Request $request)
   	{
   		$request->validate([
   			'name'=>'required|unique:menu_categories,name|max:50'
   		]);

   		$newCuisine = MenuCategory::create(['name' => $request->name]);

   		return $this->showAllMenuCategories();
   	}
}
