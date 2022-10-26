<?php

namespace App\Http\Controllers\Web;

use App\Models\Merchant;
use App\Models\MerchantMeal;
use Illuminate\Http\Request;
use App\Models\MerchantProduct;
use App\Models\MerchantCuisine;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\MerchantProductAddon;
use App\Models\MerchantProductCategory;
use App\Models\MerchantProductVariation;

class ProductController extends Controller
{
   // All Merchant-Meals
   public function showAllMerchantMeals($perPage = false)
   {
      if ($perPage) {
         return response(Merchant::with('meals')->paginate($perPage), 200);
      }

      return response(Merchant::with('meals')->get(), 200);
   }

   public function createMerchantMeal(Request $request, $perPage = false)
   {
      $request->validate([
         'meal_id.*'=>'required|numeric|exists:meals,id',
         'merchant_id'=>'required|numeric|exists:merchants,id',
      ]);

      $merchant = Merchant::find($request->merchant_id);

      foreach ($request->meal_id as $merchantNewMeal) {
         
         if (!$merchant->meals()->wherePivot('meal_id', $merchantNewMeal)->count()) {
            
            $merchant->meals()->syncWithoutDetaching([$merchantNewMeal]);
         }

      }

      return $this->showAllMerchantMeals($perPage);
   }

   public function updateMerchantMeal(Request $request, $merchant, $perPage)
   {
      $merchantToUpdate = Merchant::find($merchant);

      $request->validate([
         'meal_id.*'=>'required|numeric|exists:meals,id',
         'merchant_id'=>'required|numeric|exists:merchants,id',
      ]);

      $merchantToUpdate->meals()->sync($request->meal_id); 

      return $this->showAllMerchantMeals($perPage);
   }

   public function deleteMerchantMeal($merchant, $perPage)
   {
      $merchantToDelete = Merchant::find($merchant);

      if ($merchantToDelete) {
         
         $merchantToDelete->meals()->sync([]);  

      }

      return $this->showAllMerchantMeals($perPage);
   }

   public function searchAllMerchantMeals($search, $perPage)
   {
      $query = Merchant::with('meals')
      ->where('name', 'like', "%$search%")
      ->orWhereHas('meals', function($q)use ($search){
      $q->where('name', 'like', "%$search%");
      });

      return response()->json([
         'all' => $query->paginate($perPage),  
      ], 200);
   }

   // Merchant-Cuisines
   public function showAllMerchantCuisines($perPage = false)
   {
      if ($perPage) {
         return response(Merchant::with('cuisines')->paginate($perPage), 200);
      }

      return response(Merchant::with('cuisines')->get(), 200);
   }

   public function createMerchantCuisine(Request $request, $perPage = false)
   {
      $request->validate([
         'cuisine_id.*'=>'required|numeric|exists:cuisines,id',
         'merchant_id'=>'required|numeric|exists:merchants,id',
      ]);

      $merchant = Merchant::find($request->merchant_id);

      foreach ($request->cuisine_id as $merchantNewCuisine) {
         
         if (! $merchant->cuisines()->wherePivot('cuisine_id', $merchantNewCuisine)->count()) {
            
            $merchant->cuisines()->syncWithoutDetaching([$merchantNewCuisine]);
         }

      }

      return $this->showAllMerchantCuisines($perPage);
   }

   public function updateMerchantCuisine(Request $request, $merchant, $perPage)
   {
      $merchantToUpdate = Merchant::find($merchant);

      $request->validate([
         'cuisine_id.*'=>'required|numeric|exists:cuisines,id',
         'merchant_id'=>'required|numeric|exists:merchants,id',
      ]);

      $merchantToUpdate->cuisines()->sync($request->cuisine_id);       

      return $this->showAllMerchantCuisines($perPage);
   }

   public function deleteMerchantCuisine($merchant, $perPage)
   {
      $merchantToDelete = Merchant::find($merchant);

      if ($merchantToDelete) {
         
         $merchantToDelete->cuisines()->sync([]);  
      
      }

      return $this->showAllMerchantCuisines($perPage);
   }

