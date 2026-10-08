<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverRegistration;
use App\Models\Booking;
use App\Models\Bid;
use App\Services\WalletService;
use App\Support\DriverRideRejections;
use App\Support\Geo;
use Illuminate\Support\Facades\DB;

class DriverBidController extends Controller
{
    private function getDriver()
    {
        return DriverRegistration::find(session('driver_id'));
    }

    public function index()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $bids = Bid::with('booking')
            ->where('driver_id', $driver->id)
            ->latest()
            ->get();
            
        return view('frontend.driver.bids.index', compact('driver', 'bids'));
    }

    public function store(Request $request)
    {
        if (!session('driver_id')) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'bid_amount' => 'required|numeric|min:1',
            'notes'      => 'nullable|string|max:200',
        ]);

        $driver = $this->getDriver();
        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'This ride is no longer open for bidding.'], 403);
        }

        if (!$driver || !$driver->canReceiveRides()) {
            $message = ($driver && $driver->status === 'approved')
                ? 'Your documents must be verified before you can accept rides.'
                : 'Your account must be approved before you can accept rides.';

            return response()->json(['success' => false, 'message' => $message], 403);
        }

        $tooFar = Geo::outOfRangeMessage($driver, $booking);
        if ($tooFar) {
            return response()->json(['success' => false, 'message' => $tooFar], 403);
        }

        if (!DriverRideRejections::matchesCategory($driver, $booking)) {
            return response()->json(['success' => false, 'message' => 'This ride does not match your active vehicle.'], 403);
        }

        $blocked = app(WalletService::class)->acceptanceBlock($driver, $booking);
        if ($blocked) {
            return response()->json(['success' => false, 'message' => $blocked, 'needs_wallet' => true], 403);
        }

        $bid = DB::transaction(function () use ($request, $driver, $booking) {
            $locked = Booking::whereKey($booking->id)->lockForUpdate()->first();
            if (!$locked || $locked->status !== 'pending' || $locked->driver_id !== null) {
                return null;
            }

            $existingBid = Bid::where('booking_id', $locked->id)
                ->where('driver_id', $driver->id)
                ->whereIn('status', ['pending', 'accepted'])
                ->lockForUpdate()
                ->first();

            if ($existingBid) {
                return false;
            }

            return Bid::create([
                'booking_id' => $locked->id,
                'driver_id'  => $driver->id,
                'bid_amount' => $request->bid_amount,
                'notes'      => $request->notes,
                'status'     => 'pending',
            ]);
        });

        if ($bid === false) {
            return response()->json(['success' => false, 'message' => 'You have already placed a bid on this ride.'], 403);
        }

        if (!$bid) {
            return response()->json(['success' => false, 'message' => 'This ride is no longer open for bidding.'], 403);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Bid placed successfully!',
            'bid' => $bid
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!session('driver_id')) return response()->json(['success' => false], 401);

        $request->validate([
            'bid_amount' => 'required|numeric|min:1',
            'notes'      => 'nullable|string|max:200',
        ]);

        $driver = $this->getDriver();
        $bid = Bid::where('driver_id', $driver->id)->findOrFail($id);

        if ($bid->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Cannot modify a bid that is not pending.'], 403);
        }

        $bid->update([
            'bid_amount' => $request->bid_amount,
            'notes'      => $request->notes,
        ]);

        return response()->json(['success' => true, 'message' => 'Bid updated successfully!']);
    }

    public function withdraw($id)
    {
        if (!session('driver_id')) return back()->with('error', 'Unauthorized');

        $driver = $this->getDriver();
        $bid = Bid::where('driver_id', $driver->id)->findOrFail($id);

        if ($bid->status !== 'pending') {
            return back()->with('error', 'Cannot withdraw a bid that is not pending.');
        }

        $bid->update(['status' => 'withdrawn']);

        return back()->with('success', 'Bid withdrawn successfully.');
    }
}
