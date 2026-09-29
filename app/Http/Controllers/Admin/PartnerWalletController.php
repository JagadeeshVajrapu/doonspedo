<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverRegistration;
use App\Models\PaymentQrCode;
use App\Models\Transaction;
use App\Models\WalletRecharge;
use App\Services\CloudinaryStorage;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PartnerWalletController extends Controller
{
    public function qrIndex()
    {
        $codes = PaymentQrCode::query()->latest()->get();

        return view('backend.finance.qr-codes', compact('codes'));
    }

    public function qrStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:80',
            'upi_id' => 'nullable|string|max:100',
            'image' => 'required|file|mimes:jpeg,jpg,png,webp|max:2048|dimensions:max_width=4000,max_height=4000',
            'is_test' => 'nullable|boolean',
        ]);

        $file = $request->file('image');
        $mime = $file->getMimeType();
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return back()->with('error', 'Upload a JPEG, PNG, or WebP image.')->withInput();
        }

        try {
            $path = app(CloudinaryStorage::class)->storeUploadedFile($file, 'payment-qr');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        DB::transaction(function () use ($request, $path) {
            PaymentQrCode::query()->where('is_active', true)->update(['is_active' => false]);
            PaymentQrCode::create([
                'title' => $request->title,
                'upi_id' => $request->upi_id,
                'image_path' => $path,
                'is_active' => true,
                'is_test' => $request->boolean('is_test'),
                'created_by' => Auth::guard('admin')->id(),
            ]);
        });

        return back()->with('success', 'QR code saved and set as the active payment QR.');
    }

    public function qrActivate($id)
    {
        DB::transaction(function () use ($id) {
            $code = PaymentQrCode::query()->lockForUpdate()->findOrFail($id);
            PaymentQrCode::query()->where('id', '!=', $code->id)->update(['is_active' => false]);
            $code->update(['is_active' => true]);
        });

        return back()->with('success', 'This QR code is now the only active payment QR.');
    }

    public function qrDeactivate($id)
    {
        PaymentQrCode::query()->whereKey($id)->update(['is_active' => false]);

        return back()->with('success', 'QR code deactivated.');
    }

    public function qrDestroy($id)
    {
        $code = PaymentQrCode::query()->findOrFail($id);
        $used = WalletRecharge::query()->where('payment_qr_code_id', $code->id)->where('status', 'successful')->exists();
        if ($used) {
            $code->update(['is_active' => false]);

            return back()->with('error', 'This QR was used for a successful recharge, so it was deactivated instead of deleted.');
        }

        if (!preg_match('#^https?://#i', (string) $code->image_path)) {
            Storage::disk('public')->delete($code->image_path);
        }
        $code->delete();

        return back()->with('success', 'QR code deleted.');
    }

    public function recharges(Request $request)
    {
        $status = $request->query('status', 'all');
        $query = WalletRecharge::with(['driver', 'qrCode', 'verifier'])->latest();
        if (in_array($status, ['pending', 'successful', 'failed', 'rejected'], true)) {
            $query->where('status', $status);
        }
        $recharges = $query->paginate(20)->withQueryString();

        return view('backend.finance.recharges', compact('recharges', 'status'));
    }

    public function showRecharge($id)
    {
        $recharge = WalletRecharge::with(['driver', 'qrCode', 'verifier'])->findOrFail($id);

        return view('backend.finance.recharge-show', compact('recharge'));
    }

    public function approveRecharge($id, WalletService $wallets)
    {
        $result = $wallets->creditRecharge((int) $id, Auth::guard('admin')->id());

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function rejectRecharge(Request $request, $id, WalletService $wallets)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $result = $wallets->rejectRecharge((int) $id, (int) Auth::guard('admin')->id(), $request->rejection_reason);

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function transactions(Request $request)
    {
        $query = Transaction::with('driver')->whereNotNull('driver_id')->latest();

        if ($request->filled('driver')) {
            $term = $request->driver;
            $query->whereHas('driver', function ($driver) use ($term) {
                $driver->where('name', 'like', '%'.$term.'%')->orWhere('mobile', 'like', '%'.$term.'%');
            });
        }
        if ($request->filled('type') && in_array($request->type, ['recharge', 'ride_commission', 'ride_commission_failed'], true)) {
            $query->where('category', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('transaction_id')) {
            $query->where('id', $request->transaction_id);
        }
        if ($request->filled('ride_id')) {
            $query->where('booking_id', $request->ride_id);
        }
        if ($request->filled('recharge_id')) {
            $query->where('wallet_recharge_id', $request->recharge_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->paginate(25)->withQueryString();

        return view('backend.finance.partner-transactions', compact('transactions'));
    }

    public function partner($id)
    {
        $driver = DriverRegistration::query()->findOrFail($id);
        $transactions = Transaction::query()->where('driver_id', $driver->id)->latest()->paginate(20);
        $totalRecharge = WalletRecharge::query()->where('driver_id', $driver->id)->where('status', 'successful')->sum('amount');
        $totalCommission = Transaction::query()->where('driver_id', $driver->id)->where('category', 'ride_commission')->where('status', 'success')->sum('amount');
        $completedRides = $driver->bookings()->where('status', 'completed')->count();

        return view('backend.finance.partner-wallet', compact('driver', 'transactions', 'totalRecharge', 'totalCommission', 'completedRides'));
    }
}