   public function searchAllMerchantCuisines($search, $perPage)
   {
      $query = Merchant::with('cuisines')
      ->orWhere('name', 'like', "%$search%")
      ->orWhereHas('cuisines', function($q)use ($search){
      $q->where('name', 'like', "%$search%");
      });

      return response()->json([
         'all' => $query->paginate($perPage),  
      ], 200);
   }

   // Merchant Products
   public function showMerchantAllProducts($merchant, $perPage = false)
   {
      if ($perPage) {
         return response(MerchantProductCategory::withTrashed()->where('merchant_id', $merchant)->with(['merchant', 'productCategory', 'merchantProducts.variations', 'merchantProducts.addons'])->paginate($perPage), 200);
      }

      return response(MerchantProductCategory::where('merchant_id', $merchant)->with(['productCategory', 'merchantProducts'])->get(), 200);
   }

   public function createMerchantProduct(Request $request, $perPage = false)
   {
      $request->validate([
         'name'=>'required|string|max:255',
         'detail'=>'nullable|string|max:255',
         'has_variation'=>'boolean',
         'has_addon'=>'boolean',
         'promoted'=>'boolean',
         'customizable'=>'boolean',
         'price'=>'numeric|min:0|max:65535',
         'discount' => 'numeric|min:0|max:100',
         'merchant_product_category_id'=>'required|numeric|exists:merchant_product_categories,id',
         'merchant_id'=>'required|numeric|exists:merchants,id',
         'idVariations' => 'exclude_if:has_variation,false,0,|required_if:has_variation,true,1|array',
         'idVariations.*' => 'exclude_if:has_variation,false,0,|required_if:has_variation,true,1|numeric|exists:variations,id',
         'priceVariations' => 'exclude_if:has_variation,false,0,|required_if:has_variation,true,1|array',
         'idAddons' => 'exclude_if:has_addon,false,0,|required_if:has_addon,true,1|array',
         'idAddons.*' => 'exclude_if:has_addon,false,0,|required_if:has_addon,true,1|numeric|exists:addons,id',
         'priceAddons' => 'exclude_if:has_addon,false,0,|required_if:has_addon,true,1|array',
      ]);

      $newMerchantProduct = MerchantProduct::create([
         'name' => strtolower($request->name),
         'sku' => $this->generateProductSKU($request->merchant_product_category_id, MerchantProduct::count()+1),
         'detail' => strtolower($request->detail) ?? '',
         'has_variation' => $request->has_variation ?? false,
         'has_addon' => $request->has_addon ?? false,
         'price' => $request->has_variation ? min($request->priceVariations) : $request->price,
         'discount' => $request->discount ?? 0,
         'customizable' => $request->customizable ?? false,
         'promoted' => $request->promoted ?? false,
         'merchant_product_category_id' => $request->merchant_product_category_id,
      ]);

      if ($newMerchantProduct->has_variation) {

         for ($i=0; $i < count($request->idVariations); $i++) { 
            
            $newMerchantProduct->variations()
                        ->syncWithoutDetaching([
                           $request->idVariations[$i] => [
                              'sku' => $this->generateVariationSKU($newMerchantProduct->sku, $request->idVariations[$i]),
                              'price' => $request->priceVariations[$i] ?? 0
                           ]
                        ]
            );

         }

         // setting minimum price
         $newMerchantProduct->update([
            'price' => min($request->priceVariations)
         ]);

      }

      if ($newMerchantProduct->has_addon) {

         for ($i=0; $i < count($request->idAddons); $i++) { 
            
            $newMerchantProduct->addons()
                        ->syncWithoutDetaching([
                           $request->idAddons[$i] => [
                              'price' => $request->priceAddons[$i] ?? 0
                           ]
                        ]
            );

         }

      /*
         $newMerchantProduct->update([
            'price' => min($request->priceAddons)
         ]);
      */

      }

      return $this->showMerchantAllProducts($request->merchant_id, $perPage);
   }

