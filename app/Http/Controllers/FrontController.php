<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(){
        $featuredProducts['featuredProducts'] =  Product::where('is_featured' , 'Yes')->where('status' , 1)->get();
        return view('front.home' , $featuredProducts);
    }
}
