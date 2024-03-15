<?php

use App\Models\Category;

function getCategories()
{
    return Category::orderBy('name', 'ASC')
        ->with('sub_categories')
        ->where('status' , 1)
        ->where('showHome', 'Yes')
        ->get();
}