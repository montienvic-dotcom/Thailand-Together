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
    └── 5. SSH: migrate + cache
            │
            ▼
Hostinger Cloud Hosting
    ~/thailandtogether/          ← Laravel app
    ~/domains/thailandtogether.net/
        public_html/ → ~/thailandtogether/public/  (symlink)
```

---

## First-Time Setup (ทำครั้งเดียว)

### Step 1: Prepare Hostinger

1. **Login to hPanel** → thailandtogether.net

2. **Create MySQL Database** via hPanel → Databases:
   - Database name: `u1234_thailand` (example)
   - Username: `u1234_admin` (example)
   - Password: (generate strong password)
   - Note these credentials for .env

3. **Enable SSH Access** via hPanel → Advanced → SSH Access

4. **Set PHP Version** to 8.2+ via hPanel → Advanced → PHP Configuration

### Step 2: SSH Setup

```bash
# Generate SSH key (on your local machine)
ssh-keygen -t ed25519 -C "deploy@thailandtogether" -f ~/.ssh/hostinger_deploy

# Copy public key to Hostinger
# hPanel → Advanced → SSH Keys → Add New
cat ~/.ssh/hostinger_deploy.pub
# Paste the output into hPanel

# Test connection
ssh -p 65002 u123456789@your-server-ip
```

### Step 3: Run Initial Setup on Server

```bash
# SSH into Hostinger
ssh -p 65002 u123456789@your-server-ip

# Create the setup script (or upload it)
mkdir -p ~/thailandtogether
# Copy scripts/setup-hostinger.sh content and run it
bash setup-hostinger.sh
```

### Step 4: Configure .env on Server

```bash
ssh -p 65002 u123456789@your-server-ip
nano ~/thailandtogether/.env

# Fill in your MySQL credentials:
# DB_DATABASE=u1234_thailand
# DB_USERNAME=u1234_admin
# DB_PASSWORD=your_password
```

### Step 5: Add GitHub Secrets

Go to your GitHub repo → Settings → Secrets and variables → Actions:

| Secret Name       | Value                              | Description              |
| ----------------- | ---------------------------------- | ------------------------ |
| `SSH_HOST`        | `your-server-ip`                   | Hostinger server IP      |
| `SSH_USER`        | `u123456789`                       | SSH username from hPanel |
| `SSH_PRIVATE_KEY` | contents of `hostinger_deploy` key | Private key (not .pub)   |
| `SSH_PORT`        | `65002`                            | Hostinger SSH port       |

```bash
# Get your private key content:
cat ~/.ssh/hostinger_deploy
# Copy the ENTIRE output including -----BEGIN/END-----
```

### Step 6: Enable SSL

hPanel → SSL → Install free SSL certificate for thailandtogether.net

### Step 7: First Deploy

```bash
# Push to main branch to trigger auto-deploy
git checkout main
git push origin main
```

After first deploy, SSH in and generate the app key:

```bash
ssh -p 65002 u123456789@your-server-ip
cd ~/thailandtogether
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
   - Queue worker restart

### What's preserved on server (never overwritten):
- `.env` (your credentials)
- `storage/logs/` (application logs)
- `storage/framework/sessions/` (user sessions)
- `storage/app/public/` (uploaded files)
- `database/database.sqlite` (if used)

---

## Manual Deploy

If you need to deploy manually (without pushing to GitHub):

```bash
# SSH into server
ssh -p 65002 u123456789@your-server-ip

# Run deploy script
bash ~/thailandtogether/scripts/deploy-server.sh
```

---

## Monitoring & Troubleshooting

### Check if site is up
```bash
curl -I https://thailandtogether.net
```

### View logs on server
```bash
ssh -p 65002 u123456789@your-server-ip
tail -50 ~/thailandtogether/storage/logs/laravel.log
```

### Clear all caches
```bash
ssh -p 65002 u123456789@your-server-ip
cd ~/thailandtogether
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
* * * * * cd ~/thailandtogether && php artisan schedule:run >> /dev/null 2>&1
```

---

## Directory Structure on Hostinger

```
/home/u123456789/
├── domains/
│   └── thailandtogether.net/
│       └── public_html/ → ~/thailandtogether/public  (symlink)
│
├── thailandtogether/            ← Laravel application
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/                  ← Served via public_html symlink
│   │   ├── build/               ← Vite compiled assets
│   │   ├── storage/ → ../../storage/app/public
│   │   ├── index.php
│   │   └── .htaccess
│   ├── resources/
│   ├── routes/
│   ├── scripts/
│   ├── storage/
│   │   ├── app/public/          ← User uploads
│   │   ├── framework/
│   │   └── logs/
│   ├── vendor/
│   └── .env                     ← Production config (NOT in git)
│
└── .ssh/                        ← SSH keys
```
