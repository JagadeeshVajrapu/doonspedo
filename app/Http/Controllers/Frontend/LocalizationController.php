<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Language;
use App\Models\Currency;

class LocalizationController extends Controller
{
    public function setLocale($locale)
    {
        $lang = Language::where('code', $locale)->first();
        if ($lang) {
            Session::put('locale', $locale);
            Session::put('direction', $lang->direction); // rtl/ltr
        }
        return back();
    }

    public function setCurrency($code)
    {
        $currency = Currency::where('code', $code)->first();
        if ($currency) {
            Session::put('currency', [
                'code' => $currency->code,
                'symbol' => $currency->symbol,
                'rate' => $currency->exchange_rate
            ]);
        }
        return back();
    }

    public function setTheme($theme)
    {
        if (in_array($theme, ['light', 'dark'])) {
            Session::put('theme', $theme);
        }
        return back();
    }
}
