<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RiderBookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pickup_location'     => 'required|string|max:500',
            'dropoff_location'    => 'required|string|max:500',
            'service_type'        => 'required|in:ride,rental,parcel,freight',
            'vehicle_category_id' => 'required|exists:vehicle_categories,id',
            'fare'                => 'required|numeric|min:0',
            'payment_method'      => 'required|string|in:cash,wallet,online',
            'notes'               => 'nullable|string|max:1000',
            'parcel_details'      => 'nullable|string|max:1000',
            'pickup_lat'          => 'required|numeric|between:-90,90',
            'pickup_lng'          => 'required|numeric|between:-180,180',
            'dropoff_lat'         => 'required|numeric|between:-90,90',
            'dropoff_lng'         => 'required|numeric|between:-180,180',
            'distance'            => 'nullable|numeric|min:0',
        ]);

        if ($request->payment_method === 'wallet') {
            $user = Auth::user();
            if ($user->wallet_balance < $request->fare) {
                return response()->json(['success' => false, 'message' => 'Insufficient wallet balance. Please add money to your wallet.'], 403);
            }
        }

        $booking = Booking::create([
            'user_id'             => Auth::id(),
            'service_type'        => $request->service_type,
            'vehicle_category_id' => $request->vehicle_category_id,
            'pickup_location'     => $request->pickup_location,
            'dropoff_location'    => $request->dropoff_location,
            'pickup_lat'          => $request->pickup_lat,
            'pickup_lng'          => $request->pickup_lng,
            'dropoff_lat'         => $request->dropoff_lat,
            'dropoff_lng'         => $request->dropoff_lng,
            'distance'            => $request->distance,
            'fare'                => $request->fare,
            'payment_method'      => $request->payment_method,
            'notes'               => $request->notes,
            'parcel_details'      => $request->parcel_details,
            'status'              => 'pending',
            'ride_otp'            => Booking::generateRideOtp(),
            'payment_status'      => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ride request created successfully! Waiting for driver bids.',
            'booking' => $booking
        ]);
    }

    public function showBids($bookingId)
    {
        $booking = Booking::with('driver')->where('user_id', Auth::id())->findOrFail($bookingId);
        if ($booking->driver_id && in_array($booking->status, ['accepted', 'ongoing'], true)) {
            $booking->makeVisible('ride_otp');
        }
        $bids = Bid::with('driver')->where('booking_id', $bookingId)->where('status', 'pending')->get();

        return response()->json([
            'success' => true,
            'bids'    => $bids,
            'booking' => $booking
        ]);
    }

    public function acceptBid($bidId)
    {
        $bid = Bid::with('booking')->findOrFail($bidId);
        $booking = $bid->booking;

        if ($booking->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'This booking is already ' . $booking->status], 403);
        }

        DB::transaction(function () use ($bid, $booking) {
            // Update the accepted bid
            $bid->update(['status' => 'accepted']);

            // Update the booking
            if (!$booking->ride_otp) {
                $booking->ride_otp = Booking::generateRideOtp();
            }

            $booking->update([
                'driver_id'   => $bid->driver_id,
                'fare'        => $bid->bid_amount,
                'status'      => 'accepted',
                'accepted_at' => now(),
                'ride_otp'    => $booking->ride_otp,
            ]);

            // Reject all other bids for this booking
            Bid::where('booking_id', $booking->id)
                ->where('id', '!=', $bid->id)
                ->update(['status' => 'rejected']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Bid accepted! Your driver is on the way.',
            'booking' => $booking->load('driver')->makeVisible('ride_otp')
        ]);
    }

    public function rejectBid($bidId)
    {
        $bid = Bid::with('booking')->findOrFail($bidId);
        
        if ($bid->booking->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $bid->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Bid rejected.'
        ]);
    }
    
    public function cancel(Request $request, $id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);
        
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return response()->json(['success' => false, 'message' => 'Cannot cancel a completed or already cancelled ride.'], 403);
        }

        $wasPending = $booking->status === 'pending';

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'notes' => trim(($booking->notes ?? '') . "\nCancellation Reason: " . ($request->reason ?? 'Cancelled by rider')),
        ]);

        // Reject open bids for cancelled pending requests
        if ($wasPending) {
            Bid::where('booking_id', $booking->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ride cancelled successfully.'
        ]);
    }

    public function show($id)
    {
        $booking = Booking::with(['driver', 'vehicleCategory'])->where('user_id', Auth::id())->findOrFail($id);
        return view('frontend.rider.bookings.show', compact('booking'));
    }

    public function downloadInvoice($id)
    {
        $booking = Booking::with(['driver', 'vehicleCategory', 'user'])->where('user_id', Auth::id())->findOrFail($id);
        // For now, we'll return a printable view, but this could be a PDF download using a library like Barryvdh\DomPDF
        return view('frontend.rider.bookings.invoice', compact('booking'));
    }

    public function rebook($id)
    {
        $oldBooking = Booking::where('user_id', Auth::id())->findOrFail($id);
        
        $newBooking = Booking::create([
            'user_id'             => Auth::id(),
            'service_type'        => $oldBooking->service_type,
            'vehicle_category_id' => $oldBooking->vehicle_category_id,
            'pickup_location'     => $oldBooking->pickup_location,
            'dropoff_location'    => $oldBooking->dropoff_location,
            'pickup_lat'          => $oldBooking->pickup_lat,
            'pickup_lng'          => $oldBooking->pickup_lng,
            'dropoff_lat'         => $oldBooking->dropoff_lat,
            'dropoff_lng'         => $oldBooking->dropoff_lng,
            'fare'                => $oldBooking->fare,
            'status'              => 'pending',
            'ride_otp'            => Booking::generateRideOtp(),
            'payment_status'      => 'pending',
        ]);

        return redirect()->route('rider.app', ['booking_id' => $newBooking->id])->with('success', 'Ride rebooked! Waiting for bids.');
    }
    
    public function myBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())->latest()->get();
        return view('frontend.rider.bookings.index', compact('bookings'));
    }

    public function getStatus($id)
    {
        $booking = Booking::with('driver')->where('user_id', Auth::id())->findOrFail($id);
        if ($booking->driver_id && in_array($booking->status, ['accepted', 'ongoing'], true)) {
            $booking->makeVisible('ride_otp');
        }

        return response()->json([
            'success' => true,
            'status'  => $booking->status,
            'booking' => $booking
        ]);
    }
}
