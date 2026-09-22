<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RiderWalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)
            ->latest()
            ->paginate(10);
            
        return view('frontend.rider.wallet.index', compact('user', 'transactions'));
    }

    public function addMoney(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|string'
        ]);

        // Mocking payment gateway integration (Stripe/Razorpay/etc)
        // In a real app, this would redirect to a gateway or open a popup
        
        $user = Auth::user();
        $amount = $request->amount;
        
        DB::transaction(function() use ($user, $amount, $request) {
            $user->increment('wallet_balance', $amount);
            
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amount,
                'description' => 'Added money to wallet via ' . ucfirst($request->payment_method),
                'reference_id' => 'TXN' . strtoupper(uniqid()),
                'status' => 'success'
            ]);
        });

        return back()->with('success', '₹' . number_format($amount, 2) . ' added to your wallet successfully!');
    }
}
