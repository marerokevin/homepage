<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Mail\ContactNotification;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        // Save to database
        $contact = Contact::create($request->only(
            'name',
            'email',
            'subject',
            'message'
        ));

        // Send email notification
        Mail::to('kevinroy.marero@crestecphil.com.ph')
            ->send(new ContactNotification($contact));

        return back()->with('success', 'Message sent successfully!');
    }
}

