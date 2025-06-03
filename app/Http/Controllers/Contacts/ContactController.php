<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\Contacts\Contact;
use App\Notifications\NewContactMessage;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{

    public function index()
    {
        return Inertia::render('MyMARZ/Contacts/Index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Contact::create($validated);

        // Send notification to static email
            Notification::route('mail', 'umoyoprintex@gmail.com')
            ->notify(new NewContactMessage($validated));

        return response()->json([
            'message' => 'Thank you for your message! We will get back to you shortly.'
        ], 201);
    }

    public function getAllContacts(Request $request)
    {
        $query = Contact::query();
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        
        // Date range filter
        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->input('start_date'))->startOfDay(),
                Carbon::parse($request->input('end_date'))->endOfDay()
            ]);
        }
        
        // Month filter
        if ($request->has('month')) {
            $query->whereMonth('created_at', $request->input('month'));
        }

        $contacts = $query->select([
            'id',
            'fullName',
            'email',
            'subject',
            'message',
            'created_at'
        ])->get();

        return response()->json($contacts);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(['message' => 'Contact deleted successfully']);
    }

  

}
