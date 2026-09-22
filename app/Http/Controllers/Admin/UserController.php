<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Assuming User model for riders/customers

class UserController extends Controller
{
    public function index()
    {
        // For now, let's just create an empty collection or mock data
        $users = User::latest()->get(); 
        return view('backend.users.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('backend.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        // $user->status = $request->input('status'); // We will leave this commented out until a 'status' column is added to the User model.
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Customer profile updated successfully.');
    }

    public function viewDetails($id)
    {
        $user = User::findOrFail($id);
        return view('backend.users.view', compact('user'));
    }

    public function destroy($id)
    {
        // Dummy delete
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function blocked()
    {
        // Dummy blocked
        // Add a "status" column to User model if not exist. Assume some are blocked.
        $blockedUsers = collect(); // User::where('status', 'blocked')->get();
        return view('backend.users.blocked', compact('blockedUsers'));
    }

    public function block($id)
    {
        // Dummy block
        return redirect()->route('admin.users.index')->with('success', 'User blocked successfully.');
    }

    public function wallet()
    {
        // Mocking wallet details
        $users = User::all();
        return view('backend.users.wallet', compact('users'));
    }
}
