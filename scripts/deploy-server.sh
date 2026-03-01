#!/bin/bash
###############################################
# Thailand Together - Server Deploy Script
# Run on Hostinger after code is synced
#
# Usage: bash ~/thailandtogether/scripts/deploy-server.sh
###############################################

set -e

APP_DIR="$HOME/thailandtogether"
DOMAIN="thailandtogether.net"
PUBLIC_HTML="$HOME/domains/$DOMAIN/public_html"

echo "🚀 Starting deployment..."
cd "$APP_DIR"

# ─── Ensure storage directories ───
echo "📁 Checking directory structure..."
mkdir -p storage/logs
mkdir -p storage/framework/{sessions,views,cache/data}
mkdir -p storage/app/public
mkdir -p bootstrap/cache

# ─── Permissions ───
echo "🔒 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# ─── Composer (if vendor not synced) ───
if [ ! -d "vendor" ] || [ "$1" = "--install" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# ─── Database Migration ───
echo "🗄️  Running migrations..."
php artisan migrate --force

# ─── Cache optimization ───
echo "⚡ Optimizing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# ─── Storage link ───
# Create symlink in public_html for storage
if [ ! -L "$PUBLIC_HTML/storage" ]; then
    echo "🔗 Creating storage symlink..."
    ln -sf "$APP_DIR/storage/app/public" "$PUBLIC_HTML/storage"
fi

# ─── Restart queue workers ───
php artisan queue:restart 2>/dev/null || true

echo ""
echo "✅ Deployment completed successfully!"
echo "🌐 Site: https://$DOMAIN"
