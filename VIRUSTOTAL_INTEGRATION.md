# VirusTotal API Integration - Auto-Verify Feature

**Date:** August 31, 2026  
**Status:** ✅ IMPLEMENTED  
**Feature:** Automatic file hash verification using VirusTotal API

---

## Overview

Integrated VirusTotal API v3 to automatically verify file attachments submitted with threat reports. When a user submits a report with file attachments, the system asynchronously checks the file hash against VirusTotal's database and stores the scan results.

---

## Setup Instructions

### **1. Get VirusTotal API Key (FREE)**

1. Go to https://www.virustotal.com/gui/join-us
2. Sign up for a free account
3. Navigate to your profile → API Key
4. Copy your API key

**Free Tier Limits:**
- 4 requests per minute
- 500 requests per day
- Perfect for small-to-medium SOC operations

---

### **2. Configure Environment**

Add your API key to `.env`:

```env
VIRUSTOTAL_API_KEY=your_actual_api_key_here
```

**⚠️ IMPORTANT:** Replace `your_actual_api_key_here` with your real API key from VirusTotal!

---

### **3. Queue Configuration**

Your `.env` already has:
```env
QUEUE_CONNECTION=database
```

This means queue jobs will be stored in the `jobs` table (already migrated).

---

### **4. Start Queue Worker**

To process VirusTotal checks, you MUST run the queue worker:

**Option 1: Development (keeps running):**
```bash
php artisan queue:work
```

**Option 2: Process once and exit:**
```bash
php artisan queue:work --once
```

**Option 3: Production (with supervisor/systemd):**
```bash
php artisan queue:work --daemon
```

**📌 NOTE:** For local testing, open a SECOND terminal window and run `php artisan queue:work` while your Laravel server is running in the first window.

---

## How It Works

### **Flow Diagram**

```
User submits report with file
         ↓
File saved to storage/app/private/threat_attachments/
         ↓
Attachment record created with SHA256 hash
         ↓
CheckAttachmentWithVirusTotal job dispatched to queue
         ↓
Queue worker picks up job
         ↓
VirusTotalService calls API with file hash
         ↓
Scan result stored in ThreatReport.scan_result field
         ↓
Analyst sees result in Report Review page
```

---

## Implementation Details

### **Files Created**

1. **`app/Services/VirusTotalService.php`** - API integration service
2. **`app/Jobs/CheckAttachmentWithVirusTotal.php`** - Async queue job
3. **`VIRUSTOTAL_INTEGRATION.md`** - This documentation

### **Files Modified**

1. **`.env`** - Added `VIRUSTOTAL_API_KEY=`
2. **`.env.example`** - Added placeholder with instructions
3. **`config/services.php`** - Added VirusTotal configuration
4. **`app/Http/Controllers/ThreatReportController.php`** - Dispatches check job on upload
5. **`resources/views/analyst/report-review.blade.php`** - Shows scan result with helpful hints

---

## VirusTotal Service API

### **Method: `checkFileHash(string $hash): ?string`**

Checks a file hash against VirusTotal API v3.

**Parameters:**
- `$hash` - SHA256 hash of the file (64 characters)

**Returns:**
- `string` - Formatted scan result
- `null` - On API failure (network error, timeout, no API key)

**Possible Return Values:**

| Scenario | Return Value |
|----------|--------------|
| **Hash found, malicious** | `"VirusTotal: 15/70 vendors flagged as malicious"` |
| **Hash found, suspicious** | `"VirusTotal: 5/70 vendors flagged as malicious, 3 suspicious"` |
| **Hash found, clean** | `"VirusTotal: 0/70 vendors flagged as malicious"` |
| **Hash not in database** | `"VirusTotal: No existing record for this file (not yet scanned by any engine)"` |
| **Rate limit exceeded** | `"VirusTotal: Rate limit exceeded, scan pending"` |
| **API key missing** | `null` (logs warning) |
| **Network error** | `null` (logs warning) |
| **Timeout (>8 seconds)** | `null` (logs warning) |

