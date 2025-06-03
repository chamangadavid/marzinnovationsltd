<?php

namespace App\Http\Controllers\Gallery;

use App\Http\Controllers\Controller;
use App\Models\Galleries\GalleryCategory;
use Illuminate\Http\Request;

class GalleryCategoryController extends Controller
{

    public function index()
    {
        return GalleryCategory::all();
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:gallery_categories,name'
        ]);

        $category = GalleryCategory::create($request->only('name'));

        return response()->json($category, 201);
    }
}
