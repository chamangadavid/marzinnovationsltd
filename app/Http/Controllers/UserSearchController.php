<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserSearchController extends Controller
{

    public function search(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:2|max:255'
            ]);

            $searchQuery = $request->input('query');

            $users = User::query()
                ->where('name', 'like', "%{$searchQuery}%")
                ->orWhere('email', 'like', "%{$searchQuery}%")
                ->limit(10)
                ->get(['id', 'name', 'email']);
                // ->get(['id', 'name', 'email', 'role']);

            return response()->json($users);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred during search',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show(User $user)
    {
        return Inertia::render('MyMARZ/Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ]
        ]);
    }

}
