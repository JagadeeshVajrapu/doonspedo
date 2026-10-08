<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    private $settingsFile;

    public function __construct()
    {
        $this->settingsFile = storage_path('app/settings.json');
    }

    private function getSettings()
    {
        if (File::exists($this->settingsFile)) {
            return json_decode(File::get($this->settingsFile), true);
        }
        return [];
    }

    private function saveSettings($data)
    {
        File::put($this->settingsFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function index()
    {
        $settings = $this->getSettings();
        return view('backend.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = $this->getSettings();

        // Handle string settings
        // Handle boolean and array settings
        $booleanKeys = ['multi_currency_enabled', 'rtl_enabled', 'manual_payment_enabled'];
        foreach ($booleanKeys as $key) {
            $settings[$key] = $request->has($key) ? true : false;
        }

        // Handle string settings
        $keys = [
            'app_name', 'support_email', 'contact_phone', 'currency', 'theme_color',
            'razorpay_key', 'razorpay_secret', 'paypal_id',
            'base_fare', 'rate_per_mile', 'google_maps_key', 'map_provider',
            'default_language', 'supported_languages', 'timezone', 'date_format',
            'sms_provider', 'true_bulk_sms_username', 'true_bulk_sms_password', 'true_bulk_sms_sender_id', 'true_bulk_sms_entity_id', 'true_bulk_sms_temp_id',
            'manual_payment_instructions'
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                $settings[$key] = $request->input($key);
            }
        }

        if ($request->exists('max_driver_acceptance_km')) {
            $validated = $request->validate([
                'max_driver_acceptance_km' => 'required|numeric|min:0.1|max:100',
            ]);
            $settings['max_driver_acceptance_km'] = round((float) $validated['max_driver_acceptance_km'], 1);
        }

        // Handle File upload particularly for webp/png/jpg
        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');
            $extension = $file->getClientOriginalExtension();
            $filename = 'logo.' . $extension;
            
            // Move file to public directory so it's accessible directly
            $file->move(public_path('uploads/logo'), $filename);
            
            $settings['app_logo'] = 'uploads/logo/' . $filename;
        }

        if ($request->hasFile('manual_payment_qr')) {
            $file = $request->file('manual_payment_qr');
            $extension = $file->getClientOriginalExtension();
            $filename = 'manual_qr_' . time() . '.' . $extension;
            $file->move(public_path('uploads/payments'), $filename);
            $settings['manual_payment_qr'] = 'uploads/payments/' . $filename;
        }

        $this->saveSettings($settings);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function testSMS(Request $request)
    {
        $mobile = $request->input('mobile');
        if (empty($mobile)) {
            return response()->json(['success' => false, 'message' => 'Please provide a mobile number.']);
        }

        $settings = $this->getSettings();
        $message = "This is a test message from " . ($settings['app_name'] ?? 'Your App');

        
        // We use the send_sms helper directly. 
        // Note: In a real scenario, we might want to pass the temp ID specifically for testing if it's not saved yet.
        // But here we'll assume they saved or we can pull from request if we wanted to be fancy.
        // For simplicity, let's just use the current saved settings.
        
        $success = send_sms($mobile, $message);

        if ($success) {
            return response()->json(['success' => true, 'message' => 'Test SMS sent successfully! Check your phone.']);
        } else {
            return response()->json(['success' => false, 'message' => 'SMS Sending failed. Check logs or credentials.']);
        }
    }
    public function homepageSettings()
    {
        $settings = $this->getSettings();
        $faqs = Faq::latest()->paginate(20);
        return view('backend.settings.homepage', compact('settings', 'faqs'));
    }

    public function updateHomepageSettings(Request $request)
    {
        $settings = $this->getSettings();

        $keys = [
            'hero_title', 'hero_subtitle', 'hero_cta_text', 'hero_cta_link',
            'about_title', 'about_description',
            'wcu_title', 'wcu_subtitle',
            'wcu_feature1_title', 'wcu_feature1_desc', 'wcu_feature1_icon',
            'wcu_feature2_title', 'wcu_feature2_desc', 'wcu_feature2_icon',
            'wcu_feature3_title', 'wcu_feature3_desc', 'wcu_feature3_icon',
            'popup_title', 'popup_link', 'popup_delay'
        ];

        foreach ($keys as $key) {
            $settings[$key] = $request->input($key);
        }

        $settings['popup_enabled'] = $request->has('popup_enabled') ? '1' : '0';

        // Handle File uploads
        $fileKeys = ['hero_image', 'about_image', 'popup_image'];
        foreach ($fileKeys as $fKey) {
            if ($request->hasFile($fKey)) {
                $file = $request->file($fKey);
                $filename = $fKey . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/homepage'), $filename);
                $settings[$fKey] = 'uploads/homepage/' . $filename;
            }
        }

        $this->saveSettings($settings);

        return redirect()->back()->with('success', 'Homepage settings updated successfully.');
    }
}
