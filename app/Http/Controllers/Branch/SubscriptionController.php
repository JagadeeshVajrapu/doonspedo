<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverSubscription;
use App\Models\DriverRegistration;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $branch = Auth::guard('branch')->user();
        
        $subscriptions = DriverSubscription::with(['driver', 'plan'])
            ->whereHas('driver', function($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->latest()
            ->paginate(15);
            
        $drivers = DriverRegistration::where('branch_id', $branch->id)->where('status', 'active')->get();
        $plans = SubscriptionPlan::where('is_active', 1)->get();
        $branchPlans = \App\Models\BranchPlan::where('branch_id', $branch->id)->latest()->get();
            
        return view('branch.subscriptions.index', compact('subscriptions', 'drivers', 'plans', 'branchPlans'));
    }

    public function plans()
    {
        $branch = Auth::guard('branch')->user();
        $branchPlans = \App\Models\BranchPlan::where('branch_id', $branch->id)->latest()->get();
        return view('branch.subscriptions.plans', compact('branchPlans'));
    }

    public function requests()
    {
        $branch = Auth::guard('branch')->user();
        
        $requests = DriverSubscription::with(['driver', 'plan'])
            ->whereHas('driver', function($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);
            
        $drivers = DriverRegistration::where('branch_id', $branch->id)->where('status', 'active')->get();
        $plans = SubscriptionPlan::where('is_active', 1)->get();
        $branchPlans = \App\Models\BranchPlan::where('branch_id', $branch->id)->latest()->get();
            
        return view('branch.subscriptions.requests', compact('requests', 'drivers', 'plans', 'branchPlans'));
    }

    public function approve($id)
    {
        $branch = Auth::guard('branch')->user();
        $subscription = DriverSubscription::whereHas('driver', function($query) use ($branch) {
            $query->where('branch_id', $branch->id);
        })->findOrFail($id);

        $plan = $subscription->plan;

        $subscription->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($plan->duration_days),
        ]);

        return back()->with('success', 'Subscription request approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $branch = Auth::guard('branch')->user();
        $subscription = DriverSubscription::whereHas('driver', function($query) use ($branch) {
            $query->where('branch_id', $branch->id);
        })->findOrFail($id);

        $subscription->update([
            'status' => 'cancelled',
            'admin_note' => $request->admin_note
        ]);

        return back()->with('success', 'Subscription request rejected.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|exists:driver_registrations,id',
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $branch = Auth::guard('branch')->user();
        $driver = DriverRegistration::where('branch_id', $branch->id)->findOrFail($request->driver_id);
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        DriverSubscription::create([
            'driver_id' => $driver->id,
            'plan_id' => $plan->id,
            'price' => $plan->price,
            'payment_method' => 'manual',
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($plan->duration_days),
            'transaction_id' => 'BRANCH-' . strtoupper(uniqid()),
        ]);

        return back()->with('success', 'Subscription added successfully for the driver.');
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_rides' => 'nullable|integer|min:1',
            'max_ride_amount' => 'nullable|numeric|min:0',
        ]);

        $branch = Auth::guard('branch')->user();

        \App\Models\BranchPlan::create([
            'branch_id' => $branch->id,
            'name' => $validated['name'],
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'],
            'max_rides' => $validated['max_rides'] ?? null,
            'max_ride_amount' => $validated['max_ride_amount'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Subscription plan created successfully.');
    }

    public function destroyPlan($id)
    {
        $branch = Auth::guard('branch')->user();
        $plan = \App\Models\BranchPlan::where('branch_id', $branch->id)->findOrFail($id);
        $plan->delete();
        
        return back()->with('success', 'Subscription plan deleted successfully.');
    }
}

