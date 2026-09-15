<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;

class LoginLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = LoginLog::with('user')->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(50);

        return view('admin.login-logs', compact('logs'));
    }

    public function blockUser($userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->isAdmin()) {
            return back()->withErrors(['error' => 'Cannot block admin users.']);
        }

        $user->is_blocked = true;
        $user->save();

        return back()->with('success', "User {$user->email} has been blocked.");
    }

    public function unblockUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_blocked = false;
        $user->failed_login_attempts = 0;
        $user->locked_until = null;
        $user->save();

        return back()->with('success', "User {$user->email} has been unblocked.");
    }
}
