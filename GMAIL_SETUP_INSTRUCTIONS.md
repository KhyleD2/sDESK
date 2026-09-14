# 📧 Gmail SMTP Setup for SentryDesk 2FA

## ⚠️ IMPORTANT: You Need to Complete These Steps

I've configured the `.env` file to use Gmail SMTP, but you need to add your credentials.

---

## 🔐 Step 1: Get Gmail App Password (Required)

Gmail requires an "App Password" for security. Here's how:

### **A. Enable 2-Step Verification on Your Gmail**
1. Go to: https://myaccount.google.com/security
2. Click **"2-Step Verification"**
3. Follow steps to enable it (if not already enabled)

### **B. Generate App Password**
1. Go to: https://myaccount.google.com/apppasswords
2. Sign in if prompted
3. Select app: **Mail**
4. Select device: **Windows Computer**
5. Click **"Generate"**
6. Copy the 16-character password (example: `abcd efgh ijkl mnop`)

---

## 📝 Step 2: Update .env File

Open `d:\sentrydesk\.env` and replace these lines:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com           👈 CHANGE THIS
MAIL_PASSWORD=your-app-password-here         👈 CHANGE THIS
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@sentrydesk.com"
MAIL_FROM_NAME="SentryDesk"
```

**Replace:**
- `your-gmail@gmail.com` → Your actual Gmail address
- `your-app-password-here` → The 16-character app password (no spaces!)

**Example:**
```env
MAIL_USERNAME=khyled2@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
```

---

## ✅ Step 3: Clear Config Cache

After updating `.env`, run:

```cmd
php artisan config:clear
```

---

## 🧪 Step 4: Test It

1. **Start server:**
   ```cmd
   php artisan serve
   ```

2. **Register/Login with your Gmail:**
   - Go to: http://localhost:8000/register
   - Use YOUR Gmail as the email
   - Password: `test@123` (or any password with 8+ chars and a special character)

3. **Check your Gmail inbox:**
   - You should receive a beautiful email with a 6-digit code!
   - Enter the code to complete login

---

## 🎯 Quick Setup (Copy-Paste Ready)

1. Get your Gmail app password from: https://myaccount.google.com/apppasswords

2. Open `.env` and update:
   ```env
   MAIL_USERNAME=YOUR_GMAIL_HERE
   MAIL_PASSWORD=YOUR_APP_PASSWORD_HERE
   ```

3. Run:
   ```cmd
   php artisan config:clear
   php artisan serve
   ```

4. Test login - check your Gmail!

---

## 🐛 Troubleshooting

### **"Invalid credentials" error:**
- Make sure you're using the **App Password**, NOT your regular Gmail password
- Remove any spaces from the app password
- Make sure 2-Step Verification is enabled on your Gmail

### **No email received:**
- Check Spam/Junk folder
- Verify `MAIL_USERNAME` is correct
- Run `php artisan config:clear` again

### **"Connection timeout":**
- Check your internet connection
- Make sure port 587 is not blocked by firewall
- Try `MAIL_PORT=465` and `MAIL_ENCRYPTION=ssl` as alternative

### **Still not working?**
Use Mailtrap instead (easier for testing):
1. Sign up at https://mailtrap.io (free)
2. Get SMTP credentials from your inbox
3. Update `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_mailtrap_username
   MAIL_PASSWORD=your_mailtrap_password
   ```

---

## 📸 For Tomorrow's Presentation

Once emails are working:

1. **Register with your real Gmail**
2. **Login and trigger 2FA**
3. **Screenshot the email you receive** (show the beautiful template!)
4. **Screenshot the 2FA verification page**
5. **Show successful login**

This demonstrates real email sending and professional 2FA implementation!

---

## ⚙️ Current Configuration

```env
✅ MAIL_MAILER=smtp (using Gmail SMTP)
✅ MAIL_HOST=smtp.gmail.com
✅ MAIL_PORT=587 (TLS)
✅ MAIL_ENCRYPTION=tls
✅ Professional sender name: "SentryDesk"
✅ Beautiful HTML email template
```

**What you need to add:**
- ❌ MAIL_USERNAME (your Gmail)
- ❌ MAIL_PASSWORD (your app password)

---

## 🚀 After Setup

Once configured, the system will:
1. ✅ Send 6-digit codes to registered email addresses
2. ✅ Use professional "SentryDesk" branding
3. ✅ Beautiful HTML email template
4. ✅ Works with ANY Gmail address users register with
5. ✅ 10-minute code expiration
6. ✅ One-time use codes

---

**Ready? Get your app password and update `.env` now!** 🎉
