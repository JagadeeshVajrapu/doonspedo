<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\CommissionSetting;
use App\Models\PaymentQrCode;
use App\Models\Transaction;
use App\Models\WalletRecharge;
use App\Services\WalletService;
use Illuminate\Http\Request;

class DriverFinanceController extends Controller
{
    private function getDriver()
    {
        return \App\Models\DriverRegistration::find(session('driver_id'));
    }

    public function earnings()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $bookings = \App\Models\Booking::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->latest()
            ->get();

        $commissionRate = $driver->commission_rate ?? config('taxi.default_commission_rate', 15);

        $stats = [
            'today' => [
                'gross' => $bookings->where('completed_at', '>=', now()->startOfDay())->sum('fare'),
                'net'   => $bookings->where('completed_at', '>=', now()->startOfDay())->sum(fn($b) => $b->net_amount > 0 ? $b->net_amount : ($b->fare - (($commissionRate/100) * $b->fare))),
            ],
            'week' => [
                'gross' => $bookings->where('completed_at', '>=', now()->startOfWeek())->sum('fare'),
                'net'   => $bookings->where('completed_at', '>=', now()->startOfWeek())->sum(fn($b) => $b->net_amount > 0 ? $b->net_amount : ($b->fare - (($commissionRate/100) * $b->fare))),
            ],
            'month' => [
                'gross' => $bookings->where('completed_at', '>=', now()->startOfMonth())->sum('fare'),
                'net'   => $bookings->where('completed_at', '>=', now()->startOfMonth())->sum(fn($b) => $b->net_amount > 0 ? $b->net_amount : ($b->fare - (($commissionRate/100) * $b->fare))),
            ],
            'total' => [
                'gross' => $bookings->sum('fare'),
                'net'   => $bookings->sum(fn($b) => $b->net_amount > 0 ? $b->net_amount : ($b->fare - (($commissionRate/100) * $b->fare))),
            ],
        ];

