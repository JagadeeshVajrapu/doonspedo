<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverRegistration;
use App\Models\Booking;
use App\Models\Transaction;
use App\Services\WalletService;
use App\Support\Geo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DriverRideController extends Controller
{
    private function getDriver()
    {
        return DriverRegistration::find(session('driver_id'));
    }

    public function index()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $activeRide = Booking::with('user', 'vehicleCategory')
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'ongoing'])
            ->first();
            
        return view('frontend.driver.rides.index', compact('driver', 'activeRide'));
    }

    public function getNewRequests()
    {
        if (!session('driver_id')) return response()->json(['success' => false], 401);
        
        $driver = $this->getDriver();
        if (!$driver || !$driver->is_online || $driver->is_blocked) {
            return response()->json(['success' => true, 'requests' => []]);
        }

        if ($driver->current_lat === null || $driver->current_lng === null) {
            return response()->json(['success' => true, 'requests' => [], 'needs_location' => true]);
        }

        $radius = Geo::nearbyRadiusKm($driver);
        $requests = Booking::with('user', 'vehicleCategory')
            ->where('status', 'pending')
            ->whereNull('driver_id')
            ->whereNotNull('pickup_lat')
            ->whereNotNull('pickup_lng')
            ->latest()
            ->limit(50)
            ->get()
            ->filter(function (Booking $booking) use ($driver, $radius) {
                $km = Geo::kilometers(
                    (float) $driver->current_lat,
                    (float) $driver->current_lng,
                    (float) $booking->pickup_lat,
                    (float) $booking->pickup_lng
                );
                $booking->setAttribute('distance_km', round($km, 1));

                return $km <= $radius;
            })
            ->sortBy('distance_km')
            ->take(20)
            ->values();

        return response()->json(['success' => true, 'requests' => $requests]);
    }

    public function acceptRide($id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending' || $booking->driver_id !== null) {
            return back()->with('error', 'Ride request is no longer available.');
        }

        // Check if driver is already on a ride
        $hasActive = Booking::where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'ongoing'])
            ->exists();
            
        if ($hasActive) {
            return back()->with('error', 'You already have an active ride.');
        }

        $tooFar = Geo::outOfRangeMessage($driver, $booking);
        if ($tooFar) {
            return back()->with('error', $tooFar);
        }

        $blocked = app(WalletService::class)->acceptanceBlock($driver, $booking);
        if ($blocked) {
            return back()->with('error', $blocked)->with('needs_wallet', true);
        }

        $booking->update([
            'driver_id' => $driver->id,
            'status' => 'accepted',
            'accepted_at' => now(),
            'ride_otp' => $booking->ride_otp ?: Booking::generateRideOtp(),
        ]);

        return redirect()->route('driver.rides.details', $booking->id)->with('success', 'Ride accepted! Navigate to pickup.');
    }

    public function markArrived($id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = $this->getDriver();
        $booking = Booking::where('driver_id', $driver->id)->findOrFail($id);

        if ($booking->status !== 'accepted') {
            return back()->with('error', 'You can mark arrival only while you are on the way to the rider.');
        }

        if (!$booking->arrived_at) {
            $booking->update(['arrived_at' => now()]);
        }

        return back()->with('success', 'You have reached the rider. The ride OTP is now on this screen. Confirm it to start the trip.');
    }

    public function rejectRide($id)
    {
        // For now, just a dummy reject. In real app, this might track rejection rates.
        return back()->with('success', 'Request rejected.');
    }

    public function showRideDetails($id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $ride = Booking::with('user', 'vehicleCategory')->where('driver_id', $driver->id)->findOrFail($id);
        
        return view('frontend.driver.rides.details', compact('driver', 'ride'));
    }

    public function pickupPassenger(Request $request, $id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $driver = $this->getDriver();
        if (!$driver || $driver->is_blocked) {
            return back()->with('error', 'Your driver account cannot start rides.');
        }

        $started = false;
        $error = null;

        DB::transaction(function () use ($request, $id, $driver, &$started, &$error) {
            $ride = Booking::where('driver_id', $driver->id)->lockForUpdate()->find($id);
            if (!$ride) {
                $error = 'This ride is not assigned to you.';
                return;
            }

            if ($ride->status !== 'accepted' || $ride->ride_otp_verified_at) {
                $error = 'This ride cannot be started from its current status.';
                return;
            }

            if (!$ride->arrived_at) {
                $error = 'Reach the rider before starting the trip.';
                return;
            }

            if (!$ride->ride_otp || !hash_equals((string) $ride->ride_otp, (string) $request->otp)) {
                $error = 'Incorrect OTP. Please ask the customer for the correct ride OTP.';
                return;
            }

            $ride->update([
                'status' => 'ongoing',
                'picked_up_at' => now(),
                'ride_otp_verified_at' => now(),
            ]);
            $started = true;
        });

        if (!$started) {
            return back()->with('error', $error ?: 'Unable to start this ride.')->withInput();
        }

        return back()->with('success', 'OTP verified. Trip started. Drive safely to the destination.');
    }

    public function completeRide($id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $ride = Booking::where('driver_id', $driver->id)->findOrFail($id);

        if ($ride->status !== 'ongoing') {
            return back()->with('error', 'Only an ongoing ride can be completed.');
        }

        $service = app(WalletService::class);
        $summary = null;
        $error = null;

        DB::transaction(function () use ($id, $driver, $service, &$summary, &$error) {
            $ride = Booking::where('driver_id', $driver->id)->lockForUpdate()->find($id);
            if (!$ride || $ride->status !== 'ongoing') {
                $error = 'Only an ongoing ride can be completed.';
                return;
            }

            if (!$ride->fare) {
                $distance = (float) $ride->distance ?: 5.0;
                $ride->fare = 50 + ($distance * 15);
            }

            $prepaid = $service->activeCommission();
            if ($prepaid) {
                $commission = (float) $service->requiredCommission($ride);
            } else {
                $commissionRate = $driver->commission_rate ?: config('taxi.default_commission_rate', 15);
                $commission = ($commissionRate / 100) * (float) $ride->fare;
            }
            $netEarning = (float) $ride->fare - $commission;

            $ride->update([
                'status' => 'completed',
                'completed_at' => now(),
                'fare' => $ride->fare,
                'commission_amount' => $commission,
                'net_amount' => $netEarning,
                'payment_status' => 'completed',
            ]);

            if ($ride->payment_method === 'wallet') {
                $user = $ride->user()->lockForUpdate()->first();
                if ($user && $user->wallet_balance >= $ride->fare) {
                    $user->decrement('wallet_balance', $ride->fare);
                    Transaction::create([
                        'user_id' => $user->id,
                        'booking_id' => $ride->id,
                        'type' => 'debit',
                        'amount' => $ride->fare,
                        'description' => 'Payment for Ride (#'.$ride->id.')',
                        'status' => 'success',
                    ]);
                }
            }

            $commissionNote = '';
            if ($prepaid) {
                $debit = $service->debitRideCommissionLocked($driver->id, $ride->id);
                $commissionNote = $debit['ok']
                    ? ' Commission deducted: ₹'.number_format((float) ($debit['amount'] ?? $commission), 2).'.'
                    : ' '.$debit['message'];
            } else {
                $lockedDriver = DriverRegistration::query()->whereKey($driver->id)->lockForUpdate()->first();
                $lockedDriver->increment('wallet_balance', $netEarning);
                Transaction::create([
                    'driver_id' => $lockedDriver->id,
                    'booking_id' => $ride->id,
                    'type' => 'credit',
                    'amount' => $netEarning,
                    'description' => 'Ride Earning (#'.$ride->id.')',
                    'status' => 'success',
                ]);
            }

            $summary = 'Ride completed! Fare: ₹'.number_format((float) $ride->fare, 2).$commissionNote;
        });

        if ($error) {
            return back()->with('error', $error);
        }

        return redirect()->route('driver.dashboard')->with('success', $summary);
    }
}
