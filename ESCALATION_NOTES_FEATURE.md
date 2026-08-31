# Escalation Notes & Enhanced Notifications Feature

**Date:** August 31, 2026  
**Status:** ✅ IMPLEMENTED  
**Feature:** Make escalation notes visible and create specific notifications

---

## Overview

Extended the notification and activity log system to make Escalation Notes visible throughout the application and create specific notifications when reports are escalated externally.

---

## 1. Escalation Note Visibility

### **A) Report Details Display**

Escalation notes are now visible in BOTH the Analyst/Admin view and the User's own report view.

**Location 1: Analyst Report Review** (`resources/views/analyst/report-review.blade.php`)
- Added new section in "Report Details" card
- **Condition:** Only shows when `verdict === 'escalated_externally'` AND `escalation_note` is not empty
- **Styling:** Uses the same `description-box` style as other report fields
- **Position:** Appears after the Description field

```blade
@if($report->verdict === 'escalated_externally' && !empty($report->escalation_note))
<div class="detail-row">
    <div class="detail-label">Escalation Note:</div>
    <div class="description-box">{{ $report->escalation_note }}</div>
</div>
@endif
```

**Location 2: User Report View** (`resources/views/reports/show.blade.php`)
- Added in the Report Details card, after Verdict and Submitted fields
- **Condition:** Same as above - only shows for escalated reports with notes
- **Styling:** Consistent with the report card design
- **Position:** Last row in the Report Details section

```blade
@if($report->verdict === 'escalated_externally' && !empty($report->escalation_note))
<div class="detail-row">
    <div class="detail-label">Escalation Note</div>
    <div class="detail-value">
        <div class="description-box" style="margin-top: 0;">{{ $report->escalation_note }}</div>
    </div>
</div>
@endif
```

---

### **B) Activity Log Entry on Escalation**

**Controllers Updated:**
- `app/Http/Controllers/Analyst/ReportQueueController.php`
- `app/Http/Controllers/ThreatReportController.php`

**Implementation:**
When a report's verdict changes TO `escalated_externally`, a specific activity log entry is created:

```php
// Handle escalation to external - specific notification and activity log
if (isset($validated['verdict']) && 
    $validated['verdict'] === 'escalated_externally' && 
    $oldVerdict !== 'escalated_externally') {
    
    // Create specific activity log entry for escalation with the note
    $escalationDescription = auth()->user()->name . ' escalated this report externally';
    if (!empty($validated['escalation_note'])) {
        $escalationDescription .= ': ' . $validated['escalation_note'];
    }
    
    ActivityLog::create([
        'report_id' => $report->id,
        'user_id' => auth()->id(),
        'action_description' => $escalationDescription,
    ]);
}
```

**Activity Log Examples:**
- With note: "John Doe escalated this report externally: Forwarded to FBI Cyber Division for investigation"
- Without note: "John Doe escalated this report externally"

**Display:** Appears in the Activity Log feed on both:
- Analyst report review page
- User report show page

---

## 2. Escalation-Specific Notifications

### **Implementation**

When a report is escalated externally, TWO notifications are now created:

**1. Generic Update Notification** (existing behavior - kept)
```php
Notification::create([
    'user_id' => $report->user_id,
    'report_id' => $report->id,
    'message' => 'Your report #X has been updated by Analyst Name',
]);
```

**2. Specific Escalation Notification** (NEW)
```php
Notification::create([
    'user_id' => $report->user_id,
    'report_id' => $report->id,
    'message' => 'Your report #X has been escalated externally by Analyst Name',
]);
```

### **Notification Display**

- **Location:** Top-right bell icon dropdown (existing notifications system)
- **Styling:** Uses the same notification list item styling already in place
- **Features:**
  - Icon and timestamp
  - Clickable to view the report
  - "Mark all read" functionality
  - Badge with unread count

### **User Experience Flow**

1. Analyst sets verdict to "Escalated Externally" and adds escalation note
2. Report submitter receives notification: "Your report #5 has been escalated externally by Jane Smith"
3. User clicks notification → taken to their report
4. Report shows Escalation Note in a dedicated section
5. Activity Log shows: "Jane Smith escalated this report externally: [note text]"

---

## 3. Action Assignment Notes in Notifications

### **Enhancement to Existing Feature**

**File Updated:** `app/Http/Controllers/ActionController.php`

**Implementation:**
When an action is assigned to a DIFFERENT analyst (not self-assigned), the notification now includes the notes:

