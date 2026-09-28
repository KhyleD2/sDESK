# Railway Deployment - Quick Start 🚀

## ✅ Your Code is Ready for Deployment!

### What's Been Prepared:
- ✅ Database seeder with your admin account
- ✅ Procfile for Railway
- ✅ nixpacks.toml configuration
- ✅ All code pushed to GitHub

---

## 🎯 Next Steps in Railway:

### 1. Go to Railway.app
Visit: **https://railway.app**

### 2. Create Project from GitHub
- Click "New Project"
- Select "Deploy from GitHub repo"
- Choose: **KhyleD2/sDESK**

### 3. Add MySQL Database
- Click "+ New"
- Select "Database" → "MySQL"
- Railway auto-configures it

### 4. Set Environment Variables

In your Laravel service → Variables tab:

```env
APP_NAME=SentryDesk
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_KEY_HERE
APP_URL=https://your-app.up.railway.app

DB_CONNECTION=mysql
DB_HOST=${MYSQL_HOST}
DB_PORT=${MYSQL_PORT}
DB_DATABASE=${MYSQL_DATABASE}
DB_USERNAME=${MYSQL_USER}
DB_PASSWORD=${MYSQL_PASSWORD}

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=log

VIRUSTOTAL_API_KEY=your_api_key_here

FILESYSTEM_DISK=local
```

**Generate APP_KEY locally:**
```bash
php artisan key:generate --show
```

### 5. Deploy!
Railway will automatically deploy. Wait 3-5 minutes.

---

## 🔑 Login Credentials (Auto-Created)

When deployment finishes, 3 accounts will be automatically created:

### Admin Account (Your Account):
- **Email**: `khyle.drey@gmail.com`
- **Password**: `admin123`
- **Role**: Admin

### Analyst Account:
- **Email**: `analyst@sentrydesk.local`
- **Password**: `analyst123`
- **Role**: Analyst

### User Account:
- **Email**: `user@sentrydesk.local`
- **Password**: `user123`
- **Role**: User

---

## ⚡ What Happens on Deploy:

1. Railway pulls your code from GitHub
2. Installs dependencies (`composer install`)
3. Runs migrations (`php artisan migrate --force`)
4. **Seeds database** (`php artisan db:seed --force`) ← Creates your accounts!
5. Links storage (`php artisan storage:link`)
6. Starts server

---

## 🎓 For Your Presentation:

### You Have 2 Options:

#### Option 1: Local Demo (Recommended for Tomorrow)
```bash
php artisan serve
```
- ✅ FREE
- ✅ No issues
- ✅ Already working
- ✅ Full control

#### Option 2: Railway Deployment (After Presentation)
- ✅ Online access
- ✅ Shareable link
- ⏱️ Takes 10-15 minutes
- 💰 Free for first month ($5 credit)

---

## 📝 Railway Deployment Checklist:

Before deploying:
- [x] Code pushed to GitHub ✅
- [x] Seeder created ✅
- [x] Procfile configured ✅
- [x] nixpacks.toml configured ✅
- [ ] Railway account created
- [ ] Project deployed
- [ ] MySQL database added
- [ ] Environment variables set
- [ ] APP_KEY generated
- [ ] Deployment successful
- [ ] Login tested

---

## 🆘 If You Deploy and Can't Login:

The seeder should auto-create your account, but if it doesn't:

1. **Check Railway Logs**:
   - Look for "Seeding complete!" message
   - Check for any database errors

2. **Manually Create Account**:
   Via Railway CLI:
   ```bash
   railway login
   railway link
   railway run php artisan tinker
   ```
   
   Then in tinker:
   ```php
   User::create([
       'name' => 'Khyle Drey',
       'email' => 'khyle.drey@gmail.com',
       'password' => Hash::make('admin123'),
       'role' => 'admin',
       'email_verified_at' => now()
   ]);
   ```

3. **Or Run Seeder Manually**:
   ```bash
   railway run php artisan db:seed --force
   ```

---

## ⚠️ Important Notes:

### For Tomorrow's Presentation:
- **Use local demo** (`php artisan serve`)
- It's safer, faster, and you have full control
- Deploy to Railway **after** presentation if needed

### About Passwords:
- Change `admin123` to something secure after deployment!
- In Railway Variables, you can set:
  ```env
  ADMIN_PASSWORD=your_secure_password
  ```

### About File Uploads:
- Railway filesystem is ephemeral (resets on redeploy)
- For production, use S3/Cloudinary
- For demo, local storage works fine

---

## 🎉 You're Ready!

### Your Options:
1. **Tomorrow**: Demo locally ✅ (Recommended)
2. **After presentation**: Deploy to Railway in 10 minutes

### What You Have:
- ✅ Complete working application
- ✅ Code on GitHub
- ✅ Deployment files ready
- ✅ Auto-seeder for accounts
- ✅ Documentation

---

**Good luck with your presentation! 🚀**

Need help deploying? Follow DEPLOYMENT_GUIDE.md for step-by-step instructions.
