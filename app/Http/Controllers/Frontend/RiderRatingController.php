<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class RiderRatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:500',
        ]);

        $booking = Booking::where('user_id', Auth::id())->findOrFail($request->booking_id);

        if ($booking->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'You can only rate a completed ride.'], 403);
        }

        // Check if already reviewed
        $existingReview = Review::where('booking_id', $booking->id)
            ->where('user_id', Auth::id())
            ->where('is_driver_review', false)
            ->first();

        if ($existingReview) {
            return response()->json(['success' => false, 'message' => 'You have already reviewed this ride.'], 403);
        }

        $review = Review::create([
            'booking_id'       => $booking->id,
            'user_id'          => Auth::id(),
            'driver_id'        => $booking->driver_id,
            'rating'           => $request->rating,
            'comment'          => $request->comment,
            'is_driver_review' => false, // User reviewing driver
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!',
            'review'  => $review
        ]);
    }
}
