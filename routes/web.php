<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\Analyst\ActivityLogController;
use App\Http\Controllers\Analyst\DashboardController;
use App\Http\Controllers\Analyst\ReportQueueController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\KnownThreatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ThreatReportController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// 2FA routes (not protected by auth middleware since user is temporarily logged out)
Route::get('/2fa/verify', [LoginController::class, 'showTwoFactorForm'])->name('2fa.verify');
Route::post('/2fa/verify', [LoginController::class, 'verifyTwoFactor']);
Route::post('/2fa/resend', [LoginController::class, 'resendTwoFactorCode'])->name('2fa.resend');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    
    // Shared Dashboard Route - redirects based on role
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->isAnalyst()) {
            return redirect()->route('analyst.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    // Attachment Download Route - accessible to report owner, analysts, and admins
    Route::get('/attachments/{attachment}', [AttachmentController::class, 'download'])->name('attachments.download');

    // User Routes
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {
        // User Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'index'])->name('dashboard');
    });

    // User Routes - Threat Reports (Users can submit, all can view their own)
    Route::resource('reports', ThreatReportController::class);
    
    // Report Comments - All authenticated users
    Route::post('/reports/{report}/comments', [\App\Http\Controllers\ReportCommentController::class, 'store'])->name('reports.comments.store');

    // Notifications - All roles
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    
    // Profile/Settings - All roles
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/picture', [\App\Http\Controllers\ProfileController::class, 'removeProfilePicture'])->name('profile.remove-picture');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    
    // API endpoints for notification dropdown
    Route::get('/api/notifications/recent', [NotificationController::class, 'getRecent'])->name('api.notifications.recent');
    Route::post('/api/notifications/{notification}/read', [NotificationController::class, 'markAsReadApi'])->name('api.notifications.read');
    Route::post('/api/notifications/read-all', [NotificationController::class, 'markAllAsReadApi'])->name('api.notifications.read-all');

    // Analyst Routes
    Route::middleware('role:analyst,admin')->prefix('analyst')->name('analyst.')->group(function () {
        // Analyst Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Report Queue
        Route::get('/report-queue', [ReportQueueController::class, 'index'])->name('report-queue.index');
        Route::get('/report-queue/{report}', [ReportQueueController::class, 'show'])->name('report-queue.show');
        Route::put('/report-queue/{report}', [ReportQueueController::class, 'update'])->name('report-queue.update');
        
        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
        
        // Actions Management
        Route::post('/reports/{report}/actions', [ActionController::class, 'store'])->name('actions.store');
        Route::put('/actions/{action}', [ActionController::class, 'update'])->name('actions.update');
        Route::get('/my-actions', [ActionController::class, 'index'])->name('my-actions');
        
        // Known Threats (Read only for analysts)
        Route::get('/known-threats', [KnownThreatController::class, 'index'])->name('known-threats');
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // Admin Dashboard (different from analyst)
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // User Management
        Route::resource('users', \App\Http\Controllers\Admin\UserManagementController::class);
        
        // Analytics
        Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/export', [\App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('analytics.export');
        
        // Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
        Route::post('/settings/categories', [\App\Http\Controllers\Admin\SettingsController::class, 'storeCategory'])->name('settings.categories.store');
        Route::put('/settings/categories/{category}', [\App\Http\Controllers\Admin\SettingsController::class, 'updateCategory'])->name('settings.categories.update');
        Route::delete('/settings/categories/{category}', [\App\Http\Controllers\Admin\SettingsController::class, 'deleteCategory'])->name('settings.categories.delete');
        Route::post('/settings/account', [\App\Http\Controllers\Admin\SettingsController::class, 'updateAccount'])->name('settings.account.update');
        
        // Login Logs
        Route::get('/login-logs', [\App\Http\Controllers\Admin\LoginLogsController::class, 'index'])->name('login-logs');
        Route::post('/login-logs/block/{user}', [\App\Http\Controllers\Admin\LoginLogsController::class, 'blockUser'])->name('login-logs.block');
        Route::post('/login-logs/unblock/{user}', [\App\Http\Controllers\Admin\LoginLogsController::class, 'unblockUser'])->name('login-logs.unblock');
        
        // Known Threats Management (Full CRUD for admins)
        Route::post('/known-threats', [KnownThreatController::class, 'store'])->name('known-threats.store');
        Route::delete('/known-threats/{knownThreat}', [KnownThreatController::class, 'destroy'])->name('known-threats.destroy');
        
        // Archives Management
        Route::get('/archives', [\App\Http\Controllers\Admin\ArchivesController::class, 'index'])->name('archives');
        Route::post('/archives/{id}/restore', [\App\Http\Controllers\Admin\ArchivesController::class, 'restore'])->name('archives.restore');
        Route::delete('/archives/{id}/force-delete', [\App\Http\Controllers\Admin\ArchivesController::class, 'forceDelete'])->name('archives.force-delete');
        
        // Admin can access all analyst routes too
        Route::get('/report-queue', [ReportQueueController::class, 'index'])->name('report-queue.index');
        Route::get('/report-queue/{report}', [ReportQueueController::class, 'show'])->name('report-queue.show');
        Route::put('/report-queue/{report}', [ReportQueueController::class, 'update'])->name('report-queue.update');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
        Route::get('/known-threats', [KnownThreatController::class, 'index'])->name('known-threats');
    });
    
    // Debug routes accessible to both admin and analyst
    Route::middleware('role:admin,analyst')->prefix('admin')->name('admin.')->group(function () {
        // Diagnostic route for debugging resolution time
        Route::get('/debug/resolution-time', function () {
            $reports = \App\Models\ThreatReport::select('id', 'status', 'created_at', 'updated_at')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function($report) {
                    $hours = null;
                    $days = null;
                    if ($report->created_at && $report->updated_at) {
                        $hours = $report->created_at->diffInHours($report->updated_at);
                        $days = round($hours / 24, 2);
                    }
                    return [
                        'id' => $report->id,
                        'status' => $report->status,
                        'created_at' => $report->created_at?->toDateTimeString(),
                        'updated_at' => $report->updated_at?->toDateTimeString(),
                        'hours_diff' => $hours,
                        'days_diff' => $days
                    ];
                });
            
            $resolvedCount = \App\Models\ThreatReport::where('status', 'resolved')->count();
            
            $avgResolution = \App\Models\ThreatReport::where('status', 'resolved')
                ->whereNotNull('updated_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at) / 24.0) as avg_days')
                ->value('avg_days');
            
            return response()->json([
                'resolved_count' => $resolvedCount,
                'avg_resolution_days' => $avgResolution ? round($avgResolution, 2) : null,
                'all_reports' => $reports
            ], 200, [], JSON_PRETTY_PRINT);
        })->name('debug.resolution-time');
        
        // Diagnostic route for file storage debugging
        Route::get('/debug/attachments', function () {
            $attachments = \App\Models\Attachment::orderBy('id', 'desc')
                ->limit(10)
                ->get()
                ->map(function($attachment) {
                    $storagePath = $attachment->storage_path;
                    $fileExists = \Storage::disk('local')->exists($storagePath);
                    $fullPath = \Storage::disk('local')->path($storagePath);
                    
                    return [
                        'id' => $attachment->id,
                        'original_filename' => $attachment->original_filename,
                        'stored_filename' => $attachment->stored_filename,
                        'storage_path' => $storagePath,
                        'file_hash' => substr($attachment->file_hash, 0, 32) . '...',
                        'file_type' => $attachment->file_type,
                        'file_exists' => $fileExists,
                        'full_path' => $fullPath,
                        'disk_root' => storage_path('app/private'),
                    ];
                });
            
            return response()->json([
                'total_attachments' => \App\Models\Attachment::count(),
                'attachments' => $attachments,
                'storage_info' => [
                    'local_disk_root' => storage_path('app/private'),
                    'threat_attachments_dir_exists' => is_dir(storage_path('app/private/threat_attachments')),
                    'files_in_threat_attachments' => is_dir(storage_path('app/private/threat_attachments')) 
                        ? array_slice(scandir(storage_path('app/private/threat_attachments')), 2, 10) 
                        : []
                ]
            ], 200, [], JSON_PRETTY_PRINT);
        })->name('debug.attachments');
    });
});