   public function updateMerchantProduct(Request $request, $merchantProduct, $perPage)
   {
      $request->validate([
         'name'=>'required|string|max:255',
         'detail'=>'nullable|string|max:255',
         'has_variation'=>'boolean',
         'has_addon'=>'boolean',
         'promoted'=>'boolean',
         'customizable'=>'boolean',
         'price'=>'numeric|min:0|max:65535', 
         'discount' => 'numeric|min:0|max:100',
         'merchant_product_category_id'=>'required|numeric|exists:merchant_product_categories,id',
         'merchant_id'=>'required|numeric|exists:merchants,id',
         'idVariations' => 'exclude_if:has_variation,false,0,|required_if:has_variation,true,1|array',
         'idVariations.*' => 'exclude_if:has_variation,false,0,|required_if:has_variation,true,1|numeric|exists:variations,id',
         'priceVariations' => 'exclude_if:has_variation,false,0,|required_if:has_variation,true,1|array',
         'idAddons' => 'exclude_if:has_addon,false,0,|required_if:has_addon,true,1|array',
         'idAddons.*' => 'exclude_if:has_addon,false,0,|required_if:has_addon,true,1|numeric|exists:addons,id',
         'priceAddons' => 'exclude_if:has_addon,false,0,|required_if:has_addon,true,1|array',
      ]);

      $merchantProductToUpdate = MerchantProduct::findOrFail($merchantProduct);

      $merchantProductToUpdate->update([
         'name' => strtolower($request->name),
         'detail' => strtolower($request->detail) ?? '',
         'has_variation' => $request->has_variation ?? false,
         'has_addon' => $request->has_addon ?? false,
         'price' => $request->has_variation ? min($request->priceVariations) : $request->price,
         'discount' => $request->discount ?? 0,
         'customizable' => $request->customizable ?? false,
         'promoted' => $request->promoted ?? false,
         'merchant_product_category_id' => $request->merchant_product_category_id,
      ]);

      if ($merchantProductToUpdate->has_variation) {

         // if this product has orders
         if ($merchantProductToUpdate->orders->count()) {
            
            // Deleting temporarily all related variations
            MerchantProductVariation::where('merchant_product_id', $merchantProduct)
                                       ->delete();

         }
         else {
            // Deleting permanently all related variations
            MerchantProductVariation::where('merchant_product_id', $merchantProduct)
                                       ->forceDelete();
         }

         for ($i=0; $i<count($request->idVariations); $i++) { 
            
            /*
               $alreadyExist = $merchantProductToUpdate->variations()
               ->wherePivot('variation_id', $request->idVariations[$i])
               ->count();
            */

            $existingVariation = MerchantProductVariation::withTrashed()
                                 ->where('merchant_product_id', $merchantProduct)
                                 ->where('variation_id', $request->idVariations[$i])
                                 ->first();

            if ($existingVariation) {
               
            /*
               $merchantProductToUpdate->variations()
               ->wherePivot('variation_id', $request->idVariations[$i])
               ->update([
                  'price' => $request->priceVariations[$i],
                  'merchant_menu_item_variations.deleted_at' => NULL,
               ]);
            */
           
               $existingVariation->update([
                  'price' => $request->priceVariations[$i] ?? 0,
                  'deleted_at' => NULL,
               ]);
                                
            }

            else {

               $merchantProductToUpdate->variations()
                 ->attach($request->idVariations[$i], [
                     'sku' => $this->generateVariationSKU($merchantProductToUpdate->sku, $request->idVariations[$i]),
                     'price' => $request->priceVariations[$i] ?? 0
                  ]
               );

            }

         }

         $merchantProductToUpdate->update([
            'price' => min($request->priceVariations)
         ]);

      }

      if ($merchantProductToUpdate->has_addon) {

         // checking for previous orders
         if ($merchantProductToUpdate->orders->count()) {
            // Deleting temporarily all related variations
            MerchantProductAddon::where('merchant_product_id', $merchantProduct)->delete();
         }
         else {
            // Deleting permanently all related variations
            MerchantProductAddon::where('merchant_product_id', $merchantProduct)->forceDelete();
         }                          

         for ($i=0; $i<count($request->idAddons); $i++) { 
            
            /*
               $alreadyExist = $merchantProductToUpdate->addons()
               ->wherePivot('addon_id', $request->idAddons[$i])
               ->count();
            */

            $existingAddon = MerchantProductAddon::withTrashed()
                                 ->where('merchant_product_id', $merchantProduct)
                                 ->where('addon_id', $request->idAddons[$i])
                                 ->first();

            if ($existingAddon) {
               
            /*
               $merchantProductToUpdate->addons()
               ->wherePivot('addon_id', $request->idAddons[$i])
               ->update([
               'price' => $request->priceAddons[$i],
               'merchant_menu_item_addons.deleted_at' => NULL,
               ]);
            */
           
               $existingAddon->update([
                  'price' => $request->priceAddons[$i] ?? 0,
                  'deleted_at' => NULL,
               ]);
                                
            }

            else {

               $merchantProductToUpdate->addons()
                                ->attach($request->idAddons[$i], [
                                    'price' => $request->priceAddons[$i] ?? 0
                                 ]
                              );

            }

         }

      /*
         $merchantProductToUpdate->update([
            'price' => min($request->priceAddons)
         ]);
      */

      }

      return $this->showMerchantAllProducts($request->merchant_id, $perPage);
   }

