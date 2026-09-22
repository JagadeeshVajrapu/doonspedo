<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    // Pages
    public function pages()
    {
        $pages = \App\Models\Page::latest()->paginate(15);
        return view('backend.cms.pages', compact('pages'));
    }

    public function storePage(Request $request) 
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:pages,slug',
            'content' => 'required',
            'is_active' => 'boolean'
        ]);

        \App\Models\Page::create($validated);
        return back()->with('success', 'Page saved successfully'); 
    }

    public function updatePage(Request $request, $id) 
    {
        $page = \App\Models\Page::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:pages,slug,'.$id,
            'content' => 'required',
            'is_active' => 'boolean'
        ]);

        $page->update($validated);
        return back()->with('success', 'Page updated successfully'); 
    }

    public function destroyPage($id) 
    {
        \App\Models\Page::findOrFail($id)->delete();
        return back()->with('success', 'Page deleted successfully'); 
    }

    // FAQs
    public function faqs()
    {
        $faqs = \App\Models\Faq::latest()->paginate(15);
        return view('backend.cms.faqs', compact('faqs'));
    }

    public function storeFaq(Request $request) 
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'is_active' => 'nullable'
        ]);

        $validated['is_active'] = $request->has('is_active');

        \App\Models\Faq::create($validated);
        return back()->with('success', 'FAQ saved successfully'); 
    }

    public function updateFaq(Request $request, $id) 
    {
        $faq = \App\Models\Faq::findOrFail($id);
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'is_active' => 'nullable'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $faq->update($validated);
        return back()->with('success', 'FAQ updated successfully'); 
    }

    public function destroyFaq($id) 
    {
        \App\Models\Faq::findOrFail($id)->delete();
        return back()->with('success', 'FAQ deleted successfully'); 
    }

    // Banners
    public function banners()
    {
        $banners = \App\Models\Banner::latest()->paginate(15);
        return view('backend.cms.banners', compact('banners'));
    }

    public function storeBanner(Request $request) 
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
            'position' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $validated['image_path'] = $path;
            unset($validated['image']);
        }

        \App\Models\Banner::create($validated);
        return back()->with('success', 'Banner saved successfully'); 
    }

    public function updateBanner(Request $request, $id) 
    {
        $banner = \App\Models\Banner::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
            'position' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $validated['image_path'] = $path;
            unset($validated['image']);
        }

        $banner->update($validated);
        return back()->with('success', 'Banner updated successfully'); 
    }

    public function destroyBanner($id) 
    {
        \App\Models\Banner::findOrFail($id)->delete();
        return back()->with('success', 'Banner deleted successfully'); 
    }
}

