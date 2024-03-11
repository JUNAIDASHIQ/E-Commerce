<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        // $brands['brands'] = Brand::all();
        $brandsarray = Brand::latest();
        if (!empty($request->get('keyword'))) {
            $brandsarray =  Brand::where('name', 'like', '%' . $request->get('keyword') . '%');
        }
        $brands['brands'] = $brandsarray->paginate(10);
        return view('admin.brands.list', $brands);
    }
    public function create()
    {
        return view('admin.brands.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands'
        ]);
        if ($validator->passes()) {
            $brand = new Brand;
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();
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
    public function edit($brandId)
    {
        $brand['brand'] = Brand::find($brandId);
        return view('admin.brands.edit', $brand);
    }
    public function update($brandId, Request $request)
    {
        $brand = Brand::find($brandId);
        if (empty($brand)) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category Not Found',
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $brand->id . ',id',
        ]);
        if ($validator->passes()) {
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();
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
    public function destroy($BrandID ,  Request $request)
    {
        $brand = Brand::find($BrandID);
        if (empty($brand)) {
            $request->session()->flash('error' , 'Category Not Found');
            return response()->json([
                'status' => true,
                'message' => 'Category Not Found'
            ]);
        }
        else{
            $brand->delete();
            $request->session()->flash('success', 'Category Delete successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category Delete Successfully'
            ]);

        }
    }
}
