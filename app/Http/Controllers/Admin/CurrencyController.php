<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = \App\Models\Currency::latest()->paginate(15);
        return view('backend.localization.currencies', compact('currencies'));
    }

    public function store(Request $request)
    {
        \App\Models\Currency::create($request->all());
        return back()->with('success', 'Currency created successfully');
    }

    public function update(Request $request, $id)
    {
        $currency = \App\Models\Currency::findOrFail($id);
        $currency->update($request->all());
        return back()->with('success', 'Currency updated successfully');
    }

    public function destroy($id)
    {
        \App\Models\Currency::destroy($id);
        return back()->with('success', 'Currency deleted successfully');
    }
}
