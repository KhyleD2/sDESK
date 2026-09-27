# Archive System Implementation - Complete ✅

## Date: September 27, 2026

## Summary
Implemented a soft delete (archive) system where users can remove their reports, and admins can view, restore, or permanently delete archived reports from a dedicated Archives page.

---

## What Was Implemented

### 1. Database Changes
- **Migration**: `2026_09_27_202016_add_deleted_at_to_threat_reports_table.php`
- **Columns Added**:
  - `deleted_at` (timestamp, nullable) - Soft delete timestamp
  - `archived_by` (string, nullable) - Tracks who archived the report
  - `archive_reason` (string, nullable) - Reason for archiving
- **Status**: ✅ Migrated successfully

### 2. Model Updates
- **File**: `app/Models/ThreatReport.php`
- **Changes**:
  - Added `SoftDeletes` trait
  - Added `archived_by` and `archive_reason` to fillable
  - Added `deleted_at` to casts

### 3. Archive/Remove Functionality
- **File**: `app/Http/Controllers/ThreatReportController.php`
- **Updated `destroy()` method**:
  - Users can only remove their own reports
  - Analysts/Admins can remove any report
  - Soft deletes (archives) instead of hard deletes
  - Tracks who archived and why
  - Logs activity when report is archived
  - Success message: "Report has been archived successfully"

### 4. Admin Archives Controller
- **File**: `app/Http/Controllers/Admin/ArchivesController.php`
- **Methods**:
  - `index()` - Display all archived reports with pagination (15 per page)
  - `restore($id)` - Restore an archived report back to active
  - `forceDelete($id)` - Permanently delete report and associated files

### 5. Archives View
- **File**: `resources/views/admin/archives.blade.php`
- **Features**:
  - Table view of all archived reports
  - Shows: Report ID, Submitted By (with profile picture), Category, Archived By, Archived Date
  - Two action buttons per report:
    - **Restore**: Brings report back to active state
    - **Delete**: Permanently deletes report with confirmation
  - Pagination for archives
  - Empty state when no archived reports
  - Theme-aware design (dark mint + light mode)

### 6. Remove Button on Reports
- **File**: `resources/views/reports/show.blade.php`
- **Added "Remove Report" button**:
  - Visible to report owner, analysts, and admins
  - Red/rose color scheme (archive icon)
  - Confirmation dialog before archiving
  - Located next to "Back to Reports" button

### 7. Navigation Updates
- **File**: `resources/views/layouts/app.blade.php`
- **Added "Archives" to Admin Sidebar**:
  - Located in SYSTEM section
  - Between "Login Logs" and "Settings"
  - Archive icon (`fa-archive`)
  - Active state detection

### 8. Routes Added
```php
// Admin Archives
Route::get('/admin/archives', [ArchivesController::class, 'index'])
    ->name('admin.archives');
    
Route::post('/admin/archives/{id}/restore', [ArchivesController::class, 'restore'])
    ->name('admin.archives.restore');
    
Route::delete('/admin/archives/{id}/force-delete', [ArchivesController::class, 'forceDelete'])
    ->name('admin.archives.force-delete');
```

---

## How It Works

### For Users:
1. User views their report
2. Clicks "Remove Report" button (red button with archive icon)
3. Confirms the action in dialog
4. Report is soft deleted (archived)
5. Report disappears from their reports list
6. Activity log records the action

### For Admins:
1. Admin navigates to **Admin → Archives** in sidebar
2. Sees table of all archived reports with:
   - Report details
   - Who archived it and when
   - Reason for archiving
3. Can choose to:
   - **Restore**: Report becomes active again
   - **Delete**: Permanently removes report and files (with strong confirmation)

---

## Permissions

| Action | User | Analyst | Admin |
|--------|------|---------|-------|
| Remove own report | ✅ | ✅ | ✅ |
| Remove any report | ❌ | ✅ | ✅ |
| View archives | ❌ | ❌ | ✅ |
| Restore archived report | ❌ | ❌ | ✅ |
| Permanently delete | ❌ | ❌ | ✅ |

---

## Archive Metadata Tracked