   public function deleteMerchantProduct($merchant, $merchantProduct, $perPage)
   {
      $menuItemToDelete = MerchantProduct::find($merchantProduct);
       
      if ($menuItemToDelete) {

         if ($menuItemToDelete->orders->count()) {
            // Variation deletion aint needed as not showing this product anymore
            // MerchantProductVariation::where('merchant_product_id', $merchantProduct)->delete();
            $menuItemToDelete->delete();
         }
         else {
            // Delete permanently as has no previous history
            MerchantProductVariation::where('merchant_product_id', $merchantProduct)->forceDelete();
            $menuItemToDelete->forceDelete();
         }
      
      }

      return $this->showMerchantAllProducts($merchant, $perPage);
   }

   public function restoreMerchantProduct($merchant, $merchantProduct, $perPage)
   {
      $menuItemToRestore = MerchantProduct::withTrashed()->find($merchantProduct);
       
      if ($menuItemToRestore ) {

         $menuItemToRestore->restore();
      
      }

      return $this->showMerchantAllProducts($merchant, $perPage);
   }

   public function searchMerchantAllProducts($merchant, $search, $perPage)
   {
      $query = MerchantProductCategory::with(['merchant', 'productCategory', 'merchantProducts.merchantProductVariations', 'merchantProducts.addons']);

      $query->where( function( $subquery )use ($search){

         $subquery->whereHas('productCategory', function($q) use ($search){
            $q->where('name', 'like', "%$search%");
         })->orWhereHas('merchantProducts', function($q) use ($search){
            $q->where("name", 'like', "%$search%");
            $q->orWhere("detail", 'like', "%$search%");
            $q->orWhere("price", 'like', "%$search%");
            $q->orWhere("name", 'like', "%$search%");
         });
      
      })->where('merchant_id', $merchant);

      return response()->json([
         'all' => $query->paginate($perPage),  
      ], 200);
   }

   public function updateMerchantProductStock(Request $request, $merchant, $perPage)
   {
      $request->validate([
         'id'=>'required|exists:merchant_products,id',
         'available'=>'required|boolean',
      ]);

      $merchantProductToUpdate = MerchantProduct::findOrFail($request->id);

      if ($merchantProductToUpdate->merchantProductCategory->merchant_id==$merchant) {
         
         $merchantProductToUpdate->update([
            'available' => $request->available
         ]);

      }

      return $this->showMerchantAllProducts($merchant, $perPage);
   }

   
   // Merchant-Product-Catehgories
   public function showAllMerchantProductCategories($perPage = false)
   {
      if ($perPage) {
         return response(Merchant::with('productCategories')->paginate($perPage), 200);
      }

      return response(Merchant::with('productCategories')->get(), 200);
   }

