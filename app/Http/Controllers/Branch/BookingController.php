<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $branch = Auth::guard('branch')->user();
        $bookings = Booking::where('branch_id', $branch->id)->latest()->paginate(15);
        return view('branch.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $branch = Auth::guard('branch')->user();
        $booking = Booking::where('branch_id', $branch->id)->findOrFail($id);
        return view('branch.bookings.show', compact('booking'));
    }
}
