<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

Route::get('/', [\App\Http\Controllers\Frontend\HomeController::class, 'index']);

// Policy Routes
Route::get('/policy/privacy', function () { return view('policy.privacy'); })->name('policy.privacy');
Route::get('/policy/data-deletion', function () { return view('policy.data-deletion'); })->name('policy.data-deletion');
Route::get('/policy/refund', function () { return view('policy.refund'); })->name('policy.refund');
Route::get('/policy/about', function () { return view('policy.about'); })->name('policy.about');
Route::get('/policy/account-deletion', function () { return view('policy.account-deletion'); })->name('policy.account-deletion');
Route::get('/policy/community-guidelines', function () { return view('policy.community-guidelines'); })->name('policy.community-guidelines');
Route::get('/policy/driver-terms', function () { return view('policy.driver-terms'); })->name('policy.driver-terms');
Route::get('/policy/terms', function () { return view('policy.terms'); })->name('policy.terms');

use Illuminate\Support\Facades\DB;
use App\Models\VehicleCategory;

// Rider Auth Routes
Route::get('/login', [\App\Http\Controllers\Frontend\UserController::class, 'showLoginForm'])->name('login');
Route::get('/login/verify-otp', function () { 
    if (session()->has('rider_mobile')) return view('frontend.rider-verify-otp');
    return redirect()->route('login'); 
})->name('login.verifyOtpForm');
Route::post('/login/send-otp', [\App\Http\Controllers\Frontend\UserController::class, 'sendOtp'])->name('login.sendOtp');
Route::post('/login/verify-otp', [\App\Http\Controllers\Frontend\UserController::class, 'verifyOtp'])->name('login.verifyOtp');
Route::post('/login', [\App\Http\Controllers\Frontend\UserController::class, 'login'])->name('login.submit');
Route::get('/register', [\App\Http\Controllers\Frontend\UserController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\Frontend\UserController::class, 'register'])->name('register.submit');
Route::post('/logout', [\App\Http\Controllers\Frontend\UserController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {
    Route::get('/app', function () {
        $categories = DB::table('vehicle_categories')->where('is_active', 1)->get();
        $packages = DB::table('rental_packages')->where('is_active', 1)->get();
        return view('frontend.rider-app', compact('categories', 'packages'));
    })->name('rider.app');

    Route::get('/profile', [\App\Http\Controllers\Frontend\UserController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\Frontend\UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/kyc', [\App\Http\Controllers\Frontend\UserController::class, 'showKyc'])->name('rider.kyc');
    Route::post('/kyc', [\App\Http\Controllers\Frontend\UserController::class, 'storeKyc'])->name('rider.kyc.store');

    // Rider Booking & Bidding
    Route::group(['prefix' => 'rider/bookings', 'as' => 'rider.bookings.'], function () {
        Route::get('/', [\App\Http\Controllers\Frontend\RiderBookingController::class, 'myBookings'])->name('index');
        Route::post('/store', [\App\Http\Controllers\Frontend\RiderBookingController::class, 'store'])->name('store');
        Route::get('/{bookingId}/bids', [\App\Http\Controllers\Frontend\RiderBookingController::class, 'showBids'])->name('bids');
        Route::post('/bids/{bidId}/accept', [\App\Http\Controllers\Frontend\RiderBookingController::class, 'acceptBid'])->name('bids.accept');
        Route::post('/bids/{bidId}/reject', [\App\Http\Controllers\Frontend\RiderBookingController::class, 'rejectBid'])->name('bids.reject');
        
        // New Routes
        Route::get('/{id}/status', [\App\Http\Controllers\Frontend\RiderBookingController::class, 'getStatus'])->name('status');
        Route::get('/{id}/show', [\App\Http\Controllers\Frontend\RiderBookingController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [\App\Http\Controllers\Frontend\RiderBookingController::class, 'cancel'])->name('cancel');
        Route::get('/{id}/rebook', [\App\Http\Controllers\Frontend\RiderBookingController::class, 'rebook'])->name('rebook');
        Route::get('/{id}/invoice', [\App\Http\Controllers\Frontend\RiderBookingController::class, 'downloadInvoice'])->name('invoice');
    });

    // Wallet System
    Route::group(['prefix' => 'rider/wallet', 'as' => 'rider.wallet.'], function () {
        Route::get('/', [\App\Http\Controllers\Frontend\RiderWalletController::class, 'index'])->name('index');
        Route::post('/add-money', [\App\Http\Controllers\Frontend\RiderWalletController::class, 'addMoney'])->name('addMoney');
    });

    // Ratings & Reviews
    Route::post('/rider/ratings', [\App\Http\Controllers\Frontend\RiderRatingController::class, 'store'])->name('rider.ratings.store');

    // Support System
    Route::group(['prefix' => 'rider/support', 'as' => 'rider.support.'], function () {
        Route::get('/', [\App\Http\Controllers\Frontend\RiderSupportController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Frontend\RiderSupportController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Frontend\RiderSupportController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Frontend\RiderSupportController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [\App\Http\Controllers\Frontend\RiderSupportController::class, 'reply'])->name('reply');
    });

    // Shared Chat Route (Accessible by both after auth)
    Route::get('/chat/{bookingId}', [\App\Http\Controllers\Frontend\RideChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/{bookingId}/send', [\App\Http\Controllers\Frontend\RideChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/{bookingId}/messages', [\App\Http\Controllers\Frontend\RideChatController::class, 'getMessages'])->name('chat.messages');

    // Localization & Theme Switchers
    Route::group(['prefix' => 'localization', 'as' => 'localization.'], function () {
        Route::get('/locale/{locale}', [\App\Http\Controllers\Frontend\LocalizationController::class, 'setLocale'])->name('locale');
        Route::get('/currency/{code}', [\App\Http\Controllers\Frontend\LocalizationController::class, 'setCurrency'])->name('currency');
        Route::get('/theme/{theme}', [\App\Http\Controllers\Frontend\LocalizationController::class, 'setTheme'])->name('theme');
    });
});

// Social Auth Routes
Route::get('/auth/{provider}', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update');

// Admin authentication (public)
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin protected routes
Route::middleware('auth:admin')->group(function () {
Route::get('/admin', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/search', [\App\Http\Controllers\Admin\DashboardController::class, 'search'])->name('admin.search');

// Branch Management
Route::group(['prefix' => 'admin/branches', 'as' => 'admin.branches.'], function () {
    Route::get('/', [\App\Http\Controllers\Admin\BranchController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\BranchController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\BranchController::class, 'store'])->name('store');
    Route::get('/{branch}/edit', [\App\Http\Controllers\Admin\BranchController::class, 'edit'])->name('edit');
    Route::put('/{branch}', [\App\Http\Controllers\Admin\BranchController::class, 'update'])->name('update');
    Route::delete('/{branch}', [\App\Http\Controllers\Admin\BranchController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/toggle-status', [\App\Http\Controllers\Admin\BranchController::class, 'toggleStatus'])->name('toggle_status');
});

});

// Branch Portal Routes
Route::group(['prefix' => 'branch', 'as' => 'branch.'], function () {
    Route::get('/login', [\App\Http\Controllers\Branch\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Branch\AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [\App\Http\Controllers\Branch\AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Branch\AuthController::class, 'register'])->name('register.submit');
    Route::post('/logout', [\App\Http\Controllers\Branch\AuthController::class, 'logout'])->name('logout');

    Route::group(['middleware' => 'auth:branch'], function () {
        Route::get('/dashboard', [\App\Http\Controllers\Branch\DashboardController::class, 'index'])->name('dashboard');
        
        // Branch Drivers
        Route::get('/drivers', [\App\Http\Controllers\Branch\DriverController::class, 'index'])->name('drivers.index');
        Route::get('/drivers/create', [\App\Http\Controllers\Branch\DriverController::class, 'create'])->name('drivers.create');
        Route::post('/drivers', [\App\Http\Controllers\Branch\DriverController::class, 'store'])->name('drivers.store');
        Route::get('/drivers/{id}/view', [\App\Http\Controllers\Branch\DriverController::class, 'view'])->name('drivers.view');
        
        // Branch Bookings
        Route::get('/bookings', [\App\Http\Controllers\Branch\BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{id}/details', [\App\Http\Controllers\Branch\BookingController::class, 'show'])->name('bookings.show');

        // Branch Subscriptions
        Route::get('/subscriptions', [\App\Http\Controllers\Branch\SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/plans', [\App\Http\Controllers\Branch\SubscriptionController::class, 'plans'])->name('subscriptions.plans');
        Route::get('/subscriptions/requests', [\App\Http\Controllers\Branch\SubscriptionController::class, 'requests'])->name('subscriptions.requests');
        Route::post('/subscriptions/store', [\App\Http\Controllers\Branch\SubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::post('/subscriptions/plans/store', [\App\Http\Controllers\Branch\SubscriptionController::class, 'storePlan'])->name('subscriptions.plans.store');
        Route::delete('/subscriptions/plans/{id}', [\App\Http\Controllers\Branch\SubscriptionController::class, 'destroyPlan'])->name('subscriptions.plans.destroy');
        Route::post('/subscriptions/approve/{id}', [\App\Http\Controllers\Branch\SubscriptionController::class, 'approve'])->name('subscriptions.approve');
        Route::post('/subscriptions/reject/{id}', [\App\Http\Controllers\Branch\SubscriptionController::class, 'reject'])->name('subscriptions.reject');

        Route::get('/run-migrations-temp', function () {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            return 'Migrations executed: ' . \Illuminate\Support\Facades\Artisan::output();
        });

        // Branch Vehicles
        Route::get('/vehicles', [\App\Http\Controllers\Branch\VehicleController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/create', [\App\Http\Controllers\Branch\VehicleController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicles', [\App\Http\Controllers\Branch\VehicleController::class, 'store'])->name('vehicles.store');

        // Branch Vehicle Master Data
        Route::group(['prefix' => 'vehicles', 'as' => 'vehicles.'], function () {
            Route::get('/categories', [\App\Http\Controllers\Branch\VehicleController::class, 'categories'])->name('categories');
            Route::post('/categories', [\App\Http\Controllers\Branch\VehicleController::class, 'storeCategory'])->name('categories.store');
            Route::put('/categories/{id}', [\App\Http\Controllers\Branch\VehicleController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{id}', [\App\Http\Controllers\Branch\VehicleController::class, 'deleteCategory'])->name('categories.delete');
            
            Route::get('/rentals', [\App\Http\Controllers\Branch\VehicleController::class, 'rentals'])->name('rentals');
            
            Route::get('/pricing', [\App\Http\Controllers\Branch\VehicleController::class, 'pricing'])->name('pricing');
            Route::post('/pricing/update', [\App\Http\Controllers\Branch\VehicleController::class, 'updatePricing'])->name('pricing.update');
            
            Route::get('/parcel', [\App\Http\Controllers\Branch\VehicleController::class, 'freight'])->name('parcel');
            Route::post('/parcel', [\App\Http\Controllers\Branch\VehicleController::class, 'storeParcel'])->name('parcel.store');
            Route::put('/parcel/{id}', [\App\Http\Controllers\Branch\VehicleController::class, 'updateParcel'])->name('parcel.update');
            Route::delete('/parcel/{id}', [\App\Http\Controllers\Branch\VehicleController::class, 'deleteParcel'])->name('parcel.delete');
        });
    });
});

Route::middleware('auth:admin')->group(function () {
// Admin User Management
Route::group(['prefix' => 'admin/users', 'as' => 'admin.users.'], function () {
    Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
    Route::get('/{id}/view', [\App\Http\Controllers\Admin\UserController::class, 'viewDetails'])->name('view');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
    Route::get('/blocked', [\App\Http\Controllers\Admin\UserController::class, 'blocked'])->name('blocked');
    Route::post('/{id}/block', [\App\Http\Controllers\Admin\UserController::class, 'block'])->name('block');
    Route::get('/wallet', [\App\Http\Controllers\Admin\UserController::class, 'wallet'])->name('wallet');
});

Route::middleware('auth:admin')->prefix('admin/customer-kyc')->name('admin.customers.kyc.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\CustomerKycController::class, 'index'])->name('index');
    Route::get('/{submission}/document', [\App\Http\Controllers\Admin\CustomerKycController::class, 'download'])->name('download');
    Route::post('/{submission}/approve', [\App\Http\Controllers\Admin\CustomerKycController::class, 'approve'])->name('approve');
    Route::post('/{submission}/reject', [\App\Http\Controllers\Admin\CustomerKycController::class, 'reject'])->name('reject');
});

// Admin Driver Management
Route::group(['prefix' => 'admin/drivers', 'as' => 'admin.drivers.'], function () {
    Route::get('/', [\App\Http\Controllers\Admin\DriverController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\DriverController::class, 'create'])->name('create');
    Route::post('/store', [\App\Http\Controllers\Admin\DriverController::class, 'store'])->name('store');
    Route::get('/kyc-approved', [\App\Http\Controllers\Admin\DriverController::class, 'kycApproved'])->name('kyc.approved');
    Route::get('/kyc-pending', [\App\Http\Controllers\Admin\DriverController::class, 'kycPending'])->name('kyc.pending');
    Route::get('/{id}/approve', [\App\Http\Controllers\Admin\DriverController::class, 'approve'])->name('approve');
    Route::get('/{id}/reject', [\App\Http\Controllers\Admin\DriverController::class, 'reject'])->name('reject');
    Route::get('/{id}/view', [\App\Http\Controllers\Admin\DriverController::class, 'viewDetails'])->name('view');
    Route::post('/{id}/block', [\App\Http\Controllers\Admin\DriverController::class, 'block'])->name('block');
    Route::post('/{id}/commission', [\App\Http\Controllers\Admin\DriverController::class, 'updateCommission'])->name('commission.update');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\DriverController::class, 'edit'])->name('edit');
    Route::put('/{id}/update', [\App\Http\Controllers\Admin\DriverController::class, 'update'])->name('update');
    Route::post('/{id}/photo', [\App\Http\Controllers\Admin\DriverController::class, 'updatePhoto'])->name('photo.update');
    Route::delete('/{id}/destroy', [\App\Http\Controllers\Admin\DriverController::class, 'destroy'])->name('destroy');
    Route::post('/documents/{id}/status', [\App\Http\Controllers\Admin\DriverController::class, 'updateDocumentStatus'])->name('documents.status');
});

// Admin Settings
Route::group(['prefix' => 'admin/general-settings', 'as' => 'admin.settings.'], function () {
    Route::get('/', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('index');
    Route::post('/update', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update');
    Route::post('/test-sms', [\App\Http\Controllers\Admin\SettingController::class, 'testSMS'])->name('test_sms');
    Route::get('/homepage', [\App\Http\Controllers\Admin\SettingController::class, 'homepageSettings'])->name('homepage');
    Route::post('/homepage/update', [\App\Http\Controllers\Admin\SettingController::class, 'updateHomepageSettings'])->name('homepage.update');
    Route::get('/homepage/update', function() { return redirect()->route('admin.settings.homepage'); });
});

Route::group(['prefix' => 'admin/required-kyc-document', 'as' => 'admin.settings.kyc.'], function () {
    Route::get('/', [\App\Http\Controllers\Admin\KycRequirementController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\Admin\KycRequirementController::class, 'store'])->name('store');
    Route::put('/{id}', [\App\Http\Controllers\Admin\KycRequirementController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\KycRequirementController::class, 'destroy'])->name('destroy');
});

// Admin Localization (Currencies & Languages)
Route::group(['prefix' => 'admin/localization', 'as' => 'admin.localization.'], function () {
    Route::get('/currencies', [\App\Http\Controllers\Admin\CurrencyController::class, 'index'])->name('currencies');
    Route::post('/currencies', [\App\Http\Controllers\Admin\CurrencyController::class, 'store'])->name('currencies.store');
    Route::put('/currencies/{id}', [\App\Http\Controllers\Admin\CurrencyController::class, 'update'])->name('currencies.update');
    Route::delete('/currencies/{id}', [\App\Http\Controllers\Admin\CurrencyController::class, 'destroy'])->name('currencies.destroy');

    Route::get('/languages', [\App\Http\Controllers\Admin\LanguageController::class, 'index'])->name('languages');
    Route::post('/languages', [\App\Http\Controllers\Admin\LanguageController::class, 'store'])->name('languages.store');
    Route::put('/languages/{id}', [\App\Http\Controllers\Admin\LanguageController::class, 'update'])->name('languages.update');
    Route::delete('/languages/{id}', [\App\Http\Controllers\Admin\LanguageController::class, 'destroy'])->name('languages.destroy');
});

// Admin Vehicle Management
Route::group(['prefix' => 'admin/vehicles', 'as' => 'admin.vehicles.'], function () {
    Route::get('/categories', [\App\Http\Controllers\Admin\VehicleController::class, 'categories'])->name('categories');
    Route::post('/categories', [\App\Http\Controllers\Admin\VehicleController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{id}', [\App\Http\Controllers\Admin\VehicleController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\VehicleController::class, 'deleteCategory'])->name('categories.delete');
    Route::get('/rentals', [\App\Http\Controllers\Admin\VehicleController::class, 'rentals'])->name('rentals');
    Route::get('/pricing', [\App\Http\Controllers\Admin\VehicleController::class, 'pricing'])->name('pricing');
    Route::post('/pricing/update', [\App\Http\Controllers\Admin\VehicleController::class, 'updatePricing'])->name('pricing.update');
    Route::get('/parcel', [\App\Http\Controllers\Admin\VehicleController::class, 'freight'])->name('parcel');
    Route::post('/parcel', [\App\Http\Controllers\Admin\VehicleController::class, 'storeParcel'])->name('parcel.store');
    Route::put('/parcel/{id}', [\App\Http\Controllers\Admin\VehicleController::class, 'updateParcel'])->name('parcel.update');
    Route::delete('/parcel/{id}', [\App\Http\Controllers\Admin\VehicleController::class, 'deleteParcel'])->name('parcel.delete');
});

// Admin Bookings
Route::group(['prefix' => 'admin/bookings', 'as' => 'admin.bookings.'], function () {
    Route::get('/', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('index');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\BookingController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\BookingController::class, 'update'])->name('update');
    Route::get('/{id}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('show');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\BookingController::class, 'destroy'])->name('destroy');
});

// Admin Bids
Route::group(['prefix' => 'admin/bids', 'as' => 'admin.bids.'], function () {
    Route::get('/', [\App\Http\Controllers\Admin\BidController::class, 'index'])->name('index');
    Route::get('/settings', [\App\Http\Controllers\Admin\BidController::class, 'settings'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Admin\BidController::class, 'updateSettings'])->name('settings.update');
    Route::get('/{id}', [\App\Http\Controllers\Admin\BidController::class, 'show'])->name('show');
    Route::post('/{id}/status', [\App\Http\Controllers\Admin\BidController::class, 'updateStatus'])->name('update.status');
});

// Admin Finance
Route::group(['prefix' => 'admin/finance', 'as' => 'admin.finance.'], function () {
    Route::get('/gateways', [\App\Http\Controllers\Admin\FinanceController::class, 'gateways'])->name('gateways');
    Route::get('/commissions', [\App\Http\Controllers\Admin\FinanceController::class, 'commissions'])->name('commissions');
    Route::post('/commissions', [\App\Http\Controllers\Admin\FinanceController::class, 'updateCommissions'])->name('commissions.update');
    Route::get('/qr-codes', [\App\Http\Controllers\Admin\PartnerWalletController::class, 'qrIndex'])->name('qr');
    Route::post('/qr-codes', [\App\Http\Controllers\Admin\PartnerWalletController::class, 'qrStore'])->name('qr.store');
    Route::post('/qr-codes/{id}/activate', [\App\Http\Controllers\Admin\PartnerWalletController::class, 'qrActivate'])->name('qr.activate');
    Route::post('/qr-codes/{id}/deactivate', [\App\Http\Controllers\Admin\PartnerWalletController::class, 'qrDeactivate'])->name('qr.deactivate');
    Route::delete('/qr-codes/{id}', [\App\Http\Controllers\Admin\PartnerWalletController::class, 'qrDestroy'])->name('qr.destroy');
    Route::get('/recharges', [\App\Http\Controllers\Admin\PartnerWalletController::class, 'recharges'])->name('recharges');
    Route::get('/recharges/{id}', [\App\Http\Controllers\Admin\PartnerWalletController::class, 'showRecharge'])->name('recharges.show');
    Route::post('/recharges/{id}/approve', [\App\Http\Controllers\Admin\PartnerWalletController::class, 'approveRecharge'])->name('recharges.approve');
    Route::post('/recharges/{id}/reject', [\App\Http\Controllers\Admin\PartnerWalletController::class, 'rejectRecharge'])->name('recharges.reject');
    Route::get('/partner-wallets', [\App\Http\Controllers\Admin\PartnerWalletController::class, 'transactions'])->name('partner-transactions');
    Route::get('/partner-wallets/{id}', [\App\Http\Controllers\Admin\PartnerWalletController::class, 'partner'])->name('partner-wallet');
    Route::get('/coupons', [\App\Http\Controllers\Admin\FinanceController::class, 'coupons'])->name('coupons');
    Route::get('/transactions', [\App\Http\Controllers\Admin\FinanceController::class, 'transactions'])->name('transactions');
    Route::get('/invoices', [\App\Http\Controllers\Admin\FinanceController::class, 'invoices'])->name('invoices');
});

// Admin Subscriptions
Route::group(['prefix' => 'admin/subscriptions', 'as' => 'admin.subscriptions.'], function () {
    Route::get('/', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'index'])->name('index');
    Route::get('/requests', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'requests'])->name('requests');
    Route::post('/requests/{id}/approve', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'approveRequest'])->name('requests.approve');
    Route::post('/requests/{id}/reject', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'rejectRequest'])->name('requests.reject');
    Route::post('/store', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'store'])->name('store');
    Route::put('/{id}', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'destroy'])->name('destroy');
});

// Admin CMS
Route::group(['prefix' => 'admin/cms', 'as' => 'admin.cms.'], function () {
    Route::get('/pages', [\App\Http\Controllers\Admin\CmsController::class, 'pages'])->name('pages');
    Route::post('/pages', [\App\Http\Controllers\Admin\CmsController::class, 'storePage'])->name('pages.store');
    Route::put('/pages/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'updatePage'])->name('pages.update');
    Route::delete('/pages/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'destroyPage'])->name('pages.destroy');

    Route::get('/faqs', [\App\Http\Controllers\Admin\CmsController::class, 'faqs'])->name('faqs');
    Route::post('/faqs', [\App\Http\Controllers\Admin\CmsController::class, 'storeFaq'])->name('faqs.store');
    Route::put('/faqs/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'updateFaq'])->name('faqs.update');
    Route::delete('/faqs/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'destroyFaq'])->name('faqs.destroy');

    Route::get('/banners', [\App\Http\Controllers\Admin\CmsController::class, 'banners'])->name('banners');
    Route::post('/banners', [\App\Http\Controllers\Admin\CmsController::class, 'storeBanner'])->name('banners.store');
    Route::put('/banners/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'updateBanner'])->name('banners.update');
    Route::delete('/banners/{id}', [\App\Http\Controllers\Admin\CmsController::class, 'destroyBanner'])->name('banners.destroy');
});

// Admin Notifications
Route::group(['prefix' => 'admin/notifications', 'as' => 'admin.notifications.'], function () {
    Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
    Route::get('/{id}/open', [\App\Http\Controllers\Admin\NotificationController::class, 'open'])->name('open');
    Route::post('/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('read-all');
    Route::get('/push', [\App\Http\Controllers\Admin\NotificationController::class, 'push'])->name('push');
    Route::post('/push', [\App\Http\Controllers\Admin\NotificationController::class, 'storePush'])->name('push.store');
    
    Route::get('/email', [\App\Http\Controllers\Admin\NotificationController::class, 'email'])->name('email');
    Route::post('/email', [\App\Http\Controllers\Admin\NotificationController::class, 'storeEmail'])->name('email.store');
    
    Route::get('/sms', [\App\Http\Controllers\Admin\NotificationController::class, 'sms'])->name('sms');
    Route::post('/sms', [\App\Http\Controllers\Admin\NotificationController::class, 'storeSms'])->name('sms.store');
});

// Admin Reports
Route::group(['prefix' => 'admin/reports', 'as' => 'admin.reports.'], function () {
    Route::get('/earnings', [\App\Http\Controllers\Admin\ReportController::class, 'earnings'])->name('earnings');
    Route::get('/drivers', [\App\Http\Controllers\Admin\ReportController::class, 'drivers'])->name('drivers');
    Route::get('/bookings', [\App\Http\Controllers\Admin\ReportController::class, 'bookings'])->name('bookings');
});

});

// Driver Routes
Route::group(['prefix' => 'driver', 'as' => 'driver.'], function () {
    Route::get('/register', [\App\Http\Controllers\Frontend\DriverController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Frontend\DriverController::class, 'register'])->name('register.submit');
    
    Route::get('/login', [\App\Http\Controllers\Frontend\DriverController::class, 'showLoginForm'])->name('login');
    Route::get('/login/verify-otp', function () { 
        if (session()->has('mobile')) return view('frontend.driver-verify-otp');
        return redirect()->route('driver.login'); 
    })->name('login.verifyOtpForm');
    Route::post('/login/send-otp', [\App\Http\Controllers\Frontend\DriverController::class, 'sendOtp'])->name('login.sendOtp');
    Route::post('/login/verify-otp', [\App\Http\Controllers\Frontend\DriverController::class, 'verifyOtp'])->name('login.verifyOtp');
    
    Route::get('/dashboard', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'showProfile'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/toggle-status', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'toggleOnlineStatus'])->name('toggleStatus');
    Route::post('/update-location', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'updateLocation'])->name('updateLocation');
    Route::get('/availability', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'showAvailability'])->name('availability');
    Route::post('/availability', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'updateAvailability'])->name('availability.update');
    
    // Vehicle Management
    Route::group(['prefix' => 'vehicles', 'as' => 'vehicles.'], function () {
        Route::get('/', [\App\Http\Controllers\Frontend\DriverVehicleController::class, 'index'])->name('index');
        Route::get('/add', [\App\Http\Controllers\Frontend\DriverVehicleController::class, 'create'])->name('create');
        Route::post('/add', [\App\Http\Controllers\Frontend\DriverVehicleController::class, 'store'])->name('store');
        Route::post('/{id}/active', [\App\Http\Controllers\Frontend\DriverVehicleController::class, 'setActive'])->name('setActive');
        Route::delete('/{id}', [\App\Http\Controllers\Frontend\DriverVehicleController::class, 'destroy'])->name('destroy');
    });

    // Ride Management
    Route::group(['prefix' => 'rides', 'as' => 'rides.'], function () {
        Route::get('/', [\App\Http\Controllers\Frontend\DriverRideController::class, 'index'])->name('index');
        Route::get('/requests', [\App\Http\Controllers\Frontend\DriverRideController::class, 'getNewRequests'])->name('requests');
        Route::post('/{id}/accept', [\App\Http\Controllers\Frontend\DriverRideController::class, 'acceptRide'])->name('accept');
        Route::post('/{id}/reject', [\App\Http\Controllers\Frontend\DriverRideController::class, 'rejectRide'])->name('reject');
        Route::get('/{id}/details', [\App\Http\Controllers\Frontend\DriverRideController::class, 'showRideDetails'])->name('details');
        Route::post('/{id}/arrived', [\App\Http\Controllers\Frontend\DriverRideController::class, 'markArrived'])->name('arrived');
        Route::post('/{id}/pickup', [\App\Http\Controllers\Frontend\DriverRideController::class, 'pickupPassenger'])->name('pickup');
        Route::post('/{id}/complete', [\App\Http\Controllers\Frontend\DriverRideController::class, 'completeRide'])->name('complete');
    });

    // Bidding System
    Route::group(['prefix' => 'bids', 'as' => 'bids.'], function () {
        Route::get('/', [\App\Http\Controllers\Frontend\DriverBidController::class, 'index'])->name('index');
        Route::post('/store', [\App\Http\Controllers\Frontend\DriverBidController::class, 'store'])->name('store');
        Route::post('/{id}/update', [\App\Http\Controllers\Frontend\DriverBidController::class, 'update'])->name('update');
        Route::post('/{id}/withdraw', [\App\Http\Controllers\Frontend\DriverBidController::class, 'withdraw'])->name('withdraw');
    });

    // Finance & Wallet
    Route::get('/earnings', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'earnings'])->name('earnings');
    Route::get('/wallet', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'wallet'])->name('wallet');
    Route::get('/wallet/history', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'history'])->name('wallet.history');
    Route::get('/wallet/add-money', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'addMoneyForm'])->name('wallet.add');
    Route::post('/wallet/add-money', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'startRecharge'])->name('wallet.add.submit');
    Route::get('/wallet/pay/{id}', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'showPayment'])->name('wallet.pay');
    Route::post('/wallet/pay/{id}', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'submitPayment'])->name('wallet.pay.submit');
    Route::post('/withdraw', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'requestWithdrawal'])->name('withdraw.request');
    Route::get('/subscriptions', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'subscriptions'])->name('subscriptions');
    Route::post('/subscriptions/purchase', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'purchaseSubscription'])->name('subscriptions.purchase');
    Route::post('/subscriptions/purchase-manual', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'purchaseSubscriptionManual'])->name('subscriptions.purchase_manual');
    Route::post('/subscriptions/purchase-razorpay-init', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'purchaseSubscriptionRazorpayInit'])->name('subscriptions.purchase_razorpay_init');
    Route::post('/subscriptions/purchase-razorpay-complete', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'purchaseSubscriptionRazorpayComplete'])->name('subscriptions.purchase_razorpay_complete');
    Route::get('/subscriptions/purchase-paypal', [\App\Http\Controllers\Frontend\DriverFinanceController::class, 'purchaseSubscriptionPayPal'])->name('subscriptions.purchase_paypal');

    // Communication & Support
    Route::group(['prefix' => 'support', 'as' => 'support.'], function () {
        Route::get('/', [\App\Http\Controllers\Frontend\DriverSupportController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Frontend\DriverSupportController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Frontend\DriverSupportController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Frontend\DriverSupportController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [\App\Http\Controllers\Frontend\DriverSupportController::class, 'reply'])->name('reply');
    });

    Route::group(['prefix' => 'chat', 'as' => 'chat.'], function () {
        Route::get('/{bookingId}', [\App\Http\Controllers\Frontend\RideChatController::class, 'index'])->name('index');
        Route::post('/{bookingId}/send', [\App\Http\Controllers\Frontend\RideChatController::class, 'sendMessage'])->name('send');
        Route::get('/{bookingId}/messages', [\App\Http\Controllers\Frontend\RideChatController::class, 'getMessages'])->name('get');
    });

    Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
        Route::get('/', [\App\Http\Controllers\Frontend\DriverNotificationController::class, 'index'])->name('index');
        Route::get('/{id}/read', [\App\Http\Controllers\Frontend\DriverNotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [\App\Http\Controllers\Frontend\DriverNotificationController::class, 'markAllRead'])->name('markAllRead');
    });

    Route::get('/ratings', [\App\Http\Controllers\Frontend\DriverRatingController::class, 'index'])->name('ratings');
    Route::get('/settings', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'showSettings'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'updateSettings'])->name('settings.update');

    Route::get('/kyc', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'showKycForm'])->name('kyc');
    Route::post('/kyc', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'uploadKyc'])->name('kyc.store');
    Route::post('/logout', [\App\Http\Controllers\Frontend\DriverDashboardController::class, 'logout'])->name('logout');
});


