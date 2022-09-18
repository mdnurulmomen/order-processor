<?php

namespace App\Http\Controllers\Api;

use App\Models\Meal;
use App\Models\Review;
use App\Models\Cuisine;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\MerchantProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MerchantResource;
use App\Http\Resources\Api\MerchantReviewResource;
use App\Http\Resources\Api\SearchMerchantResource;
use App\Http\Resources\Api\MerchantReviewCollection;
use App\Http\Resources\Api\SponsorMerchantResource;
use App\Http\Resources\Api\SearchMerchantCollection;
use App\Http\Resources\Api\SponsorMerchantCollection;
use App\Http\Resources\Api\PromotionalProductCollection;
use App\Http\Resources\Api\MerchantProductDetailResource;

class MerchantController extends Controller
{
    public function getMerchants(Request $request)
    {
      $request->validate([
        'latitude' => 'required|string',
        'longitude' => 'required|string',
        'per_page' => 'nullable|numeric',
        // 'preference' => 'nullable',
        'preference.type' => [ 'nullable', 'string', 'in:meals,cuisines,menus' ],
        'preference.ids' => 'nullable|array|required_unless:preference.type,',
        'preference.ids.*' => [
          'required_unless:preference.type,',
          function ($attribute, $value, $fail) use ($request) {
            if ($request->input('preference.type') === 'meals' && Meal::find($value) === null) {
              $fail('Invalid meal preference.');
            }
            else if ($request->input('preference.type') === 'cuisines' && Cuisine::find($value) === null) {
              $fail('Invalid cuisine preference.');
            }
            else if ($request->input('preference.type') === 'menus' && ProductCategory::find($value) === null) {
              $fail('Invalid menu preference.');
            }
          },
        ]
      ]);

      $merchantsInArea = Merchant::where('admin_approval', 1)->where('taking_order', 1)
      ->whereBetween('lat', [intval($request->latitude-1), intval($request->latitude+1)])
      ->whereBetween('lng', [intval($request->longitude-1), intval($request->longitude+1)]);

      // preferences
      if (! empty($request->preference['type']) && ! empty($request->preference['ids'])) {

        if ($request->preference['type']=='meals') {

          $merchants = $merchantsInArea->whereHas('meals', function ($query) use ($request) {
            $query->whereIn('meal_id', $request->preference['ids']);
          });

        }
        else if ($request->preference['type']=='cuisines') {

          $merchants = $merchantsInArea->whereHas('cuisines', function ($query) use ($request) {
            $query->whereIn('cuisine_id', $request->preference['ids']);
          });

        }
        else if ($request->preference['type']=='menus') {

          $merchants = $merchantsInArea->whereHas('menuCategories', function ($query) use ($request) {
            $query->whereIn('menu_category_id', $request->preference['ids']);
          });

        }

      }
      else {

        $merchants = $merchantsInArea;

      }

      // pagination
      if ($request->per_page) {
        return new SearchMerchantCollection($merchants->paginate($request->per_page));
      }
      else {
        // return new MerchantCollection(); // aggregations, products
        return SearchMerchantResource::collection($merchants->get());
      }

    }

    public function showMerchantDetails($merchant)
    {
      return new MerchantResource(Merchant::with(['booking', 'merchantMeals', 'merchantCuisines', 'merchantMenuCategories.menuItems.variations', 'reviews'])->findOrFail($merchant));
    }

