<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = \App\Models\SubscriptionPlan::latest()->get();
        return view('backend.subscriptions.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_rides' => 'nullable|integer|min:1',
            'max_ride_amount' => 'nullable|numeric|min:0',
            'features' => 'nullable|string'
        ]);

        $features = $request->features ? explode(',', $request->features) : [];
        $features = array_map('trim', $features);

        \App\Models\SubscriptionPlan::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'],
            'max_rides' => $validated['max_rides'] ?? null,
            'max_ride_amount' => $validated['max_ride_amount'] ?? null,
            'features' => $features,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Subscription plan created successfully.');
    }

    public function update(Request $request, $id)
    {
        $plan = \App\Models\SubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_rides' => 'nullable|integer|min:1',
            'max_ride_amount' => 'nullable|numeric|min:0',
            'features' => 'nullable|string'
        ]);

        $features = $request->features ? explode(',', $request->features) : [];
        $features = array_map('trim', $features);

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'],
            'max_rides' => $validated['max_rides'] ?? null,
            'max_ride_amount' => $validated['max_ride_amount'] ?? null,
            'features' => $features,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Subscription plan updated successfully.');
    }

    public function requests()
    {
        $requests = \App\Models\DriverSubscription::with(['driver', 'plan'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);
        return view('backend.subscriptions.requests', compact('requests'));
    }

    public function approveRequest($id)
    {
        $request = \App\Models\DriverSubscription::findOrFail($id);
        $plan = $request->plan;

        $request->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($plan->duration_days),
        ]);

        return back()->with('success', 'Subscription request approved successfully.');
    }

    public function rejectRequest(Request $request, $id)
    {
        $subRequest = \App\Models\DriverSubscription::findOrFail($id);
        $subRequest->update([
            'status' => 'cancelled',
            'admin_note' => $request->admin_note
        ]);

        return back()->with('success', 'Subscription request rejected.');
    }

    public function destroy($id)
    {
        $plan = \App\Models\SubscriptionPlan::findOrFail($id);
        $plan->delete();

        return back()->with('success', 'Subscription plan deleted successfully.');
    }
}

