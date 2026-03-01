# Thailand Together - Deployment Guide

## Architecture Overview

```
GitHub (push to main)
    │
    ▼
GitHub Actions CI/CD
    │
    ├── 1. Run Tests
    ├── 2. Install PHP deps (--no-dev)
    ├── 3. Build frontend (npm run build)
    ├── 4. Rsync to Hostinger
    └── 5. SSH: migrate + cache + copy assets
            │
            ▼
Hostinger Cloud Hosting
    ~/pattayatogether/          ← Laravel app
    ~/domains/pattayatogether.com/
        public_html/platform/   ← Web root (bridge index.php)
```

> **Note:** Hostinger disables `symlink()`. Instead of symlinking `public_html`,
> we use a bridge `index.php` that loads Laravel from `~/pattayatogether/`.
> Static assets (Vite build, uploads) are copied to `public_html/platform/`.

---

## First-Time Setup

### Step 1: Prepare Hostinger

1. **Login to hPanel** → pattayatogether.com

2. **Create MySQL Database** via hPanel → Databases:
   - Database name: `u504097778_platform`
   - Username: `u504097778_platform`
   - Password: (your password)
   - Note these credentials for .env

3. **Enable SSH Access** via hPanel → Advanced → SSH Access

4. **Set PHP Version** to 8.2+ via hPanel → Advanced → PHP Configuration

### Step 2: SSH Setup

```bash
# Generate SSH key (on your local machine)
ssh-keygen -t ed25519 -C "deploy@pattayatogether" -f ~/.ssh/hostinger_deploy

# Copy public key to Hostinger
# hPanel → Advanced → SSH Keys → Add New
cat ~/.ssh/hostinger_deploy.pub
# Paste the output into hPanel

# Test connection
ssh -i ~/.ssh/hostinger_deploy -p 65002 u504097778@46.202.187.217
```

### Step 3: Run Initial Setup on Server

```bash
# SSH into Hostinger
ssh -i ~/.ssh/hostinger_deploy -p 65002 u504097778@46.202.187.217

# Create the setup script (or upload it)
nano ~/setup-hostinger.sh
# Paste contents of scripts/setup-hostinger.sh
bash ~/setup-hostinger.sh
```

### Step 4: Configure .env on Server

```bash
nano ~/pattayatogether/.env

# Fill in your MySQL credentials:
# DB_DATABASE=u504097778_platform
# DB_USERNAME=u504097778_platform
# DB_PASSWORD=your_password
```

### Step 5: Add GitHub Secrets

Go to your GitHub repo → Settings → Secrets and variables → Actions:

| Secret Name       | Value                              | Description              |
| ----------------- | ---------------------------------- | ------------------------ |
| `SSH_HOST`        | `46.202.187.217`                   | Hostinger server IP      |
| `SSH_USER`        | `u504097778`                       | SSH username from hPanel |
| `SSH_PRIVATE_KEY` | contents of `hostinger_deploy` key | Private key (not .pub)   |
| `SSH_PORT`        | `65002`                            | Hostinger SSH port       |

```bash
# Get your private key content:
cat ~/.ssh/hostinger_deploy
# Copy the ENTIRE output including -----BEGIN/END-----
```

### Step 6: Enable SSL

hPanel → SSL → Install free SSL certificate for platform.pattayatogether.com

### Step 7: First Deploy

```bash
# Push to main branch to trigger auto-deploy
git checkout main
git push origin main
```

After first deploy, SSH in and generate the app key:

```bash
ssh -i ~/.ssh/hostinger_deploy -p 65002 u504097778@46.202.187.217
cd ~/pattayatogether
php artisan key:generate
php artisan migrate --force
```

---

## How Auto-Deploy Works

Every time you push to the `main` branch:

1. **GitHub Actions** triggers automatically
2. **Tests** run first — if they fail, deploy is cancelled
3. **Composer install** (production, no dev dependencies)
4. **npm build** (Vite compiles frontend assets)
5. **Rsync** transfers files to Hostinger (excluding .env, logs, sessions)
6. **Post-deploy** runs on server:
   - Database migrations
   - Config/route/view caching
   - Copy assets to public_html (build/, storage/, favicon, etc.)
   - Ensure bridge index.php and .htaccess exist
   - Queue worker restart

### What's preserved on server (never overwritten):
- `.env` (your credentials)
- `storage/logs/` (application logs)
- `storage/framework/sessions/` (user sessions)
- `storage/app/public/` (uploaded files)
- `public_html/platform/index.php` (bridge file)
- `public_html/platform/.htaccess`

---

## Bridge Architecture (No Symlink)

Hostinger disables `symlink()`, so we use a bridge approach:

```
Request → https://platform.pattayatogether.com
    │
    ▼
~/domains/pattayatogether.com/public_html/platform/
    ├── index.php          ← Bridge: loads Laravel from ~/pattayatogether
    ├── .htaccess          ← Routes all requests to index.php
    ├── build/             ← Vite compiled assets (copied after deploy)
    └── storage/           ← Uploaded files (copied after deploy)
    │
    ▼ (index.php loads)
~/pattayatogether/
    ├── vendor/autoload.php
    ├── bootstrap/app.php
    └── (full Laravel app)
```

Static assets (CSS, JS, images) must be copied to `public_html/platform/`
after each deploy since they need to be served directly by Apache.

---

## Manual Deploy

If you need to deploy manually (without pushing to GitHub):

```bash
# SSH into server
ssh -i ~/.ssh/hostinger_deploy -p 65002 u504097778@46.202.187.217

# Run deploy script
bash ~/pattayatogether/scripts/deploy-server.sh
```

---

## Monitoring & Troubleshooting

### Check if site is up
```bash
curl -I https://platform.pattayatogether.com
```

### View logs on server
```bash
ssh -i ~/.ssh/hostinger_deploy -p 65002 u504097778@46.202.187.217
tail -50 ~/pattayatogether/storage/logs/laravel.log
```

### Clear all caches
```bash
cd ~/pattayatogether
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Maintenance mode
```bash
# Enable
php artisan down --secret="your-secret-token"

# Disable
php artisan up
```

### Rollback migration
```bash
php artisan migrate:rollback --step=1
```

### Check GitHub Actions deploy status
```bash
# From your local machine
gh run list --workflow=deploy.yml
gh run view <run-id>
```

---

## Cron Jobs (Optional)

If your app uses Laravel's task scheduler, set up cron on Hostinger:

hPanel → Advanced → Cron Jobs:

```
* * * * * cd ~/pattayatogether && php artisan schedule:run >> /dev/null 2>&1
```

---

## Directory Structure on Hostinger

```
/home/u504097778/
├── domains/
│   └── pattayatogether.com/
│       └── public_html/
│           └── platform/              ← Web root for subdomain
│               ├── index.php          ← Bridge (loads Laravel)
│               ├── .htaccess          ← Apache rewrite rules
│               ├── build/             ← Vite assets (copied)
│               └── storage/           ← Uploaded files (copied)
│
├── pattayatogether/                   ← Laravel application
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/                        ← Laravel public dir (not web root)
│   │   └── build/                     ← Vite compiled assets
│   ├── resources/
│   ├── routes/
│   ├── scripts/
│   ├── storage/
│   │   ├── app/public/                ← User uploads
│   │   ├── framework/
│   │   └── logs/
│   ├── vendor/
│   └── .env                           ← Production config (NOT in git)
│
└── .ssh/                              ← SSH keys
```
