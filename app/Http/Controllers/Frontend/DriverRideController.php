<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverRegistration;
use App\Models\Booking;
use App\Models\Transaction;
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

        // Pending open bookings (existing marketplace behavior). Proximity filtering can be added later.
        $requests = Booking::with('user', 'vehicleCategory')
            ->where('status', 'pending')
            ->whereNull('driver_id')
            ->latest()
            ->limit(20)
            ->get();

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

        $booking->update([
            'driver_id' => $driver->id,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return redirect()->route('driver.rides.details', $booking->id)->with('success', 'Ride accepted! Navigate to pickup.');
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

    public function pickupPassenger($id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = $this->getDriver();
        $ride = Booking::where('driver_id', $driver->id)->findOrFail($id);

        if ($ride->status !== 'accepted') {
            return back()->with('error', 'This ride cannot be started from its current status.');
        }

        $ride->update([
            'status' => 'ongoing',
            'picked_up_at' => now(),
        ]);

        return back()->with('success', 'Trip started! Drive safely to destination.');
    }

    public function completeRide($id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $ride = Booking::where('driver_id', $driver->id)->findOrFail($id);

        if ($ride->status !== 'ongoing') {
            return back()->with('error', 'Only an ongoing ride can be completed.');
        }
        
        // Simple fare calculation if not set
        if (!$ride->fare) {
            $distance = (float)$ride->distance ?: 5.0;
            $ride->fare = 50 + ($distance * 15); // Base 50 + 15 per km
        }

        // Calculate commission and net earning
        $commissionRate = $driver->commission_rate ?: config('taxi.default_commission_rate', 15);
        $commission = ($commissionRate / 100) * $ride->fare;
        $netEarning = $ride->fare - $commission;

        $ride->update([
            'status' => 'completed',
            'completed_at' => now(),
            'fare' => $ride->fare,
            'commission_amount' => $commission,
            'net_amount' => $netEarning,
            'payment_status' => 'completed',
        ]);

        // Logic for Rider Wallet Deduction
        if ($ride->payment_method === 'wallet') {
            $user = $ride->user;
            if ($user->wallet_balance >= $ride->fare) {
                $user->decrement('wallet_balance', $ride->fare);
                
                // Record Rider Transaction
                Transaction::create([
                    'user_id' => $user->id,
                    'booking_id' => $ride->id,
                    'type' => 'debit',
                    'amount' => $ride->fare,
                    'description' => 'Payment for Ride (#' . $ride->id . ')',
                    'status' => 'success',
                ]);
            } else {
                // If wallet balance was somehow insufficient at the end
                // We mark it as successful anyway (for driver) but ideally 
                // we should handle this earlier.
            }
        }

        // Credit driver's wallet
        $driver->increment('wallet_balance', $netEarning);

        // Record Driver Transaction
        Transaction::create([
            'driver_id' => $driver->id,
            'booking_id' => $ride->id,
            'type' => 'credit',
            'amount' => $netEarning,
            'description' => 'Ride Earning (#' . $ride->id . ')',
            'status' => 'success',
        ]);

        return redirect()->route('driver.dashboard')->with('success', 'Ride completed! Fare: ₹' . number_format($ride->fare, 2) . ' (Net: ₹' . number_format($netEarning, 2) . ')');
    }
}
