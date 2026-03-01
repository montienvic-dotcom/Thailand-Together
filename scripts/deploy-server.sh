#!/bin/bash
###############################################
# Thailand Together - Server Deploy Script
# Run on Hostinger after code is synced
#
# Usage: bash ~/pattayatogether/scripts/deploy-server.sh
#
# Note: Hostinger disables symlink() function.
#       Uses bridge index.php + file copy instead.
###############################################

set -e

APP_DIR="$HOME/pattayatogether"
DOMAIN="pattayatogether.com"
SUBDOMAIN="platform"
PUBLIC_HTML="$HOME/domains/$DOMAIN/public_html/$SUBDOMAIN"

echo "Starting deployment..."
cd "$APP_DIR"

# ─── Ensure storage directories ───
echo "Checking directory structure..."
mkdir -p storage/logs
mkdir -p storage/framework/{sessions,views,cache/data}
mkdir -p storage/app/public
mkdir -p bootstrap/cache

# ─── Permissions ───
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache

# ─── Composer (if vendor not synced) ───
if [ ! -d "vendor" ] || [ "$1" = "--install" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# ─── Database Migration ───
echo "Running migrations..."
php artisan migrate --force

# ─── Cache optimization ───
echo "Optimizing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# ─── Copy static assets to public_html ───
echo "Copying assets to web root..."
mkdir -p "$PUBLIC_HTML"

# Copy Vite build assets
if [ -d "$APP_DIR/public/build" ]; then
    cp -r "$APP_DIR/public/build" "$PUBLIC_HTML/build"
    echo "   Done: build/ assets copied"
fi

# Copy storage files (uploaded files)
if [ -d "$APP_DIR/storage/app/public" ]; then
    mkdir -p "$PUBLIC_HTML/storage"
    cp -r "$APP_DIR/storage/app/public/." "$PUBLIC_HTML/storage/"
    echo "   Done: storage/ files copied"
fi

# Copy other public assets (favicon, robots.txt, etc.)
for asset in favicon.ico robots.txt; do
    if [ -f "$APP_DIR/public/$asset" ]; then
        cp "$APP_DIR/public/$asset" "$PUBLIC_HTML/$asset"
    fi
done

# Ensure bridge index.php exists
if [ ! -f "$PUBLIC_HTML/index.php" ]; then
    echo "   Creating bridge index.php..."
    cat > "$PUBLIC_HTML/index.php" << 'PHPEOF'
<?php

define('LARAVEL_START', microtime(true));

$appDir = getenv('HOME') . '/pattayatogether';

require $appDir . '/vendor/autoload.php';

$app = require_once $appDir . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
PHPEOF
fi

# Ensure .htaccess exists
if [ ! -f "$PUBLIC_HTML/.htaccess" ]; then
    echo "   Creating .htaccess..."
    cat > "$PUBLIC_HTML/.htaccess" << 'HTEOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{HTTP:X-XSRF-Token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
HTEOF
fi

# ─── Restart queue workers ───
php artisan queue:restart 2>/dev/null || true

echo ""
echo "Deployment completed successfully!"
echo "Site: https://$SUBDOMAIN.$DOMAIN"