When a report is archived, the system records:
- **archived_by**: Full name and role (e.g., "John Doe (user)")
- **archive_reason**: 
  - "User removed report" (when user archives their own)
  - "Archived by analyst" (when analyst archives)
  - "Archived by admin" (when admin archives)
- **deleted_at**: Timestamp when archived

---

## Safety Features

### Soft Delete Protection
- Reports are **never** hard deleted by users
- Only admins can permanently delete
- All archived reports remain in database until admin action

### Confirmation Dialogs
1. **Remove Report**: "Are you sure you want to remove this report? It will be archived and can be restored by admins."
2. **Permanent Delete**: "⚠️ PERMANENTLY DELETE this report? This action CANNOT be undone and all associated files will be deleted!"

### File Management
- Soft deleted reports keep their files
- Files only deleted on permanent delete (force delete)
- Storage cleanup happens automatically

---

## UI/UX Details

### Remove Button Design
- Color: Rose/Red (--rose, --rose-dim)
- Icon: Archive icon (fa-archive)
- Position: Next to "Back to Reports" button
- Style: Matches existing button design system

### Archives Page Design
- Clean table layout
- Profile pictures in "Submitted By" column
- Amber badge for "Archived By" info
- Monospace fonts for dates/IDs
- Hover effects on table rows
- Action buttons with distinct colors (green restore, red delete)

### Empty State
```
🗄️ No Archived Reports
Archived reports will appear here
```

---

## Testing Checklist

- [x] Migration runs successfully
- [x] Soft delete trait added to model
- [x] Users can remove their own reports
- [x] Reports disappear from regular listings when archived
- [x] Archived reports appear in Admin Archives page
- [x] Admins can view archived reports
- [x] Restore functionality works
- [x] Permanent delete works and removes files
- [x] Activity logging records archive actions
- [x] Archives link in admin sidebar
- [x] Confirmation dialogs appear
- [x] Profile pictures display in archives table
- [x] Pagination works
- [x] Theme consistency (dark/light mode)

---

## Database Schema

### threat_reports table (new columns)
```sql
deleted_at TIMESTAMP NULL -- Soft delete timestamp
archived_by VARCHAR(255) NULL -- Who archived it
archive_reason VARCHAR(255) NULL -- Why it was archived
```

---

## Possible Archive Reasons

Based on your request, here are items that could be archived:

### User-Initiated Archives:
- ✅ **Wrong Report Submission** - User realized they submitted incorrect information
- ✅ **Duplicate Report** - User accidentally submitted the same report twice
- ✅ **No Longer Relevant** - Issue resolved before analyst review
- ✅ **Mistake/Test Submission** - User was testing or made an error

### Analyst/Admin-Initiated Archives:
- ✅ **Spam/Abuse** - Report is spam or misuse of system
- ✅ **Invalid Format** - Report doesn't meet submission requirements
- ✅ **Out of Scope** - Report doesn't fall under system's jurisdiction
- ✅ **Duplicate** - Already exists in system
- ✅ **Low Quality** - Insufficient information provided
- ✅ **Resolved Elsewhere** - Already handled through other channels

---

## Future Enhancements (Optional)

If you want to add more features later:
- Add dropdown for archive reasons when removing
- Auto-archive reports after X days of being resolved
- Archive statistics dashboard
- Bulk archive/restore functionality
- Export archived reports to CSV
- Archive search/filter functionality

---

## Files Modified/Created

### Created:
1. `database/migrations/2026_09_27_202016_add_deleted_at_to_threat_reports_table.php`
2. `app/Http/Controllers/Admin/ArchivesController.php`
3. `resources/views/admin/archives.blade.php`

### Modified:
1. `app/Models/ThreatReport.php` - Added SoftDeletes
2. `app/Http/Controllers/ThreatReportController.php` - Updated destroy method
3. `resources/views/reports/show.blade.php` - Added Remove button
4. `resources/views/layouts/app.blade.php` - Added Archives link
5. `routes/web.php` - Added archive routes

---

**Status**: ✅ **COMPLETE AND READY FOR PRESENTATION**

The archive system is fully functional. Users can remove reports they submitted by mistake, and admins have full control over archived reports through a dedicated Archives page.
