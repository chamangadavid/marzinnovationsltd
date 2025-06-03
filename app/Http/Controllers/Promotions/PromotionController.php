<?php

namespace App\Http\Controllers\Promotions;

use App\Http\Controllers\Controller;
use App\Models\Promotions\Promotion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    public function index()
    {
        return Inertia::render('MyMARZ/Promotions/Index');
    }

    public function getPromotion(Request $request)
    {
        $query = Promotion::query();

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('start_date', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        }

        // Status filter
        if ($request->has('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $promotion = Promotion::create($request->all());

        return response()->json($promotion, 201);
    }

    public function show(Promotion $promotion)
    {
        return $promotion;
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return response()->json(null, 204);
    }

    public function getPromotions()
    {
        $promotions = Promotion::latest()->get(); // Orders by created_at descending
        return response()->json($promotions);
    }

    // In your PromotionController.php
    public function showPromotion($id)
    {
        $promotion = Promotion::findOrFail($id);
        
        return Inertia::render('MyMARZ/Promotions/promotionDetails', [
            'promotion' => $promotion
        ]);
    }



}
