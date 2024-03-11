<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
// use Image; // at the beginning of your file
use Intervention\Image\Facades\Image as ImageFacade;



class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::latest();
        if (!empty($request->get('keyword'))) {
            $categories =  Category::where('name', 'like', '%' . $request->get('keyword') . '%');
        }
        $category['categories'] = $categories->paginate(10);
        return view('admin.category.list', $category);
    }
    public function create()
    {
        return view('admin.category.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);
        if ($validator->passes()) {
            $category = new Category;
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();
            // Save Image Here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id . '.' . $ext;
                $sourcePath = public_path() . '/temp/' . $tempImage->name;
                $destinationPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sourcePath, $destinationPath);

                // Generate Image Thumbnail
                // $destinationPath = public_path() . '/uploads/category/thumb/' . $newImageName;
                // $img = Image::make($sourcePath);
                // // resize image to fixed size
                // $img->resize(450, 600); // we use 'fit' except resize
                // $img->fit(450, 600, function ($constraint) {
                //     $constraint->upsize();
                // });
                // $img->save($destinationPath);

                $category->image = $newImageName;
                $category->save();
            }
            $request->session()->flash('success', 'Category added successfully');
            return response()->json([
                'status' => true,
                'message' => "Category added successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }
    public function edit($categoryId, Request $request)
    {
        $category['category'] = Category::find($categoryId);
        if (empty($category)) {
            return redirect()->route('categories.index');
        } else {
            return view('admin.category.edit', $category);
        }
    }
    public function update($categoryId, Request $request)
    {
        $category = Category::find($categoryId);
        if (empty($category)) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category Not Found',
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $category->id . ',id',
        ]);
        if ($validator->passes()) {
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();
            $oldImage = $category->image;
            // Save Image Here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . '-' . time() . '.' . $ext;
                $sourcePath = public_path() . '/temp/' . $tempImage->name;
                $destinationPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sourcePath, $destinationPath);

                // Generate Image Thumbnail
                // $destinationPath = public_path() . '/uploads/category/thumb/' . $newImageName;
                // $img = Image::make($sourcePath);
                // // resize image to fixed size
                // $img->fit(450, 600, function ($constraint) {
                //     $constraint->upsize();
                // });
                // $img->resize(450, 600);
                // $img->save($destinationPath);

                $category->image = $newImageName;
                $category->save();

                // Delete Old Images after update new Image
                File::delete(public_path() . '/uploads/category/thumb/' . $oldImage);
                File::delete(public_path() . '/uploads/category/' . $oldImage);
            }
            $request->session()->flash('success', 'Category added successfully');
            return response()->json([
                'status' => true,
                'message' => "Category added successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }
    public function destroy($categoryId ,  Request $request)
    {
        $category = Category::find($categoryId);
        if (empty($category)) {
            $request->session()->flash('error' , 'Category Not Found');
            return response()->json([
                'status' => true,
                'message' => 'Category Not Found'
            ]);
            // return redirect()->route('categories.index');
        }
        else{
            File::delete(public_path() . '/uploads/category/thumb/' . $category->image);
            File::delete(public_path() . '/uploads/category/' . $category->image);
            $category->delete();
            $request->session()->flash('success', 'Category Delete successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category Delete Successfully'
            ]);

        }
    }
}
