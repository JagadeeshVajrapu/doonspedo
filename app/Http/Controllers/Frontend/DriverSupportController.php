<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\DriverRegistration;
use Illuminate\Support\Str;

class DriverSupportController extends Controller
{
    private function getDriver()
    {
        return DriverRegistration::find(session('driver_id'));
    }

    public function index()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = $this->getDriver();
        $tickets = SupportTicket::where('driver_id', $driver->id)->latest()->get();

        return view('frontend.driver.support.index', compact('driver', 'tickets'));
    }

    public function create()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        $driver = $this->getDriver();
        return view('frontend.driver.support.create', compact('driver'));
    }

    public function store(Request $request)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'description' => 'required|string',
        ]);

        $driver = $this->getDriver();

        $ticket = SupportTicket::create([
            'driver_id' => $driver->id,
            'ticket_number' => 'TKT-' . strtoupper(Str::random(8)),
            'subject' => $request->subject,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        return redirect()->route('driver.support.show', $ticket->id)->with('success', 'Support ticket created successfully!');
    }

    public function show($id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = $this->getDriver();
        $ticket = SupportTicket::with('messages')->where('driver_id', $driver->id)->findOrFail($id);

        return view('frontend.driver.support.show', compact('driver', 'ticket'));
    }

    public function reply(Request $request, $id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:5120', // 5MB limit
        ]);

        $ticket = SupportTicket::findOrFail($id);
        $driver = $this->getDriver();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support/attachments', 'public');
        }

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => $driver->id,
            'sender_type' => 'driver',
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
        ]);

        // Reopen ticket if it was resolved/closed
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'Message sent successfully!');
    }
}