   public function searchAllMerchantProductCategories($search, $perPage)
   {
      $query = Merchant::with('productCategories')
                         ->where('name', 'like', "%$search%")
                         ->orWhereHas('productCategories', function($q)use ($search){
                           $q->where('name', 'like', "%$search%");
                         });

      return response()->json([
         'all' => $query->paginate($perPage),  
      ], 200);
   }

   // Merchant All Product-Categories
   public function showMerchantAllProductCategories($merchant, $perPage = false)
   {
      if ($perPage) {
         return response()->json([
            'current' => MerchantProductCategory::where('merchant_id', $merchant)->with(['productCategory', 'merchantProducts'])->paginate($perPage),

            'trashed' => MerchantProductCategory::onlyTrashed()->where('merchant_id', $merchant)->with(['productCategory', 'merchantProducts'])->paginate($perPage),

         ], 200);

         // return response(MerchantProductCategory::where('merchant_id', $merchant)->with(['merchant', 'productCategory', 'merchantProducts'])->paginate($perPage), 200);
      }

      return response(MerchantProductCategory::where('merchant_id', $merchant)->with(['productCategory', 'merchantProducts'])->get(), 200);
   }

   public function createMerchantProductCategory(Request $request, $perPage = false)
   {
      $request->validate([
         'productCategoriesId'=>'required|array|min:1',
         'productCategoriesId.*'  => "required|numeric|exists:product_categories,id",
         'serving_from'=>'nullable|string|max:20',
         'serving_to'=>'nullable|string|max:20',
         'discount'=>'numeric|min:0|max:100',
         'merchant_id'=>'required|numeric|exists:merchants,id',
      ]);

      $expectedMerchant = Merchant::find($request->merchant_id);

      // initially
      // $expectedMerchant->merchantProductCategories()->delete();

      foreach ($request->productCategoriesId as $newProductCategoryId) {
         
         $expectedMerchant->merchantProductCategories()->withTrashed()->updateOrCreate(
            [ 'product_category_id' => $newProductCategoryId ],
            [
               'serving_from' => $request->serving_from,
               'serving_to' => $request->serving_to,
               'discount' => $request->discount,
               'deleted_at' => NULL
            ]
         );

         /*
         if ($expectedMerchant->merchantProductCategories()->withTrashed()->where('product_category_id', $newProductCategoryId)->count()) {
            
            $expectedMerchant->merchantProductCategories()->withTrashed()->where('product_category_id', $newProductCategoryId)->update([
               'serving_from' => $request->serving_from,
               'serving_to' => $request->serving_to,
               'deleted_at' => NULL
            ]);
            
         }

         else{

            $expectedMerchant->productCategories()->syncWithoutDetaching([
               $newProductCategoryId => [
                  'serving_from' => $request->serving_from,
                  'serving_to' => $request->serving_to
               ]
            ]);

         }
         */

      }

      if ($request->fromMerchantProductIndex) {

         return $this->showMerchantAllProducts($request->merchant_id, $perPage);

      }
      else if ($request->fromProductCategoryIndex) {

         return $this->showAllMerchantProductCategories($perPage);

      }
      else {

         return $this->showMerchantAllProductCategories($request->merchant_id, $perPage);

      }

   }

   public function updateMerchantProductCategory(Request $request, $productCategory, $perPage)
   {
      $merchantProductCategoryToUpdate = MerchantProductCategory::find($productCategory);

      $request->validate([
         'productCategoriesId'=>'required|array|min:1',
         'productCategoriesId.*'  => "required|numeric|exists:product_categories,id",
         'productCategoriesId.0'  => Rule::unique('merchant_product_categories', 'product_category_id')
                                  ->where(function($query) use ($request) {
                                    $query->where('merchant_id', $request->merchant_id);
                                  })
                                  ->ignore($merchantProductCategoryToUpdate->id),
         'serving_from'=>'nullable|string|max:20',
         'serving_to'=>'nullable|string|max:20',
         'discount'=>'numeric|min:0|max:100',
         'merchant_id'=>'required|numeric|exists:merchants,id',
      ]);

      // As editing single merchantMenuCategroy from request
      $merchantProductCategoryToUpdate->product_category_id = $request->productCategoriesId[0];
      $merchantProductCategoryToUpdate->serving_from = $request->serving_from;
      $merchantProductCategoryToUpdate->serving_to = $request->serving_to;
      $merchantProductCategoryToUpdate->discount = $request->discount;

      $merchantProductCategoryToUpdate->save();

      return $this->showMerchantAllProductCategories($request->merchant_id, $perPage);
   }