---

## Queue Job Behavior

### **Job: `CheckAttachmentWithVirusTotal`**

**Trigger:** Automatically dispatched when attachment is created

**Process:**
1. Loads attachment by ID
2. Checks if `file_hash` exists (skips if empty)
3. Calls `VirusTotalService::checkFileHash()`
4. If result is `null`, logs warning and exits (doesn't update report)
5. If result is string, formats it with filename
6. Appends to `ThreatReport.scan_result` field
7. Saves report

**Multiple Attachments:**
If a report has multiple files, results are concatenated with line breaks:
```
[malware.exe] VirusTotal: 25/70 vendors flagged as malicious
[suspicious.pdf] VirusTotal: 0/70 vendors flagged as malicious
```

**Failure Handling:**
- If job fails, logs error but doesn't crash the system
- Report submission ALWAYS succeeds even if VirusTotal is down
- Analyst can manually add scan results if automatic check fails

---

## Frontend Display

### **Analyst Report Review Page**

**Scan Result Textarea:**
- Pre-filled with VirusTotal results when available
- Still editable by analyst (can add manual context)
- Shows helpful hint if empty:
  - With attachments: "Scan still processing or VirusTotal check pending..."
  - No attachments: "No file attached for automatic verification"

**Example Display:**

**With scan results:**
```
┌─────────────────────────────────────────────┐
│ Scan Result (Auto-Verify):                 │
│ ┌─────────────────────────────────────────┐ │
│ │ [ransomware.exe] VirusTotal: 45/70      │ │
│ │ vendors flagged as malicious, 5         │ │
│ │ suspicious                              │ │
│ └─────────────────────────────────────────┘ │
└─────────────────────────────────────────────┘
```

**Without scan results (with files):**
```
┌─────────────────────────────────────────────┐
│ Scan Result (Auto-Verify):                 │
│ ┌─────────────────────────────────────────┐ │
│ │ (empty)                                 │ │
│ └─────────────────────────────────────────┘ │
│ ℹ️ Scan still processing or VirusTotal     │
│   check pending...                          │
└─────────────────────────────────────────────┘
```

**Without scan results (no files):**
```
┌─────────────────────────────────────────────┐
│ Scan Result (Auto-Verify):                 │
│ ┌─────────────────────────────────────────┐ │
│ │ (empty)                                 │ │
│ └─────────────────────────────────────────┘ │
│ ℹ️ No file attached for automatic          │
│   verification                              │
└─────────────────────────────────────────────┘
```

---

## Testing Instructions

### **Test 1: Submit Report with Known-Clean File**

**Steps:**
1. Make sure `VIRUSTOTAL_API_KEY` is set in `.env`
2. Start queue worker: `php artisan queue:work`
3. Login as regular User
4. Submit new threat report with a clean text file (e.g., `test.txt`)
5. Wait a few seconds for queue to process
6. Login as Analyst
7. View the report in Report Queue

**Expected Result:**
- Scan Result field shows: `[test.txt] VirusTotal: 0/70 vendors flagged as malicious` (or similar with low count)

---

### **Test 2: Submit Report with Known Malicious File Hash**

**Note:** Don't actually upload malware! Use a known hash instead.

Use EICAR test file (harmless test file that antivirus treats as malware):
- Download from: https://www.eicar.org/download-anti-malware-testfile/
- File: `eicar.com`
- Hash: `275a021bbfb6489e54d471899f7db9d1663fc695ec2fe2a2c4538aabf651fd0f`

**Steps:**
1. Upload EICAR test file with a threat report
2. Wait for queue to process
3. View report as analyst

**Expected Result:**
- Scan Result shows high detection count: `[eicar.com] VirusTotal: 60+/70 vendors flagged as malicious`

---

### **Test 3: Submit Report Without Attachments**

**Steps:**
1. Submit threat report with NO files
2. View as analyst

**Expected Result:**
- Scan Result field is empty
- Hint shows: "No file attached for automatic verification"

---

### **Test 4: API Failure Handling (Invalid Key)**

**Steps:**
1. Temporarily change `.env` to invalid key: `VIRUSTOTAL_API_KEY=invalid_key_test`
2. Submit report with file
3. Wait for queue to process
4. Check Laravel logs: `storage/logs/laravel.log`

**Expected Results:**
- ✅ Report submission succeeds (doesn't crash)
- ✅ Scan Result field remains empty
- ✅ Log shows warning: "VirusTotal API returned error"
- ✅ No error shown to user

**Don't forget to restore your real API key after testing!**

---

### **Test 5: Queue Worker Not Running**

**Steps:**
1. Stop queue worker (Ctrl+C in queue terminal)
2. Submit report with file
3. Check `jobs` table in database

**Expected Results:**
- ✅ Report submission succeeds immediately
- ✅ Job is inserted into `jobs` table with `available_at` timestamp
- ✅ Scan Result field is empty with hint: "Scan still processing..."
- ✅ When you restart queue worker, job processes and result appears

**Verify in database:**
```sql
SELECT * FROM jobs ORDER BY id DESC LIMIT 1;
```

---

## Error Handling & Logging

### **Log Locations**

All VirusTotal activity is logged to `storage/logs/laravel.log`

### **Log Levels**

| Level | When | Example |
|-------|------|---------|
| **INFO** | Successful scan | `VirusTotal scan result saved` |
| **WARNING** | API key missing | `VirusTotal API key not configured` |
| **WARNING** | Rate limit hit | `VirusTotal rate limit exceeded` |
| **WARNING** | API error | `VirusTotal API returned error` |
| **WARNING** | Network timeout | `VirusTotal API call failed` |
| **ERROR** | Job failure | `VirusTotal check job failed` |

### **Sample Log Entries**

**Successful scan:**
```
[2026-08-31 14:30:22] local.INFO: VirusTotal scan result saved  
{"attachment_id":5,"report_id":12,"result":"VirusTotal: 0/70 vendors flagged as malicious"}
```

**Rate limit:**
```
[2026-08-31 14:30:45] local.WARNING: VirusTotal rate limit exceeded  
{"hash":"275a021bbfb6489e..."}
```

**Network error:**
```
[2026-08-31 14:31:10] local.WARNING: VirusTotal API call failed  
{"hash":"abc123def456...","error":"Connection timeout after 8 seconds"}
```

---

## Security & Privacy

### **API Key Protection**

✅ API key stored in `.env` (not committed to git)  
✅ `.env.example` has placeholder (no real key exposed)  
✅ Config uses `env()` helper (not hardcoded)  

### **File Privacy**

✅ Files stored in `storage/app/private/` (not publicly accessible)  
✅ Only file HASH sent to VirusTotal (not the actual file)  
✅ VirusTotal never receives your actual files  
✅ Hashes are one-way (cannot reconstruct file from hash)  

### **Rate Limiting**

✅ Queue system prevents hammering API (async, throttled)  
✅ Timeout set to 8 seconds (prevents hanging)  
✅ Graceful degradation on rate limit (logs warning, doesn't crash)  

---

## Troubleshooting

### **Problem: Scan Result Never Appears**

**Possible Causes:**

1. **Queue worker not running**
   - Solution: Run `php artisan queue:work` in separate terminal

2. **API key not set or invalid**
   - Solution: Check `.env` has `VIRUSTOTAL_API_KEY=your_actual_key`
   - Verify key at: https://www.virustotal.com/gui/user/YOUR_USERNAME/apikey

3. **File has no hash**
   - Solution: Check `attachments` table, `file_hash` column should not be NULL

4. **Job failed silently**
   - Solution: Check `failed_jobs` table:
     ```sql
     SELECT * FROM failed_jobs ORDER BY id DESC LIMIT 5;
     ```

5. **Network/firewall blocking VirusTotal**
   - Solution: Check Laravel logs for timeout errors
   - Verify you can reach VirusTotal: `curl https://www.virustotal.com/api/v3/`

---

### **Problem: "Rate limit exceeded" Message**

**Cause:** Free tier is 4 requests/minute, 500/day

**Solutions:**

1. **Immediate:** Wait 1 minute and retry
2. **Short-term:** Space out report submissions
3. **Long-term:** Upgrade to paid VirusTotal plan (if needed)

**Workaround:** Manually add scan results in the textarea

---

### **Problem: Queue Jobs Not Processing**

**Check queue status:**
```bash
php artisan queue:work --once
```

**Check jobs table:**
```sql
SELECT COUNT(*) FROM jobs;
```

**Clear failed jobs:**
```bash
php artisan queue:flush
```

**Restart queue worker:**
```bash
# Stop with Ctrl+C
# Restart
php artisan queue:work
```

---

## Production Deployment

### **1. Environment Variables**

Ensure production `.env` has:
```env
VIRUSTOTAL_API_KEY=your_production_key
QUEUE_CONNECTION=database
```

---

### **2. Queue Worker Setup (Supervisor)**

Create `/etc/supervisor/conf.d/sentrydesk-worker.conf`:

```ini
[program:sentrydesk-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/sentrydesk/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/sentrydesk/storage/logs/worker.log
stopwaitsecs=3600
```

**Reload supervisor:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start sentrydesk-worker:*
```

---

### **3. Monitor Queue**

**Check queue status:**
```bash
php artisan queue:monitor
```

**View failed jobs:**
```bash
php artisan queue:failed
```

**Retry failed jobs:**
```bash
php artisan queue:retry all
```

---

## API Reference

### **VirusTotal API v3 Endpoint**

```
GET https://www.virustotal.com/api/v3/files/{hash}
```

**Headers:**
```
x-apikey: your_api_key
Accept: application/json
```

**Response (Success):**
```json
{
  "data": {
    "attributes": {
      "last_analysis_stats": {
        "malicious": 15,
        "suspicious": 3,
        "harmless": 50,
        "undetected": 2
      }
    }
  }
}
```

**Response (Not Found):**
```json
{
  "error": {
    "code": "NotFoundError",
    "message": "File not found"
  }
}
```

---

## Future Enhancements (Optional)

### **1. Real-time Scanning**

If hash not in VirusTotal database, upload file for real-time scan:
```php
POST https://www.virustotal.com/api/v3/files
```

**Note:** Requires file upload, consumes more quota

---

### **2. Severity Auto-Detection**

Based on scan results, automatically set threat severity:
```php
if ($malicious == 0) $severity = 'low';
else if ($malicious <= 3) $severity = 'medium';
else if ($malicious <= 10) $severity = 'high';
else $severity = 'critical';
```

---

### **3. Notification on High Threat**

Send immediate notification to analysts if file is flagged by 10+ vendors:
```php
if ($malicious >= 10) {
    Notification::create([
        'message' => "CRITICAL: Report #{$report->id} contains highly malicious file!"
    ]);
}
```

---

### **4. URL Scanning**

Extract URLs from description field and scan them too:
```php
GET https://www.virustotal.com/api/v3/urls/{url_id}
```

---

## Summary

✅ **VirusTotal API integrated** - Automatic file hash verification  
✅ **Async queue processing** - Non-blocking, fast report submission  
✅ **Graceful error handling** - System never crashes on API failures  
✅ **Analyst-friendly display** - Results pre-filled, helpful hints shown  
✅ **Production-ready** - Logging, monitoring, supervisor setup  
✅ **Secure** - Only hashes sent, not files; API key protected  

---

**Status:** ✅ Ready for testing  
**Next Step:** Add your VirusTotal API key to `.env` and start queue worker  

**Get API Key:** https://www.virustotal.com/gui/join-us  
**Documentation:** https://developers.virustotal.com/reference/overview  

---

**Implemented by:** Kiro AI  
**Date:** August 31, 2026  
**Documentation:** This file
