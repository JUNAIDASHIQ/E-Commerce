<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        $categorySelected = '';
        $subCategorySelected = '';
        $brandsArray = [];

        $categories = Category::orderBy('name', 'ASC')->with('sub_categories')->where('status', 1)->get();
        $brands = Brand::orderBy('name', 'ASC')->where('status', 1)->get();
        $products = Product::where('status', 1);
        // Apply Filters Here
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id', $category->id);
            $categorySelected = $category->id;
        }
        if (!empty($subCategorySlug)) {
            $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
            $products = $products->where('sub_category_id', $subCategory->id);
            $subCategorySelected = $subCategory->id;
        }
        if (!empty($request->get('brand'))) {
            $brandsArray = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brandsArray);
        }
        // Products Fetch According to Price Filter
        if ($request->has('price_min') && $request->has('price_max')) {
            if ($request->has('price_min') == 1000) {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), 100000]);
            }
            $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
        }
        // For Sorting Products According to High/Low Price
        if ($request->has('sort')) {
            $sortOption = $request->get('sort');
            if ($sortOption == 'latest') {
                $products = $products->orderBy('created_at', 'desc');
            } elseif ($sortOption == 'price_asc') {
                $products = $products->orderBy('price', 'asc');
            } elseif ($sortOption == 'price_desc') {
                $products = $products->orderBy('price', 'desc');
            } else {
                // Default sorting option
                $products = $products->orderBy('created_at', 'desc');
            }
        }

        $products = $products->paginate(6);
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['subCategorySelected'] = $subCategorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['priceMin'] = intval($request->get('price_min'));
        $data['priceMax'] = (intval($request->get('price_max')) == 0) ? 1000  : $request->get('price_max');;
        return view('front.shop', $data);
    }

    //Product For Shoppin
    public function product($slug){
        // echo $slug;
        $product = Product::where('slug' , $slug)->with('product_images')->first();
        if ($product == null) {
            abort(404);
        }
        $data['product'] = $product;
        return view('front.product' , $data);
    }
}