```php
// Notify assigned user (only if assigned to someone else)
if ($assignedTo != Auth::id()) {
    $notificationMessage = 'Action assigned to you: ' . $validated['action_type'];
    
    // Include notes in notification if provided
    if (!empty($validated['notes'])) {
        $notificationMessage .= ' — ' . $validated['notes'];
    }
    
    $notificationMessage .= ' (Report #' . $report->id . ')';
    
    Notification::create([
        'user_id' => $assignedTo,
        'report_id' => $report->id,
        'message' => $notificationMessage,
    ]);
}
```

### **Notification Examples**

**Without Notes:**
```
Action assigned to you: Block URL (Report #12)
```

**With Notes:**
```
Action assigned to you: Block URL — Use firewall rules, check with network team first (Report #12)
```

### **Self-Assignment Behavior (Unchanged)**

When an analyst assigns an action to themselves using "Assign Action to Myself":
- ✅ Action is created with `assigned_to = current analyst's ID`
- ✅ Activity log shows: "Action assigned: Block URL (self-assigned)"
- ❌ NO notification is sent (no point notifying yourself)

---

## 4. Complete Notification Matrix

| Event | Recipient | Notification Message | Additional Info |
|-------|-----------|---------------------|-----------------|
| **Report Submitted** | All Analysts/Admins | "New threat report #X submitted by User Name" | Existing behavior |
| **Report Updated** | Report Submitter | "Your report #X has been updated by Analyst Name" | Existing behavior |
| **Report Escalated** | Report Submitter | "Your report #X has been escalated externally by Analyst Name" | **NEW - Specific message** |
| **Action Assigned (Self)** | Nobody | N/A | No notification sent |
| **Action Assigned (Other)** | Assigned Analyst | "Action assigned to you: {type} — {notes} (Report #X)" | **ENHANCED - Includes notes** |
| **Report Confirmed Threat** | Report Submitter | "Your report #X has been updated by Analyst Name" | Via generic update |

---

## 5. Activity Log Matrix

| Event | Log Entry | Who Sees It |
|-------|-----------|-------------|
| **Report Submitted** | "Threat report submitted" | Report submitter, Analysts, Admins |
| **Status Changed** | "Status changed from pending to under_review" | Everyone who can view report |
| **Verdict Changed** | "Verdict changed from pending to confirmed_threat" | Everyone who can view report |
| **Escalated Externally** | "{Analyst} escalated this report externally: {note}" | **NEW - Everyone who can view report** |
| **Added to Known Threats** | "Added to Known Threats: file_hash - abc123..." | Everyone who can view report |
| **Action Assigned (Self)** | "Action assigned: Block URL (self-assigned)" | Everyone who can view report |
| **Action Assigned (Other)** | "Action assigned: Block URL to John Doe" | Everyone who can view report |
| **Action Status Changed** | "Action status changed from pending to completed" | Everyone who can view report |

---

## 6. Testing Checklist

### **Test Case 1: Escalation Note Visibility**

