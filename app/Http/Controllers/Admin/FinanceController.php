<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionSetting;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function gateways()
    {
        return view('backend.finance.gateways');
    }

    public function commissions()
    {
        $setting = CommissionSetting::current();

        return view('backend.finance.commissions', compact('setting'));
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
        $request->validate([
            'type' => 'required|in:fixed,percentage',
            'amount' => 'required|numeric|min:0',
            'low_balance_threshold' => 'required|numeric|min:0',
            'min_recharge' => 'required|numeric|min:1',
            'max_recharge' => 'required|numeric|gt:min_recharge',
        ]);

        if ($request->type === 'percentage' && (float) $request->amount > 100) {
            return back()->with('error', 'Percentage commission cannot be more than 100.')->withInput();
        }

        $setting = CommissionSetting::current() ?? new CommissionSetting();
        $setting->fill([
            'type' => $request->type,
            'amount' => $request->amount,
            'is_active' => $request->boolean('is_active'),
            'low_balance_threshold' => $request->low_balance_threshold,
            'min_recharge' => $request->min_recharge,
            'max_recharge' => $request->max_recharge,
            'effective_from' => now(),
        ]);
        $setting->save();

        return back()->with('success', 'Commission settings saved. Only this configuration is used for completed rides.');
    }
}
