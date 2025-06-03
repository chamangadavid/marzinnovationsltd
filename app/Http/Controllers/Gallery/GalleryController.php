<?php

namespace App\Http\Controllers\Gallery;

use App\Http\Controllers\Controller;
use App\Models\Galleries\Gallery;
use App\Models\Galleries\GalleryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class GalleryController extends Controller
{
    public function index()
    {
        return Inertia::render('MyMARZ/Gallery/Index');
    }

    // public function getAllGallery(Request $request)
    // {
    //     $query = Gallery::query();

    //     // Search functionality
    //     if ($request->has('search')) {
    //         $search = $request->input('search');
    //         $query->where(function($q) use ($search) {
    //             $q->where('title', 'like', "%{$search}%")
    //               ->orWhere('description', 'like', "%{$search}%");
    //         });
    //     }

    //     // Date range filter
    //     if ($request->has(['start_date', 'end_date'])) {
    //         $query->whereBetween('created_at', [
    //             $request->input('start_date'),
    //             $request->input('end_date')
    //         ]);
    //     }

    //     return $query->get();
    // }

    public function getAllGallery(Request $request)
    {
        $query = Gallery::with('category');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        }

        return $query->get();
    }

   
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'images' => 'required|array',
    //         'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
    //         'description' => 'nullable|string'
    //     ]);

    //     $imagePaths = [];
    //     if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $image) {
    //             $path = $image->store('gallery', 'public');
    //             $imagePaths[] = Storage::url($path);
    //         }
    //     }

    //     $gallery = Gallery::create([
    //         'title' => $request->title,
    //         'images' => $imagePaths,
    //         'description' => $request->description
    //     ]);

    //     return response()->json($gallery, 201);
    // }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:gallery_categories,id'
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('gallery', 'public');
                $imagePaths[] = Storage::url($path);
            }
        }

        $gallery = Gallery::create([
            'title' => $request->title,
            'images' => $imagePaths,
            'description' => $request->description,
            'category_id' => $request->category_id
        ]);

        return response()->json($gallery->load('category'), 201);
    }

    public function show(Gallery $gallery)
    {
        return $gallery;
    }

    // public function destroy(Gallery $gallery)
    // {
    //     // Delete associated images
    //     foreach ($gallery->images as $image) {
    //         $path = str_replace('/storage', 'public', $image);
    //         Storage::delete($path);
    //     }

    //     $gallery->delete();
    //     return response()->json(null, 204);
    // }

    public function destroy(Gallery $gallery)
    {
        try {
            // Check if images exist and is an array before trying to delete
            if (!empty($gallery->images)) {
                foreach ((array)$gallery->images as $image) {
                    $path = str_replace('/storage', 'public', $image);
                    Storage::delete($path);
                }
            }

            $gallery->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Gallery deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete gallery',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function displayGalleries(Request $request)
    {
        $query = Gallery::with('category')
            ->orderBy('created_at', 'desc');

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        return $query->paginate($request->per_page ?? 12);
    }

}
