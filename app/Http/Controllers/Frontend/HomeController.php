<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\SubscriptionPlan;

class HomeController extends Controller
{
    public function index()
    {
        $faqs = Faq::where('is_active', true)->latest()->get();
        $plans = SubscriptionPlan::where('is_active', true)->latest()->get();
        return view('frontend.index', compact('faqs', 'plans'));
    }
}