    public function getSponsorMerchants(Request $request)
    {
      $request->validate([
        'latitude' => 'required|string',
        'longitude' => 'required|string',
        'per_page' => 'nullable|numeric',
        // 'preference' => 'nullable',
        /*
        'preference.type' => [ 'nullable', 'string', 'in:meals,cuisines,menus' ],
        'preference.ids' => 'nullable|array|required_unless:preference.type,',
        'preference.ids.*' => [
          'required_unless:preference.type,',
          function ($attribute, $value, $fail) use ($request) {
            if ($request->input('preference.type') === 'meals' && Meal::find($value) === null) {
              $fail('Invalid meal preference.');
            }
            else if ($request->input('preference.type') === 'cuisines' && Cuisine::find($value) === null) {
              $fail('Invalid cuisine preference.');
            }
            else if ($request->input('preference.type') === 'menus' && ProductCategory::find($value) === null) {
              $fail('Invalid menu preference.');
            }
          },
        ]
        */
      ]);

      $merchantsInArea = Merchant::where('admin_approval', 1)
      ->where('taking_order', 1)->where('sponsored', 1)
      ->whereBetween('lat', [intval($request->latitude-1), intval($request->latitude+1)])
      ->whereBetween('lng', [intval($request->longitude-1), intval($request->longitude+1)]);

      // preferences
      /*
      if (! empty($request->preference['type']) && ! empty($request->preference['ids'])) {

        if ($request->preference['type']=='meals') {

          $merchants = $merchantsInArea->whereHas('meals', function ($query) use ($request) {
            $query->whereIn('meal_id', $request->preference['ids']);
          });

        }
        else if ($request->preference['type']=='cuisines') {

          $merchants = $merchantsInArea->whereHas('cuisines', function ($query) use ($request) {
            $query->whereIn('cuisine_id', $request->preference['ids']);
          });

        }
        else if ($request->preference['type']=='menus') {

          $merchants = $merchantsInArea->whereHas('menuCategories', function ($query) use ($request) {
            $query->whereIn('menu_category_id', $request->preference['ids']);
          });

        }

      }
      else {

        $merchants = $merchantsInArea;

      }
      */

      // pagination
      if ($request->per_page) {
        return new SponsorMerchantCollection(/*$merchants->paginate($request->per_page)*/ $merchantsInArea->paginate($request->per_page));
      }
      else {
        // return new MerchantCollection(); // aggregations, products
        return SponsorMerchantResource::collection(/*$merchants->get()*/ $merchantsInArea->get());
      }     

    }

    public function getPromotionalMenuItems(Request $request)
    {
      $request->validate([
        'latitude' => 'required|string',
        'longitude' => 'required|string',
        'per_page' => 'nullable|numeric'
      ]);

      $merchantsInArea = MerchantProduct::where('promoted', 1)
      ->whereHas('merchantMenuCategory.merchant', function ($query) use ($request) {
          $query->where('taking_order', 1)->where('admin_approval', 1)
          ->whereBetween('lat', [intval($request->latitude-1), intval($request->latitude+1)])
          ->whereBetween('lng', [intval($request->longitude-1), intval($request->longitude+1)]);
      });

      // pagination
      if ($request->per_page) {
        return new PromotionalProductCollection($merchantsInArea->paginate($request->per_page));
      }
      else {
        // return new MerchantCollection(); // aggregations, products
        return MerchantProductDetailResource::collection($merchantsInArea->get());
      }     

    }

    /*
    public function getMerchantMenuItems(Request $request)
    {
      $request->validate([
        'id' => 'required|numeric|exists:merchants,id'
      ]);

      $merchantMenuItems =  Merchant::findOrFail($request->id)->with(['menuCategories.menuCategory', 'menuCategories.merchantMenuItems.merchantMenuItemVariations', 'menuCategories.merchantMenuItems.merchantMenuItemAddons'])->get();

      return MerchantMenuItemResource::collection($merchantMenuItems);
    }
    */

    public function getMerchantReviews($id, $per_page=false)
    {
        $expectedMerchant = Merchant::findOrFail($id);

        if ($per_page) {
          return new MerchantReviewCollection(Review::where('reviewable_type', 'App/Models/Merchant')->where('reviewable_id', $id)->paginate($per_page));
        }

        return MerchantReviewResource::collection($expectedMerchant->reviews);
    }

    public function addMerchantReview(Request $request)
    {
        $request->validate([
          'rating' => 'required|numeric',
          'order_id' => 'required|numeric|exists:orders,id',
          'customer_id' => 'required|numeric|exists:customers,id',
          'merchant_id' => 'required|numeric|exists:merchants,id'
        ]);

        $expectedMerchant = Merchant::findOrFail($request->merchant_id);

        $expectedMerchant->reviews()->updateOrCreate(
          [ 'order_id' => $request->order_id, 'customer_id' => $request->customer_id ],
          [ 'rating' => $request->rating ]
        );

        return $this->getMerchantReviews($expectedMerchant->id);
    }
}
