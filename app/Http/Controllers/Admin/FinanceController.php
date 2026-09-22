<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function gateways()
    {
        return view('backend.finance.gateways');
    }

    public function commissions()
    {
        return view('backend.finance.commissions');
    }

    public function coupons()
    {
        $coupons = \App\Models\Coupon::latest()->paginate(15);
        return view('backend.finance.coupons', compact('coupons'));
    }

    public function transactions()
    {
        $transactions = \App\Models\Transaction::with(['user', 'driver'])->latest()->paginate(20);
        return view('backend.finance.transactions', compact('transactions'));
    }

    public function invoices()
    {
        $invoices = \App\Models\Invoice::with('booking')->latest()->paginate(20);
        return view('backend.finance.invoices', compact('invoices'));
    }

    public function updateCommissions(Request $request)
    {
        // Update logic (simulated for UI)
        return back()->with('success', 'Commission rates updated successfully.');
    }
}
