<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Booking::with(['user', 'driver', 'vehicleCategory']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('service_type') && $request->service_type !== '') {
            $query->where('service_type', $request->service_type);
        }

        $bookings = $query->latest()->paginate(15);
        return view('backend.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = \App\Models\Booking::with(['user', 'driver', 'vehicleCategory'])->findOrFail($id);
        return view('backend.bookings.show', compact('booking'));
    }

    public function edit($id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        return view('backend.bookings.edit', compact('booking'));
    }

    public function update(Request $request, $id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string|in:pending,accepted,ongoing,completed,cancelled',
        ]);

        $booking->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking updated successfully.');
    }

    public function destroy($id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully.');
    }
}