**Steps:**
1. Login as Analyst
2. Open any report (e.g., Report #5)
3. Set Verdict to "Escalated Externally"
4. Fill in Escalation Note: "Forwarded to FBI Cyber Division"
5. Click "Update Report"

**Expected Results:**
- ✅ Report Details section shows "Escalation Note: Forwarded to FBI Cyber Division"
- ✅ Activity Log shows: "Analyst Name escalated this report externally: Forwarded to FBI Cyber Division"
- ✅ Notification created for report submitter

**Then:**
6. Logout and login as the report submitter (User)
7. View Report #5

**Expected Results:**
- ✅ Report Details section shows "Escalation Note" field with the text
- ✅ Activity Log shows the escalation entry
- ✅ Bell icon shows unread notification
- ✅ Notification says: "Your report #5 has been escalated externally by Analyst Name"

---

### **Test Case 2: Empty Escalation Note**

**Steps:**
1. Login as Analyst
2. Set verdict to "Escalated Externally" but leave note empty
3. Update report

**Expected Results:**
- ✅ NO "Escalation Note:" section appears in Report Details (hidden because empty)
- ✅ Activity Log shows: "Analyst Name escalated this report externally" (without colon and note)
- ✅ Notification still created with standard message

---

### **Test Case 3: Non-Escalated Report**

**Steps:**
1. View any report with verdict = "Confirmed Threat" or "False Positive"

**Expected Results:**
- ✅ NO "Escalation Note:" section visible (even if escalation_note field has old data)
- ✅ Only shows when verdict is specifically 'escalated_externally'

---

### **Test Case 4: Action Assignment with Notes**

**Prerequisites:** Need two analyst accounts to test "assign to other analyst"

**Steps:**
1. Login as Analyst A
2. View Report #10
3. Assign Action: Type = "Block URL", Notes = "Check with network team first", Assign To = Analyst B
4. Submit

**Expected Results:**
- ✅ Analyst B receives notification: "Action assigned to you: Block URL — Check with network team first (Report #10)"
- ✅ Activity Log shows: "Action assigned: Block URL to Analyst B"
- ✅ Analyst A does NOT receive a notification

---

### **Test Case 5: Self-Assignment (Confirm No Notification)**

**Steps:**
1. Login as Analyst
2. Assign Action to Myself (existing form)

**Expected Results:**
- ✅ Action created successfully
- ✅ Activity Log shows: "Action assigned: [type] (self-assigned)"
- ✅ NO notification sent to anyone
- ✅ No error or crash

---

## 7. Code Changes Summary

### **Controllers Modified**

1. **ReportQueueController.php** - Added escalation logging and notification
2. **ThreatReportController.php** - Added escalation logging and notification
3. **ActionController.php** - Enhanced notification to include notes

### **Views Modified**

1. **analyst/report-review.blade.php** - Added escalation note display
2. **reports/show.blade.php** - Added escalation note display

### **Database Schema (No Changes Required)**

Existing columns are sufficient:
- `threat_reports.escalation_note` - Already exists
- `threat_reports.verdict` - Already exists
- `activity_logs.action_description` - Already exists
- `notifications.message` - Already exists

---

## 8. User Interface Examples

### **Escalation Note in Report Details**

```
┌─────────────────────────────────────────┐
│ Report Details                          │
├─────────────────────────────────────────┤
│ Submitted By: John Doe (john@email.com) │
│ Submitted: Aug 31, 2026 14:30           │
│ Category: Phishing Email                │
│ Description:                            │
│ ┌─────────────────────────────────────┐ │
│ │ Suspicious email claiming to be     │ │
│ │ from IT department...               │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ Escalation Note:                        │
│ ┌─────────────────────────────────────┐ │
│ │ Forwarded to FBI Cyber Division for │ │
│ │ investigation. Case #2026-CYB-4521  │ │
│ └─────────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

### **Activity Log Entry**

```
┌─────────────────────────────────────────┐
│ Activity Log                            │
├─────────────────────────────────────────┤
│ [⚡] Jane Smith                         │
│     Jane Smith escalated this report   │
│     externally: Forwarded to FBI Cyber │
│     Division for investigation          │
│     Aug 31, 2026 · 14:35               │
│                                         │
│ [⚡] Jane Smith                         │
│     Verdict changed from pending to    │
│     escalated_externally               │
│     Aug 31, 2026 · 14:35               │
└─────────────────────────────────────────┘
```

### **Notification Dropdown**

```
┌─────────────────────────────────────────┐
│ 🔔 Notifications (2)      Mark all read │
├─────────────────────────────────────────┤
│ [📢] Your report #5 has been escalated │
│      externally by Jane Smith          │
│      2 minutes ago                     │
│                                         │
│ [📢] Action assigned to you: Block URL │
│      — Use firewall rules (Report #10) │
│      1 hour ago                        │
└─────────────────────────────────────────┘
```

---

## 9. Future Enhancements (Optional)

### **A) Email Notifications**

Add email notifications for escalations:
```php
Mail::to($report->user->email)->send(
    new ReportEscalatedMail($report, $escalationNote)
);
```

### **B) Escalation History**

Track multiple escalations if verdict changes back and forth:
```php
// Store in separate table
escalation_history:
  - report_id
  - analyst_id
  - escalated_at
  - escalation_note
  - external_case_id (optional)
```

### **C) Assign To Dropdown**

Add dropdown to assign actions to specific analysts:
```blade
<select name="assigned_to" class="form-select">
    <option value="">Myself</option>
    @foreach($analysts as $analyst)
        <option value="{{ $analyst->id }}">{{ $analyst->name }}</option>
    @endforeach
</select>
```

Then pass `$analysts` from the controller (filter users where role = 'analyst' or 'admin').

---

## 10. Backward Compatibility

✅ **All existing behavior preserved:**
- Generic update notifications still sent
- Activity logs for status/verdict changes still work
- Action assignment logging unchanged
- Self-assignment logic unchanged
- No database migrations required

✅ **New behavior is additive:**
- Escalation-specific logs/notifications are ADDED
- Nothing is removed or changed
- If escalation_note is empty, features gracefully degrade (hide the section)

---

## Conclusion

The escalation notes feature is now fully integrated into the application. Users will see:

1. **Escalation notes in Report Details** when viewing escalated reports
2. **Detailed activity log entries** showing who escalated and why
3. **Specific notifications** about escalations (not just generic updates)
4. **Action assignment notes** included in notifications

All features follow the existing UI patterns and styling, ensuring a consistent user experience.

**Status:** ✅ Ready for testing and deployment

---

**Implemented by:** Kiro AI  
**Review Status:** Complete  
**Documentation:** This file
