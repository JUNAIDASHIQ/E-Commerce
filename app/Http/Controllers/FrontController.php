<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(){
        $data['featuredProducts'] =  Product::where('is_featured' , 'Yes')->where('status' , 1)->get();
        $data['latestProducts'] =  Product::orderBy('id' , 'DESC')->where('status' , 1)->take(8)->get();
        return view('front.home' , $data);
    }
}
