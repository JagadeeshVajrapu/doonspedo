<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BidController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Bid::with(['booking', 'driver']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $bids = $query->latest()->paginate(15);
        return view('backend.bids.index', compact('bids'));
    }

    public function show($id)
    {
        $bid = \App\Models\Bid::with(['booking', 'driver'])->findOrFail($id);
        return view('backend.bids.show', compact('bid'));
    }

    public function updateStatus(Request $request, $id)
    {
        $bid = \App\Models\Bid::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string|in:pending,accepted,rejected,withdrawn',
        ]);

        $bid->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Bid status updated successfully.');
    }

    public function settings()
    {
        $settingsFile = storage_path('app/settings.json');
        $settings = [];
        if (\Illuminate\Support\Facades\File::exists($settingsFile)) {
            $settings = json_decode(\Illuminate\Support\Facades\File::get($settingsFile), true);
        }
        return view('backend.bids.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $settingsFile = storage_path('app/settings.json');
        
        $settings = [];
        if (\Illuminate\Support\Facades\File::exists($settingsFile)) {
            $settings = json_decode(\Illuminate\Support\Facades\File::get($settingsFile), true);
        }

        if ($request->has('bidding_timeout')) {
            $settings['bidding_timeout'] = $request->input('bidding_timeout');
        }
        if ($request->has('bid_deviation')) {
            $settings['bid_deviation'] = $request->input('bid_deviation');
        }
        
        $settings['enable_driver_bidding'] = $request->has('enable_driver_bidding') ? '1' : '0';
        $settings['auto_approve_lowest_bid'] = $request->has('auto_approve_lowest_bid') ? '1' : '0';

        \Illuminate\Support\Facades\File::put($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->back()->with('success', 'Bid settings updated successfully.');
    }
}
