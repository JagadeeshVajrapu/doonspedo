<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\DriverRegistration;

class DriverRatingController extends Controller
{
    private function getDriver()
    {
        return DriverRegistration::find(session('driver_id'));
    }

    public function index()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = $this->getDriver();
        
        $reviews = Review::with('user', 'booking')
            ->where('driver_id', $driver->id)
            ->where('is_driver_review', false)
            ->latest()
            ->paginate(15);

        $stats = [
            'average_rating' => Review::where('driver_id', $driver->id)->where('is_driver_review', false)->avg('rating') ?: 0,
            'total_reviews' => Review::where('driver_id', $driver->id)->where('is_driver_review', false)->count(),
            'five_star' => Review::where('driver_id', $driver->id)->where('is_driver_review', false)->where('rating', 5)->count(),
            'four_star' => Review::where('driver_id', $driver->id)->where('is_driver_review', false)->where('rating', 4)->count(),
            'three_star' => Review::where('driver_id', $driver->id)->where('is_driver_review', false)->where('rating', 3)->count(),
            'two_star' => Review::where('driver_id', $driver->id)->where('is_driver_review', false)->where('rating', 2)->count(),
            'one_star' => Review::where('driver_id', $driver->id)->where('is_driver_review', false)->where('rating', 1)->count(),
        ];

        return view('frontend.driver.ratings.index', compact('driver', 'reviews', 'stats'));
    }
}
