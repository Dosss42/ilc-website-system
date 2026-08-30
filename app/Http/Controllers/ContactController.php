<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ], [
            'name.required'    => 'Please enter your full name.',
            'email.required'   => 'Please enter your email address.',
            'email.email'      => 'Please enter a valid email address.',
            'subject.required' => 'Please select a subject.',
            'message.required' => 'Please enter your message.',
        ]);

        ContactMessage::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'subject'    => $validated['subject'],
            'message'    => $validated['message'],
            'status'     => 'unread',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Your message has been sent successfully! We will get back to you within 1–3 business days.');
    }

    public function updateStatus(Request $request, ContactMessage $message)
    {
        $request->validate(['status' => 'required|in:unread,read,replied']);
        $message->update(['status' => $request->status]);
        return response()->json(['success' => true, 'status' => $message->status]);
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return response()->json(['success' => true]);
    }
}