        return view('frontend.driver.finance.earnings', compact('driver', 'bookings', 'stats', 'commissionRate'));
    }

    public function wallet()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        $driver = $this->getDriver();
        $transactions = Transaction::where('driver_id', $driver->id)
            ->latest()
            ->limit(8)
            ->get();

        $withdrawals = \App\Models\WithdrawalRequest::where('driver_id', $driver->id)
            ->latest()
            ->get();

        $walletService = app(WalletService::class);
        $lowBalance = $walletService->isLowBalance($driver);
        $commission = CommissionSetting::current();
        $pendingRecharge = WalletRecharge::where('driver_id', $driver->id)->where('status', 'pending')->latest()->first();

        return view('frontend.driver.finance.wallet', compact('driver', 'transactions', 'withdrawals', 'lowBalance', 'commission', 'pendingRecharge'));
    }

    public function history(Request $request)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = $this->getDriver();
        $filter = $request->query('filter', 'all');
        $query = Transaction::where('driver_id', $driver->id);

        if ($filter === 'credits') {
            $query->where('type', 'credit');
        } elseif ($filter === 'debits') {
            $query->where('type', 'debit');
        } elseif ($filter === 'recharge') {
            $query->where('category', 'recharge');
        } elseif ($filter === 'commission') {
            $query->where('category', 'ride_commission');
        } elseif ($filter === 'failed') {
            $query->where('status', 'failed');
        } elseif ($filter === 'pending') {
            $query->where('status', 'pending');
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        return view('frontend.driver.finance.history', compact('driver', 'transactions', 'filter'));
    }

    public function addMoneyForm()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = $this->getDriver();
        $limits = CommissionSetting::current();

        return view('frontend.driver.finance.add-money', compact('driver', 'limits'));
    }

    public function startRecharge(Request $request)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = $this->getDriver();
        $limits = CommissionSetting::current();
        $min = $limits ? (float) $limits->min_recharge : 10;
        $max = $limits ? (float) $limits->max_recharge : 50000;

        $request->validate([
            'amount' => 'required|numeric|min:'.$min.'|max:'.$max,
        ]);

        $qr = PaymentQrCode::active();
        if (!$qr) {
            return back()->with('error', 'Payment QR is not available right now. Please try again later.')->withInput();
        }

        $recharge = WalletRecharge::create([
            'driver_id' => $driver->id,
            'amount' => number_format((float) $request->amount, 2, '.', ''),
            'payment_method' => 'upi_qr',
            'payment_qr_code_id' => $qr->id,
            'status' => 'pending',
        ]);

        return redirect()->route('driver.wallet.pay', $recharge->id);
    }

    public function showPayment($id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = $this->getDriver();
        $recharge = WalletRecharge::with('qrCode')
            ->where('driver_id', $driver->id)
            ->findOrFail($id);

        return view('frontend.driver.finance.pay', compact('driver', 'recharge'));
    }

    public function submitPayment(Request $request, $id)
    {
        if (!session('driver_id')) return redirect()->route('driver.login');

        $driver = $this->getDriver();
        $recharge = WalletRecharge::where('driver_id', $driver->id)->findOrFail($id);

        if ($recharge->status !== 'pending') {
            return redirect()->route('driver.wallet')->with('error', 'This recharge is already '.$recharge->status.'.');
        }

        $request->validate([
            'payment_reference' => 'required|string|max:80',
        ]);

        $reference = trim($request->payment_reference);
        $recharge->update([
            'payment_reference' => $reference,
            'submitted_at' => now(),
            'status' => 'pending',
        ]);

        $amountLabel = '₹'.number_format((float) $recharge->amount, 2);
        $message = $driver->name.' paid '.$amountLabel.' and submitted UTR '.$reference.'. Approve it to credit the wallet.';
        $notice = AdminNotification::query()
            ->where('reference_type', 'wallet_recharge')
            ->where('reference_id', $recharge->id)
            ->where('is_read', false)
            ->first();

        if ($notice) {
            $notice->update(['message' => $message]);
        } else {
            AdminNotification::create([
                'title' => 'Wallet payment to approve',
                'message' => $message,
                'type' => 'wallet_payment',
                'action_url' => route('admin.finance.recharges.show', $recharge->id),
                'reference_type' => 'wallet_recharge',
                'reference_id' => $recharge->id,
                'is_read' => false,
            ]);
        }

        return redirect()->route('driver.wallet')->with('success', 'Payment submitted successfully. Admin verification is pending.');
    }

    public function requestWithdrawal(Request $request)
    {
        if (!session('driver_id')) return response()->json(['success' => false], 401);

        $request->validate([
            'amount' => 'required|numeric|min:100',
            'payment_method' => 'required|string',
            'payment_details' => 'required|string',
        ]);

        $driver = $this->getDriver();

        if ($driver->wallet_balance < $request->amount) {
            return response()->json(['success' => false, 'message' => 'Insufficient wallet balance.'], 403);
        }

        \App\Models\WithdrawalRequest::create([
            'driver_id' => $driver->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_details' => $request->payment_details,
            'status' => 'pending',
        ]);

        // We don't deduct balance yet, only when processed/approved in some systems, 
        // but often it's "blocked". For simplicity, we'll deduct when admin completes it.
        // Or deduct now and refund if rejected. Let's deduct now.
        $driver->decrement('wallet_balance', $request->amount);
        
        \App\Models\Transaction::create([
            'driver_id' => $driver->id,
            'type' => 'debit',
            'amount' => $request->amount,
            'description' => 'Withdrawal Request',
            'status' => 'pending',
        ]);

        return response()->json(['success' => true, 'message' => 'Withdrawal request submitted successfully!']);
    }

    public function subscriptions()
    {
        if (!session('driver_id')) return redirect()->route('driver.login');
        
        if (!config('taxi.subscriptions_enabled', true)) {
            return redirect()->route('driver.dashboard')->with('error', 'Subscriptions are currently disabled.');
        }

        $driver = $this->getDriver()->load('activeSubscription.plan');
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)->get();
        
        return view('frontend.driver.finance.subscriptions', compact('driver', 'plans'));
    }

    public function purchaseSubscription(Request $request)
    {
        if (!session('driver_id')) return response()->json(['success' => false], 401);

        if (!config('taxi.subscriptions_enabled', true)) {
            return response()->json(['success' => false, 'message' => 'Subscriptions are currently disabled.'], 403);
        }

        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $driver = $this->getDriver();
        $plan = \App\Models\SubscriptionPlan::findOrFail($request->plan_id);

        if ($driver->wallet_balance < $plan->price) {
            return response()->json(['success' => false, 'message' => 'Insufficient wallet balance to purchase this plan.'], 403);
        }

        // Cancel old active subscriptions if any
        \App\Models\DriverSubscription::where('driver_id', $driver->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Create new subscription
        $startsAt = now();
        $expiresAt = now()->addDays($plan->duration_days);

        \App\Models\DriverSubscription::create([
            'driver_id' => $driver->id,
            'subscription_plan_id' => $plan->id,
            'price' => $plan->price,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'status' => 'active',
        ]);

        // Deduct from wallet
        $driver->decrement('wallet_balance', $plan->price);

        // Record Transaction
        \App\Models\Transaction::create([
            'driver_id' => $driver->id,
            'type' => 'debit',
            'amount' => $plan->price,
            'description' => 'Subscription Purchase: ' . $plan->name,
            'status' => 'success',
        ]);

        return response()->json(['success' => true, 'message' => 'Subscription purchased successfully!']);
    }

    public function purchaseSubscriptionManual(Request $request)
    {
        if (!session('driver_id')) return response()->json(['success' => false], 401);

        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $driver = $this->getDriver();
        $plan = \App\Models\SubscriptionPlan::findOrFail($request->plan_id);

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $filename = 'proof_' . time() . '_' . $driver->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/proofs'), $filename);
            $proofPath = 'uploads/proofs/' . $filename;
        }

        // Create new pending subscription
        \App\Models\DriverSubscription::create([
            'driver_id' => $driver->id,
            'subscription_plan_id' => $plan->id,
            'price' => $plan->price,
            'starts_at' => null, // Will be set by admin on approval
            'expires_at' => null, // Will be set by admin on approval
            'status' => 'pending',
            'payment_proof' => $proofPath,
        ]);

        return response()->json(['success' => true, 'message' => 'Payment proof submitted! Please wait for admin approval.']);
    }

    public function purchaseSubscriptionRazorpayInit(Request $request)
    {
        $plan = \App\Models\SubscriptionPlan::findOrFail($request->plan_id);
        // In a real app, you'd use Razorpay SDK to create an order here.
        // For now, we simulate a successful order ID.
        $order_id = 'order_' . rand(10000, 99999);
        
        return response()->json([
            'success' => true,
            'order_id' => $order_id,
            'amount' => $plan->price * 100 // amount in paisa
        ]);
    }

    public function purchaseSubscriptionRazorpayComplete(Request $request)
    {
        $driver = $this->getDriver();
        $plan = \App\Models\SubscriptionPlan::findOrFail($request->plan_id);

        // Verify signature here with Razorpay SDK
        
        // Finalize subscription
        \App\Models\DriverSubscription::create([
            'driver_id' => $driver->id,
            'subscription_plan_id' => $plan->id,
            'price' => $plan->price,
            'starts_at' => now(),
            'expires_at' => now()->addDays($plan->duration_days),
            'status' => 'active',
            'admin_note' => 'Paid via Razorpay. ID: ' . $request->razorpay_payment_id
        ]);

        return response()->json(['success' => true, 'message' => 'Subscription activated via Razorpay!']);
    }

    public function purchaseSubscriptionPayPal(Request $request)
    {
        // Placeholder for PayPal redirect flow
        return "PayPal integration coming soon or implement redirect logic here.";
    }
}
