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
use App\Http\Resources\Api\MerchantProductResource;
use App\Http\Resources\Api\SponsorMerchantResource;
use App\Http\Resources\Api\SearchMerchantCollection;
use App\Http\Resources\Api\SponsorMerchantCollection;
use App\Http\Resources\Api\PromotionalMerchantProductCollection;

class MerchantController extends Controller
{
    public function getMerchants(Request $request)
    {
      $request->validate([
        'latitude' => 'required|string',
        'longitude' => 'required|string',
        'per_page' => 'nullable|numeric',
        // 'preference' => 'nullable',
        'preference.type' => [ 'nullable', 'string', 'in:meals,cuisines,product_categories' ],
        'preference.ids' => 'nullable|array|required_with:preference.type',
        'preference.ids.*' => [
          'required_with:preference.type',
          function ($attribute, $value, $fail) use ($request) {
            if ($request->input('preference.type') === 'meals' && Meal::find($value) === null) {
              $fail('Invalid meal preference.');
            }
            else if ($request->input('preference.type') === 'cuisines' && Cuisine::find($value) === null) {
              $fail('Invalid cuisine preference.');
            }
            else if ($request->input('preference.type') === 'product_categories' && ProductCategory::find($value) === null) {
              $fail('Invalid category preference.');
            }
          },
        ]
      ]);

      $merchantsInArea = Merchant::where('is_approved', 1)->where('is_open', 1)
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
        else if ($request->preference['type']=='product_categories') {

          $merchants = $merchantsInArea->whereHas('productCategories', function ($query) use ($request) {
            $query->whereIn('product_category_id', $request->preference['ids']);
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
      return new MerchantResource(Merchant::with(['merchantMeals', 'merchantCuisines', 'merchantProductCategories.merchantProducts.variations', 'reviews'])->findOrFail($merchant));
    }

    public function getSponsorMerchants(Request $request)
    {
      $request->validate([
        'latitude' => 'required|string',
        'longitude' => 'required|string',
        'per_page' => 'nullable|numeric',
        // 'preference' => 'nullable',
        /*
        'preference.type' => [ 'nullable', 'string', 'in:meals,cuisines,product_categories' ],
        'preference.ids' => 'nullable|array|required_with:preference.type,',
        'preference.ids.*' => [
          'required_with:preference.type,',
          function ($attribute, $value, $fail) use ($request) {
            if ($request->input('preference.type') === 'meals' && Meal::find($value) === null) {
              $fail('Invalid meal preference.');
            }
            else if ($request->input('preference.type') === 'cuisines' && Cuisine::find($value) === null) {
              $fail('Invalid cuisine preference.');
            }
            else if ($request->input('preference.type') === 'product_categories' && ProductCategory::find($value) === null) {
              $fail('Invalid category preference.');
            }
          },
        ]
        */
      ]);

      $merchantsInArea = Merchant::where('is_approved', 1)
      ->where('is_open', 1)->where('is_sponsored', 1)
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
        else if ($request->preference['type']=='product_categories') {

          $merchants = $merchantsInArea->whereHas('productCategories', function ($query) use ($request) {
            $query->whereIn('product_category_id', $request->preference['ids']);
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

    public function getPromotionalMerchantProducts(Request $request)
    {
      $request->validate([
        'latitude' => 'required|string',
        'longitude' => 'required|string',
        'per_page' => 'nullable|numeric'
      ]);

      $merchantsInArea = MerchantProduct::where('is_promoted', 1)
      ->whereHas('merchantProductCategory.merchant', function ($query) use ($request) {
          $query->where('is_open', 1)->where('is_approved', 1)
          ->whereBetween('lat', [intval($request->latitude-1), intval($request->latitude+1)])
          ->whereBetween('lng', [intval($request->longitude-1), intval($request->longitude+1)]);
      });

      // pagination
      if ($request->per_page) {
        return new PromotionalMerchantProductCollection($merchantsInArea->paginate($request->per_page));
      }
      else {
        // return new MerchantCollection(); // aggregations, products
        return MerchantProductResource::collection($merchantsInArea->get());
      }     

    }

    /*
    public function getMerchantProducts(Request $request)
    {
      $request->validate([
        'id' => 'required|numeric|exists:merchants,id'
      ]);

      $merchantProducts =  Merchant::findOrFail($request->id)->with(['productCategories.productCategory', 'productCategories.merchantProducts.merchantProductVariations', 'productCategories.merchantProducts.merchantProductAddons'])->get();

      return MerchantProductResource::collection($merchantProducts);
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
