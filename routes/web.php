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

    // Notifications - All roles
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
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
        
        // Known Threats Management (Full CRUD for admins)
        Route::post('/known-threats', [KnownThreatController::class, 'store'])->name('known-threats.store');
        Route::delete('/known-threats/{knownThreat}', [KnownThreatController::class, 'destroy'])->name('known-threats.destroy');
        
        // Admin can access all analyst routes too
        Route::get('/report-queue', [ReportQueueController::class, 'index'])->name('report-queue.index');
        Route::get('/report-queue/{report}', [ReportQueueController::class, 'show'])->name('report-queue.show');
        Route::put('/report-queue/{report}', [ReportQueueController::class, 'update'])->name('report-queue.update');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
        Route::get('/known-threats', [KnownThreatController::class, 'index'])->name('known-threats');
    });
});
