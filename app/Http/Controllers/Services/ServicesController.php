<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Service\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ServicesController extends Controller
{

    public function index()
    {
        return Inertia::render('MyMARZ/Services/Index');
    }

    // public function getServices(Request $request)
    // {
    //     $query = Services::query();
        
    //     if ($request->has('search') && $request->search) {
    //         $query->where(function($q) use ($request) {
    //             $q->where('title', 'like', '%' . $request->search . '%')
    //               ->orWhere('description', 'like', '%' . $request->search . '%');
    //         });
    //     }
        
    //     $services = $query->latest()->paginate(10);
        
    //     return response()->json([
    //         'data' => $services->items(),
    //         'meta' => [
    //             'total' => $services->total(),
    //             'current_page' => $services->currentPage(),
    //             'per_page' => $services->perPage(),
    //         ]
    //     ]);
    // }

    public function getServices(Request $request)
{
    $query = Services::query();
    
    if ($request->has('search') && $request->search) {
        $query->where(function($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
        });
    }
    
    // Add these lines for date filtering
    if ($request->has('start_date') && $request->start_date) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }
    
    if ($request->has('end_date') && $request->end_date) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }
    
    $services = $query->latest()->paginate(10);
    
    return response()->json([
        'data' => $services->items(),
        'meta' => [
            'total' => $services->total(),
            'current_page' => $services->currentPage(),
            'per_page' => $services->perPage(),
        ]
    ]);
}

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'boolean',
        ]);
        
        $imagePaths = [];
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('services/images', 'public');
                $imagePaths[] = Storage::url($path);
            }
        }
        
        $service = Services::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'images' => $imagePaths,
            'is_active' => $request->is_active ?? true,
        ]);
        
        return response()->json($service, 201);
    }

    public function show(Services $service)
    {
        return response()->json($service);
    }

    public function update(Request $request, Services $service)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'images.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'sometimes|boolean',
        ]);
        
        $imagePaths = $service->images ?? [];
        
        if ($request->hasFile('images')) {
            // Delete old images
            foreach ($service->images as $image) {
                $path = str_replace('/storage', '', $image);
                Storage::disk('public')->delete($path);
            }
            
            // Upload new images
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('services/images', 'public');
                $imagePaths[] = Storage::url($path);
            }
        }
        
        $service->update([
            'title' => $request->title ?? $service->title,
            'description' => $request->description ?? $service->description,
            'price' => $request->price ?? $service->price,
            'images' => $imagePaths,
            'is_active' => $request->has('is_active') ? $request->is_active : $service->is_active,
        ]);
        
        return response()->json($service);
    }

    public function destroy(Services $service)
    {
        // Delete associated images
        if ($service->images) {
            foreach ($service->images as $image) {
                $path = str_replace('/storage', '', $image);
                Storage::disk('public')->delete($path);
            }
        }
        
        $service->delete();
        
        return response()->json(null, 204);
    }

    public function toggleStatus(Services $service)
    {
        $service->update(['is_active' => !$service->is_active]);
        return response()->json($service);
    }

    public function getAllServices()
    {
        $services = Services::where('is_active', true)->get();
        return response()->json($services);
    }

   



}
