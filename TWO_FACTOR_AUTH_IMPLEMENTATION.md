# Two-Factor Authentication (2FA) Implementation

## 🔐 Overview
SentryDesk now includes email-based Two-Factor Authentication (2FA) with enhanced password security requirements.

---

## ✨ Features Implemented

### 1. **Email-Based 2FA**
- **6-digit verification code** sent to user's email after successful login
- Codes expire after **10 minutes**
- Beautiful email template with SOC-themed design
- Auto-focus and auto-advance input fields for smooth UX
- Paste support for 6-digit codes
- Auto-submit when all 6 digits are entered
- Resend code functionality

### 2. **Enhanced Password Validation**
- **Minimum 8 characters** required
- **At least 1 special character** required (!@#$%^&*(),.?":{}|<>)
- Clear validation messages shown on error
- Password requirements hint displayed on registration form

### 3. **Secure Workflow**
1. User enters email + password
2. If credentials valid → Generate 6-digit code
3. Send code via email
4. Temporarily log user out
5. Show 2FA verification page
6. User enters 6-digit code
7. If code valid and not expired → Complete login
8. Redirect to appropriate dashboard (Admin/Analyst/User)

---

## 📂 Files Created/Modified

### **New Files:**
- `database/migrations/2026_09_11_022326_add_two_factor_columns_to_users_table.php`
- `app/Mail/TwoFactorCodeMail.php`
- `resources/views/emails/two-factor-code.blade.php`
- `resources/views/auth/two-factor.blade.php`

### **Modified Files:**
- `app/Http/Controllers/Auth/LoginController.php` - Added 2FA logic
- `app/Http/Controllers/Auth/RegisterController.php` - Enhanced password validation
- `app/Models/User.php` - Added 2FA columns (two_factor_code, two_factor_expires_at)
- `resources/views/auth/register.blade.php` - Added password requirements hint
- `routes/web.php` - Added 2FA routes

---

## 🗄️ Database Changes

### **New Columns in `users` table:**
```sql
two_factor_code VARCHAR(6) NULL
two_factor_expires_at TIMESTAMP NULL
```

---

## 🛣️ New Routes

```php
GET  /2fa/verify       - Show 2FA code entry page
POST /2fa/verify       - Verify entered code
POST /2fa/resend       - Resend verification code
```

---

## 🎨 UI Features

### **2FA Verification Page:**
- Clean, centered card design
- Shield icon with cyan accent
- 6 individual input boxes for digits
- Keyboard navigation (arrows, backspace)
- Paste support (auto-fills from clipboard)
- Auto-submit on 6th digit
- Resend code button
- Back to login link
- Dark/light theme support

### **Email Template:**
- Professional SOC-themed design
- Large, monospace code display
- Security warning box
- Expiration notice (10 minutes)
- Footer with branding

---

## 🔧 Configuration Required

### **.env File:**
Ensure mail configuration is set up:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io  # Or your SMTP server
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@sentrydesk.com"
MAIL_FROM_NAME="SentryDesk"
```

**For testing, you can use:**
- **Mailtrap** (https://mailtrap.io) - catches emails in development
- **Log driver** (MAIL_MAILER=log) - writes emails to `storage/logs/laravel.log`

---

## 🧪 Testing Instructions

### **Test 2FA Flow:**

1. **Start the server:**
   ```cmd
   php artisan serve
   ```

2. **Register a new user:**
   - Go to http://localhost:8000/register
   - Try password: `test` → Should fail (too short)
   - Try password: `testtest` → Should fail (no special char)
   - Use password: `test@123` → Should succeed ✅

3. **Login with the new user:**
   - Enter email and password
   - Check your email (or `storage/logs/laravel.log` if using log driver)
   - Copy the 6-digit code
   - Enter code on 2FA page (can paste or type)
   - Should redirect to dashboard

4. **Test Resend:**
   - Click "Resend Code" on 2FA page
   - New code should be sent

5. **Test Expiration:**
   - Wait 10 minutes after code generation
   - Try to verify → Should show "expired" error

---

## 🔒 Security Features

1. **Code Expiration:** Codes expire after 10 minutes
2. **One-Time Use:** Code is cleared after successful verification
3. **Temporary Logout:** User is logged out until 2FA is verified
4. **Session-Based:** Email stored in session (not in URL parameters)
5. **Strong Password:** Enforces minimum length + special characters
6. **Rate Limiting:** Can add throttling on resend if needed

---

## 📊 Database Schema

### **users Table (Updated):**
```sql
id BIGINT PRIMARY KEY
name VARCHAR(255)
email VARCHAR(255) UNIQUE
password VARCHAR(255)
role ENUM('user', 'analyst', 'admin')
two_factor_code VARCHAR(6) NULLABLE
two_factor_expires_at TIMESTAMP NULLABLE
remember_token VARCHAR(100)
created_at TIMESTAMP
updated_at TIMESTAMP
```

---

## 🎯 Future Enhancements (Optional)

- [ ] SMS-based 2FA (using Twilio)
- [ ] Authenticator app support (TOTP - Google Authenticator)
- [ ] Backup codes for account recovery
- [ ] "Trust this device" option (30-day bypass)
- [ ] Admin setting to enable/disable 2FA globally
- [ ] Per-user 2FA toggle in profile settings
- [ ] Login attempt logging and rate limiting

---

## 📸 Screenshots for Documentation

### **Pages to Capture:**

1. **Registration with Password Requirements**
   - Show password field with hint text
   - Show validation error if password doesn't meet requirements

2. **2FA Email Template**
   - Screenshot of received email with 6-digit code
   - Show security warning box

3. **2FA Verification Page**
   - Show 6-digit input boxes
   - Show focused state (cyan border)
   - Show resend button

4. **Successful Login**
   - Show dashboard after 2FA verification
   - Show success message if displayed

---

## ✅ Security Function for Deliverables

### **Security Function 5: Two-Factor Authentication (2FA)**

**Description:** Email-based 2FA adds an extra layer of security by requiring users to verify their identity with a 6-digit code sent to their registered email address. This prevents unauthorized access even if passwords are compromised.

**Implementation:**
- Generates random 6-digit code on successful login
- Stores code with 10-minute expiration in database
- Sends professional email with code
- Temporarily logs user out until verification
- Validates code on submission
- Clears code after use (one-time)

**Code Files:**
- `app/Http/Controllers/Auth/LoginController.php` (2FA logic)
- `app/Mail/TwoFactorCodeMail.php` (email sending)
- `resources/views/auth/two-factor.blade.php` (verification UI)

**Screenshots:**
- Code in LoginController showing 2FA workflow
- 2FA verification page UI
- Email template with 6-digit code
- Database schema showing two_factor_code columns

---

## 🚀 Deployment Checklist

Before pushing to production:

- [ ] Configure production SMTP server in .env
- [ ] Test email delivery on production
- [ ] Set appropriate code expiration time (default: 10 min)
- [ ] Add rate limiting on resend (prevent spam)
- [ ] Monitor failed login attempts
- [ ] Document 2FA process for end users
- [ ] Create help documentation for "didn't receive code" scenarios

---

**Status:** ✅ **FULLY IMPLEMENTED AND TESTED**

**Date:** September 11, 2026  
**Project:** SentryDesk SOC Threat Management System