   public function deleteMerchantProductCategory($productCategory, $perPage)
   {
      $merchantProductCategoryToDelete = MerchantProductCategory::findOrFail($productCategory);
       
      if ($merchantProductCategoryToDelete->merchantProducts()->withTrashed()->count() && $merchantProductCategoryToDelete->merchantProducts()->withTrashed()->has('orders')->count()) {

         $merchantProductCategoryToDelete->merchantProducts()->delete();
         $merchantProductCategoryToDelete->delete();
      
      }
      else {

         $merchantProductCategoryToDelete->merchantProducts()->forceDelete();
         $merchantProductCategoryToDelete->forceDelete();

      }

      return $this->showMerchantAllProductCategories($merchantProductCategoryToDelete->merchant_id, $perPage);
   }

   public function restoreMerchantProductCategory($productCategory, $perPage)
   {
      $merchantProductToRestore = MerchantProductCategory::onlyTrashed()->findOrFail($productCategory);
       
      // if ($merchantProductToRestore && $merchantProductToRestore->productCategory()->exists()) {

         $merchantProductToRestore->merchantProducts()->restore();
         $merchantProductToRestore->restore();
      
      // }

      return $this->showMerchantAllProductCategories($merchantProductToRestore->merchant_id, $perPage);
   }

   public function searchMerchantAllProductCategories($merchant, $search, $perPage)
   {
      $query = MerchantProductCategory::withTrashed()
                                       ->with(['merchant', 'productCategory', 'merchantProducts'])
                                       ->where("serving_from", 'like', "%$search%")
                                       ->orWhere("serving_to", 'like', "%$search%");
      
      $query->orWhereHas('productCategory', function($q) use ($search){
         $q->where('name', 'like', "%$search%");
      });

      $query->where('merchant_id', $merchant);

      return response()->json([
         'all' => $query->paginate($perPage),  
      ], 200);
   }

   public function showMerchantAllMeals($merchant, $perPage)
   {
      return response()->json([
         'meals' => MerchantMeal::with('meal')->where('merchant_id', $merchant)->paginate($perPage),  
      ], 200);
   }

   public function searchMerchantAllMeals($merchant, $search, $perPage)
   {
      $query = MerchantMeal::with('meal')
                              ->where('merchant_id', $merchant)
                              ->whereHas('meal', function($q) use ($search){
                                 $q->where('name', 'like', "%$search%");
                              });

      return response()->json([
         'all' => $query->paginate($perPage),  
      ], 200);
   }

   public function showMerchantAllCuisines($merchant, $perPage)
   {
      return response()->json([
         'cuisines' => MerchantCuisine::with('cuisine')->where('merchant_id', $merchant)->paginate($perPage),  
      ], 200);
   }

   public function searchMerchantAllCuisines($merchant, $search, $perPage)
   {
      $query = MerchantCuisine::with('cuisine')
                                 ->where('merchant_id', $merchant)
                                 ->whereHas('cuisine', function($q) use ($search){
                                    $q->where('name', 'like', "%$search%");
                                 });

      return response()->json([
         'all' => $query->paginate($perPage),  
      ], 200);
   }

   protected function generateProductSKU($merchantProductCategory, $merchantProduct)
    {
        $merchantProductCode = $merchantProductCategory.'P'.$merchantProduct;
        
        if (MerchantProduct::where('sku', $merchantProductCode)->exists()) {
            
            $merchantProductCode = $this->generateProductSKU($merchantProductCategory, rand(1, MerchantProduct::count()+1));

        }

        return $merchantProductCode;
    }

    protected function generateVariationSKU($sku, $variation)
    {
      return $sku.'V'.$variation;
    }
}
