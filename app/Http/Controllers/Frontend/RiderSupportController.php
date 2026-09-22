<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Support\Facades\Auth;

class RiderSupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->latest()
            ->get();
            
        return view('frontend.rider.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('frontend.rider.support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => 'open',
            'ticket_id' => 'TKT' . strtoupper(uniqid()),
        ]);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'sender_type' => 'user',
            'message' => $request->message,
        ]);

        return redirect()->route('rider.support.index')->with('success', 'Support ticket created successfully!');
    }

    public function show($id)
    {
        $ticket = SupportTicket::with('messages')->where('user_id', Auth::id())->findOrFail($id);
        return view('frontend.rider.support.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);
        
        $ticket = SupportTicket::where('user_id', Auth::id())->findOrFail($id);
        
        SupportMessage::create([
            'support_ticket_id' => $id,
            'sender_id' => Auth::id(),
            'sender_type' => 'user',
            'message' => $request->message,
        ]);
        
        return back()->with('success', 'Message sent!');
    }
}
