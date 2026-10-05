<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Bid;
use App\Models\VehicleCategory;
use App\Support\Geo;
use App\Support\RideFare;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
            'ac_preference'       => 'nullable|string|in:ac,non-ac',
            'coupon_code'         => 'nullable|string|max:40',
            'parcel_weight'       => 'nullable|numeric|min:0|max:1000',
            'is_rental'           => 'nullable|boolean',
            'offer_extra'         => 'nullable|numeric|min:0|max:100000',
        ]);

        $isRental = $request->boolean('is_rental') || $request->service_type === 'rental';
        $distance = round((float) ($request->distance ?? 0), 2);
        if (!$isRental && $distance <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Wait for the route to finish. A ride needs a distance greater than 0.',
            ], 422);
        }

        $category = VehicleCategory::findOrFail($request->vehicle_category_id);
        $preference = RideFare::normalizePreference($request->input('ac_preference'))
            ?? RideFare::preferenceFromNotes($request->notes);
        $fare = RideFare::calculate($category, $distance, [
            'ac_preference' => $preference,
            'service_type' => $request->service_type,
            'parcel_weight' => $request->input('parcel_weight'),
            'coupon_code' => $request->input('coupon_code'),
        ]);

        if ($fare <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Fare could not be calculated. Wait for the route and choose a priced vehicle.',
            ], 422);
        }

        $offerExtra = round(max(0, (float) $request->input('offer_extra', 0)), 2);
        $fare = round($fare + $offerExtra, 2);

        if ($request->payment_method === 'wallet') {
            $user = Auth::user();
            if ($user->wallet_balance < $fare) {
                return response()->json(['success' => false, 'message' => 'Insufficient wallet balance. Please add money to your wallet.'], 403);
            }
        }

        $identity = $this->bookingIdentity($request, $preference, $distance, $offerExtra);
        $lock = Cache::lock('rider-booking:'.Auth::id().':'.$identity, 10);

        try {
            $lock->block(5);

            $existing = $this->findRecentDuplicate($request, $preference, $fare);
            if ($existing) {
                return response()->json([
                    'success' => true,
                    'reused' => true,
                    'message' => 'Your ride request is already open.',
                    'booking' => $existing,
                ]);
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
                'distance'            => $distance,
                'fare'                => $fare,
                'payment_method'      => $request->payment_method,
                'notes'               => $request->notes,
                'parcel_details'      => $request->parcel_details,
                'status'              => 'pending',
                'ride_otp'            => Booking::generateRideOtp(),
                'payment_status'      => 'pending',
            ]);
        } finally {
            $lock->release();
        }

        return response()->json([
            'success' => true,
            'message' => 'Ride request created. Waiting for a driver to accept your offer.',
            'booking' => $booking
        ]);
    }

    private function bookingIdentity(Request $request, ?string $preference, float $distance, float $offerExtra): string
    {
        return sha1(implode('|', [
            $request->service_type,
            (int) $request->vehicle_category_id,
            $request->payment_method,
            $preference ?? '',
            number_format((float) $request->pickup_lat, 4, '.', ''),
            number_format((float) $request->pickup_lng, 4, '.', ''),
            number_format((float) $request->dropoff_lat, 4, '.', ''),
            number_format((float) $request->dropoff_lng, 4, '.', ''),
            number_format($distance, 2, '.', ''),
            number_format($offerExtra, 2, '.', ''),
        ]));
    }

    private function findRecentDuplicate(Request $request, ?string $preference, float $fare): ?Booking
    {
        $candidates = Booking::query()
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->whereNull('driver_id')
            ->where('service_type', $request->service_type)
            ->where('vehicle_category_id', $request->vehicle_category_id)
            ->where('payment_method', $request->payment_method)
            ->where('created_at', '>=', now()->subSeconds(90))
            ->latest('id')
            ->limit(5)
            ->get();

        foreach ($candidates as $booking) {
            if (!$this->samePoint($booking->pickup_lat, $booking->pickup_lng, $request->pickup_lat, $request->pickup_lng)) {
                continue;
            }
            if (!$this->samePoint($booking->dropoff_lat, $booking->dropoff_lng, $request->dropoff_lat, $request->dropoff_lng)) {
                continue;
            }
            $existingPreference = RideFare::preferenceFromNotes($booking->notes);
            if ($existingPreference !== $preference) {
                continue;
            }
            if (abs((float) $booking->fare - $fare) > 0.009) {
                continue;
            }

            return $booking;
        }

        return null;
    }

    private function samePoint($lat, $lng, $otherLat, $otherLng): bool
    {
        if ($lat === null || $lng === null || $otherLat === null || $otherLng === null) {
            return false;
        }

        return abs((float) $lat - (float) $otherLat) < 0.0002
            && abs((float) $lng - (float) $otherLng) < 0.0002;
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
        $etaMinutes = $this->arrivalMinutes($booking);

        return view('frontend.rider.bookings.show', compact('booking', 'etaMinutes'));
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
            'eta_minutes' => $this->arrivalMinutes($booking),
            'booking' => $booking
        ]);
    }

    private function arrivalMinutes(Booking $booking): ?int
    {
        $driver = $booking->driver;
        if (!$driver || $booking->status !== 'accepted' || $booking->arrived_at) {
            return null;
        }
        if ($driver->current_lat === null || $driver->current_lng === null || $booking->pickup_lat === null || $booking->pickup_lng === null) {
            return null;
        }

        return Geo::etaMinutes(Geo::kilometers(
            (float) $driver->current_lat,
            (float) $driver->current_lng,
            (float) $booking->pickup_lat,
            (float) $booking->pickup_lng
        ));
    }
}
