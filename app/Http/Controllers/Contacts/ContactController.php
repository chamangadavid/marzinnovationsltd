<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\Contacts\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{

    public function store(Request $request)
{
    $validated = $request->validate([
        'fullName' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    Contact::create($validated);

    return response()->json([
        'message' => 'Thank you for your message! We will get back to you shortly.'
    ], 201);
}

}
