<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = \App\Models\Language::latest()->paginate(15);
        return view('backend.localization.languages', compact('languages'));
    }

    public function store(Request $request)
    {
        \App\Models\Language::create($request->all());
        return back()->with('success', 'Language created successfully');
    }

    public function update(Request $request, $id)
    {
        $language = \App\Models\Language::findOrFail($id);
        $language->update($request->all());
        return back()->with('success', 'Language updated successfully');
    }

    public function destroy($id)
    {
        \App\Models\Language::destroy($id);
        return back()->with('success', 'Language deleted successfully');
    }
}
