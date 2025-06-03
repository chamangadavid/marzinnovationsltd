<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Models\News\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class NewsController extends Controller
{
    
    public function index()
    {
        return Inertia::render('MyMARZ/News/Index');
    }

    public function getNews(Request $request)
    {
        $query = News::query();
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $news = $query->latest()->paginate(10);
        
        return response()->json([
            'data' => $news->items(),
            'meta' => [
                'total' => $news->total(),
                'current_page' => $news->currentPage(),
                'per_page' => $news->perPage(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        
        $imagePaths = [];
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('news', 'public');
                $imagePaths[] = Storage::url($path);
            }
        }
        
        $news = News::create([
            'title' => $request->title,
            'description' => $request->description,
            'images' => $imagePaths,
        ]);
        
        return response()->json($news, 201);
    }

    public function publish(News $news)
    {
        $news->update(['is_published' => true]);
        return response()->json($news);
    }

    public function unpublish(News $news)
    {
        $news->update(['is_published' => false]);
        return response()->json($news);
    }

    public function destroy(News $news)
    {
        // Delete associated images
        if ($news->images) {
            foreach ($news->images as $image) {
                $path = str_replace('/storage', '', $image);
                Storage::disk('public')->delete($path);
            }
        }
        
        $news->delete();
        
        return response()->json(null, 204);
    }

    //front end
    // In NewsController.php

    public function getLatestNews(Request $request)
    {
        $query = News::query();

        if ($request->has('is_published')) {
            $query->where('is_published', $request->is_published);
        }

        $news = $query->orderByDesc('created_at')->get();

        // Add a full URL to the first image of each news item
        $news->transform(function ($item) {
            // If images array exists and has at least one image
            if (is_array($item->images) && count($item->images) > 0) {
                $item->image = asset("assets/news/" . $item->images[0]);
            } else {
                $item->image = null; // or a default placeholder path
            }
            return $item;
        });

        return response()->json([
            'data' => $news
        ]);
    }

    public function showNews($id)
    {
        $news = News::findOrFail($id);
        
        return Inertia::render('MyMARZ/News/newsDetails', [
            'news' => $news
        ]);
    }







}
