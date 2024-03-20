<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // public function index(Request $request)
    // {
    //     $data = Product::latest();
    //     if (!empty($request->get('keyword'))) {
    //         $data =  Product::where('title', 'like', '%' . $request->get('keyword') . '%');
    //     }
    //     $data['products'] = $data->paginate(10);
    //     return view('admin.products.list' , $data);
    // }
    public function index(Request $request)
    {
        // $query['products'] = DB::table('products')
        // ->join('product_images', 'products.id', '=', 'product_images.product_id')
        // ->select('products.*', 'product_images.*')
        // ->get();
        // dd($query);exit;
        $query = Product::latest('id')->with('product_images');
        // dd($query);exit;
        if (!empty($request->get('keyword'))) {
            $query->where('title', 'like', '%' . $request->get('keyword') . '%');
        }
        $products = $query->paginate(10);
        return view('admin.products.list', compact('products'));
    }

    public function create()
    {
        $data['categories'] = Category::orderBy('name', 'ASC')->get();
        $data['brands'] = Brand::orderBy('name', 'ASC')->get();
        return view('admin.products.create', $data);
    }
    public function store(Request $request)
    {
        $rules =  [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required',
            'is_featured' => 'required|in:Yes,No',
        ];
        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $product = new Product;
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->short_description = $request->short_description;
            $product->description = $request->description;
            $product->shipping_returns = $request->shipping_returns;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brands;
            $product->is_featured = $request->is_featured;
            $product->save();
            // $oldImage = $product->image;
            // Save Image Here
            if (!empty($request->image_id)) {
                // echo "Image Id" . $request->image_id;
                foreach ($request->image_id as $temp_image_id) {
                    $tempImageInfo = TempImage::find($temp_image_id);

                    // $extArray = explode('.', $tempImageInfo->name);
                    // $ext = last($extArray);

                    // $productImage = new ProductImage;
                    // $productImage->product_id = $product->id;
                    // $productImage->image = 'NULL';
                    // $productImage->save();
                    // $imageName = $product->id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                    // $productImage->image = $imageName;
                    // $productImage->save();
                    // $sourcePath = public_path() . '/temp/' . $tempImageInfo->name;
                    // $destinationPath = public_path() . '/uploads/product/large/' . $tempImageInfo->name;
                    // File::copy($sourcePath, $destinationPath);
                    // Copy the image to product_images table
                    $productImage = new ProductImage;
                    $productImage->product_id = $product->id;
                    $productImage->image = $tempImageInfo->name; // Assuming 'name' field contains image name
                    $productImage->save();

                    // Move image from temp directory to product image directory
                    $sourcePath = public_path() . '/temp/' . $tempImageInfo->name;
                    $destinationPath = public_path() . '/uploads/product/large/' . $tempImageInfo->name;
                    File::copy($sourcePath, $destinationPath);
                }
            }
            $request->session()->flash('success', 'Product added successfully');
            return response()->json([
                'status' => true,
                'message' => "Product added successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }

    public function edit($productId)
    {
        // $product['product'] = Product::find($productId)->with('product_images');
        $product['product'] = Product::with('product_images')->find($productId);
        $product['categories'] = Category::orderBy('name', 'ASC')->get();
        $product['brands'] = Brand::orderBy('name', 'ASC')->get();
        if (empty($product)) {
            return redirect()->route('products.index');
        } else {
            return view('admin.products.edit', $product);
        }
    }
    public function update($productId, Request $request)
    {
        $product = Product::find($productId);
        if (empty($product)) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Product Not Found',
            ]);
        }
        // Delete old product images
        foreach ($product->product_images as $productImage) {
            // Delete image file from storage
            if (Storage::exists('uploads/product/large/' . $productImage->image)) {
                Storage::delete('uploads/product/large/' . $productImage->image);
            }
            // Delete the product image record from the database
            $productImage->delete();
        }
        $validator = Validator::make($request->all(), [
            // 'title' => 'required',
            // 'slug' => 'required|unique:products,slug,' . $product->id . ',id',
            'title' => 'required',
            'slug' => 'required',
            'price' => 'required',
            'sku' => 'required',
            'track_qty' => 'required',
            'category' => 'required',
            'is_featured' => 'required',
        ]);
        if ($validator->passes()) {
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->short_description = $request->short_description;
            $product->description = $request->description;
            $product->shipping_returns = $request->shipping_returns;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brands;
            $product->brand_id = $request->brands;
            $product->is_featured = $request->is_featured;
            $product->save();
            if (!empty($request->image_id)) {
                foreach ($request->image_id as $temp_image_id) {
                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.', $tempImageInfo->name);
                    $ext = end($extArray);

                    // Create a new product image instance
                    $productImage = new ProductImage;
                    $productImage->product_id = $product->id;

                    // Save the product image with the same name as the temporary image
                    $productImage->image = $tempImageInfo->name;
                    $productImage->save();

                    // Move the temporary image to the product images directory
                    $sourcePath = public_path() . '/temp/' . $tempImageInfo->name;
                    $destinationPath = public_path() . '/uploads/product/large/' . $tempImageInfo->name;
                    File::move($sourcePath, $destinationPath);

                    // Delete the temporary image record from the database
                    $tempImageInfo->delete();
                }
            }
            $request->session()->flash('success', 'Product added successfully');
            return response()->json([
                'status' => true,
                'message' => "Product added successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }

    public function destroy($categoryId,  Request $request)
    {
        $product = Product::find($categoryId);
        if (empty($product)) {
            $request->session()->flash('error', 'Product Not Found');
            return response()->json([
                'status' => true,
                'message' => 'Category Not Found'
            ]);
            // return redirect()->route('categories.index');
        } else {
            File::delete(public_path() . '/uploads/category/thumb/' . $product->image);
            File::delete(public_path() . '/uploads/category/' . $product->image);
            $product->delete();
            $request->session()->flash('success', 'Category Delete successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category Delete Successfully'
            ]);
        }
    }
}
