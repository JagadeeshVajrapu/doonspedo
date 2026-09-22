<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\Booking;
use App\Models\DriverRegistration;
use Illuminate\Support\Facades\Auth;

class RideChatController extends Controller
{
    private function getAuthType()
    {
        if (Auth::check()) return 'user';
        if (session()->has('driver_id')) return 'driver';
        return null;
    }

    public function index($bookingId)
    {
        $type = $this->getAuthType();
        if (!$type) return redirect()->route('login');

        $ride = null;
        if ($type == 'user') {
            $ride = Booking::with('driver')->where('user_id', Auth::id())->findOrFail($bookingId);
        } else {
            $ride = Booking::with('user')->where('driver_id', session('driver_id'))->findOrFail($bookingId);
        }
        
        $messages = ChatMessage::where('booking_id', $ride->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark incoming messages as read
        ChatMessage::where('booking_id', $ride->id)
            ->where('sender_type', '!=', $type)
            ->update(['is_read' => true]);

        if ($type == 'driver') {
            $driver = DriverRegistration::find(session('driver_id'));
            return view('frontend.driver.rides.chat', compact('ride', 'messages', 'driver'));
        }
        
        return view('frontend.rider.chat.index', compact('ride', 'messages'));
    }

    public function sendMessage(Request $request, $bookingId)
    {
        $type = $this->getAuthType();
        if (!$type) return response()->json(['success' => false], 401);

        $request->validate(['message' => 'required|string']);
        
        $ride = null;
        $senderId = null;
        if ($type == 'user') {
            $ride = Booking::where('user_id', Auth::id())->findOrFail($bookingId);
            $senderId = Auth::id();
        } else {
            $ride = Booking::where('driver_id', session('driver_id'))->findOrFail($bookingId);
            $senderId = session('driver_id');
        }

        $message = ChatMessage::create([
            'booking_id' => $ride->id,
            'sender_id' => $senderId,
            'sender_type' => $type,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true, 
            'message' => $message->message,
            'time' => $message->created_at->format('h:i A')
        ]);
    }

    public function getMessages($bookingId)
    {
        $type = $this->getAuthType();
        if (!$type) return response()->json(['success' => false], 401);

        $messages = ChatMessage::where('booking_id', $bookingId)
            ->where('is_read', false)
            ->where('sender_type', '!=', $type)
            ->get();

        foreach($messages as $msg) {
            $msg->update(['is_read' => true]);
        }

        return response()->json(['messages' => $messages]);
    }
}
