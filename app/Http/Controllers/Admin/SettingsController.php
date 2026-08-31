<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThreatCategory;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $categories = ThreatCategory::orderBy('name')->get();
        $user = auth()->user();

        return view('admin.settings', compact('categories', 'user'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:threat_categories,name'],
        ]);

        ThreatCategory::create($validated);

        return back()->with('success', 'Category added successfully!');
    }

    public function updateCategory(Request $request, ThreatCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:threat_categories,name,' . $category->id],
        ]);

        $category->update($validated);

        return back()->with('success', 'Category updated successfully!');
    }

    public function deleteCategory(ThreatCategory $category)
    {
        // Check if category has reports
        if ($category->threatReports()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing reports. Reassign reports first.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully!');
    }

    public function updateAccount(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Account updated successfully!');
    }
}
