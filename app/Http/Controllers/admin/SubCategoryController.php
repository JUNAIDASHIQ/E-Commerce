<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Faker\Core\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subCategoriesQuery = SubCategory::latest();

        // if (!empty($request->get('keyword'))) {
        //     $subCategoriesQuery->where('name', 'like', '%' . $request->get('keyword') . '%');
        // }

        $subCategories = $subCategoriesQuery->paginate(10);

        return view('admin.sub_category.list', compact('subCategories'));
    }
    public function create()
    {
        $categories['categories'] = Category::orderBy('name', 'ASC')->get();
        return view('admin.sub_category.create', $categories);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required',
        ]);
        if ($validator->passes()) {
            $subCategory = new SubCategory;
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->showHome = $request->showHome;
            $subCategory->save();
            // Save Image Here
            if (!empty($request->image_id)) {
                // $tempImage = TempImage::find($request->image_id);
                // $extArray = explode('.', $tempImage->name);
                // $ext = last($extArray);
                // $newImageName = $category->id . '.' . $ext;
                // $sourcePath = public_path() . '/temp/' . $tempImage->name;
                // $destinationPath = public_path() . '/uploads/category/' . $newImageName;
                // File::copy($sourcePath, $destinationPath);

                // // Generate Image Thumbnail
                // // $destinationPath = public_path() . '/uploads/category/thumb/' . $newImageName;
                // // $img = Image::make($sourcePath);
                // // // resize image to fixed size
                // // $img->resize(450, 600); // we use 'fit' except resize
                // // $img->fit(450, 600, function ($constraint) {
                // //     $constraint->upsize();
                // // });
                // // $img->save($destinationPath);

                // $category->image = $newImageName;
                // $category->save();
            }
            $request->session()->flash('success', 'Sub Category added successfully');
            return response()->json([
                'status' => true,
                'message' => "Sub Category added successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }
    public function edit($subCategory , Request $request )
    {
        // $sub_category['sub_category'] = SubCategory::where( 'id' ,$subCategory)->get();
        $sub_category['sub_category'] = SubCategory::find($subCategory);
        // echo $sub_category;die;
        return view('admin.sub_category.edit' , $sub_category);
    }
    public function update($categoryId , Request $request){
        $subCategory = SubCategory::find($categoryId);
        if (empty($subCategory)) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category Not Found',
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $subCategory->id . ',id',
        ]);
        if ($validator->passes()) {
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->showHome = $request->showHome;
            $subCategory->save();
            // $oldImage = $subCategory->image;
            // // Save Image Here
            // if (!empty($request->image_id)) {
            //     $tempImage = TempImage::find($request->image_id);
            //     $extArray = explode('.', $tempImage->name);
            //     $ext = last($extArray);

            //     $newImageName = $category->id . '-' . time() . '.' . $ext;
            //     $sourcePath = public_path() . '/temp/' . $tempImage->name;
            //     $destinationPath = public_path() . '/uploads/category/' . $newImageName;
            //     File::copy($sourcePath, $destinationPath);

            //     // Generate Image Thumbnail
            //     // $destinationPath = public_path() . '/uploads/category/thumb/' . $newImageName;
            //     // $img = Image::make($sourcePath);
            //     // // resize image to fixed size
            //     // $img->fit(450, 600, function ($constraint) {
            //     //     $constraint->upsize();
            //     // });
            //     // $img->resize(450, 600);
            //     // $img->save($destinationPath);

            //     $category->image = $newImageName;
            //     $category->save();

            //     // Delete Old Images after update new Image
            //     File::delete(public_path() . '/uploads/category/thumb/' . $oldImage);
            //     File::delete(public_path() . '/uploads/category/' . $oldImage);
            // }
            $request->session()->flash('success', 'Sub-Category Update Successfully');
            return response()->json([
                'status' => true,
                'message' => "Sub-Category Update Successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }
    public function destroy($categoryId , Request $request){
        // echo "Here";die;
        // echo $categoryId;die;
        $subCategory = SubCategory::find($categoryId);
        if (empty($subCategory)) {
            $request->session()->flash('error' , 'Category Not Found');
            return response()->json([
                'status' => true,
                'message' => 'Category Not Found'
            ]);
            // return redirect()->route('categories.index');
        }
        else{
            // File::delete(public_path() . '/uploads/category/thumb/' . $subCategory->image);
            // File::delete(public_path() . '/uploads/category/' . $subCategory->image);
            $subCategory->delete();
            $request->session()->flash('success', 'Category Delete successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category Delete Successfully'
            ]);

        }
    }
}
