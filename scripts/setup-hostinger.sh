#!/bin/bash
###############################################
# Thailand Together - Hostinger Initial Setup
# Run ONCE on first deployment
#
# Prerequisites:
#   1. SSH into Hostinger: ssh u123456789@your-server-ip -p 65002
#   2. Upload this script or copy/paste it
#   3. Run: bash setup-hostinger.sh
#
# Note: Hostinger disables symlink() function.
#       This script uses a bridge index.php instead.
###############################################

set -e

DOMAIN="pattayatogether.com"
SUBDOMAIN="platform"
APP_DIR="$HOME/pattayatogether"
PUBLIC_HTML="$HOME/domains/$DOMAIN/public_html/$SUBDOMAIN"

echo "================================================"
echo "  Thailand Together - Hostinger Setup"
echo "================================================"
echo ""

# ─── Step 1: Create application directory ───
echo "Step 1: Creating application directory..."
mkdir -p "$APP_DIR"
echo "   Done: $APP_DIR"

# ─── Step 2: Setup public_html with bridge index.php ───
echo "Step 2: Setting up public_html (bridge mode)..."

mkdir -p "$PUBLIC_HTML"

# Create bridge index.php that loads Laravel from APP_DIR
cat > "$PUBLIC_HTML/index.php" << 'PHPEOF'
<?php

define('LARAVEL_START', microtime(true));

// Bridge: Load Laravel from application directory
// Hostinger disables symlink(), so we use this bridge instead
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

echo "   Done: bridge index.php created"

# Create .htaccess for Laravel routing
cat > "$PUBLIC_HTML/.htaccess" << 'HTEOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:X-XSRF-Token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
HTEOF

echo "   Done: .htaccess created"

# ─── Step 3: Create .env file ───
echo "Step 3: Creating .env file..."

if [ ! -f "$APP_DIR/.env" ]; then
    cat > "$APP_DIR/.env" << 'ENVEOF'
APP_NAME="Thailand Together"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://platform.pattayatogether.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=CHANGE_ME
DB_USERNAME=CHANGE_ME
DB_PASSWORD=CHANGE_ME

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=platform.pattayatogether.com
SESSION_SECURE_COOKIE=true

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=info@pattayatogether.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="info@pattayatogether.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
ENVEOF

    echo "   Done: .env created"
    echo ""
    echo "   IMPORTANT: Edit .env with your database credentials!"
    echo "   Run: nano $APP_DIR/.env"
    echo ""
else
    echo "   .env already exists, skipping"
fi

# ─── Step 4: Create storage directories ───
echo "Step 4: Creating storage directories..."
mkdir -p "$APP_DIR/storage/logs"
mkdir -p "$APP_DIR/storage/framework/{sessions,views,cache/data}"
mkdir -p "$APP_DIR/storage/app/public"
mkdir -p "$APP_DIR/bootstrap/cache"
mkdir -p "$PUBLIC_HTML/storage"
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
echo "   Done: storage directories created"

# ─── Step 5: Check PHP version ───
echo "Step 5: Checking PHP version..."
PHP_VER=$(php -v | head -n 1)
echo "   $PHP_VER"

# ─── Step 6: Check Composer ───
echo "Step 6: Checking Composer..."
if command -v composer &> /dev/null; then
    COMP_VER=$(composer --version 2>/dev/null | head -n 1)
    echo "   $COMP_VER"
else
    echo "   Composer not found. Installing..."
    mkdir -p "$HOME/bin"
    curl -sS https://getcomposer.org/installer | php -- --install-dir="$HOME/bin" --filename=composer
    export PATH="$HOME/bin:$PATH"
    echo 'export PATH="$HOME/bin:$PATH"' >> "$HOME/.bashrc"
    echo "   Done: Composer installed"
fi

echo ""
echo "================================================"
echo "  Setup Complete!"
echo "================================================"
echo ""
echo "  Path info:"
echo "    App dir:    $APP_DIR"
echo "    Web root:   $PUBLIC_HTML"
echo "    URL:        https://platform.pattayatogether.com"
echo ""
echo "  Next steps:"
echo ""
echo "  1. Edit .env with your MySQL credentials:"
echo "     nano $APP_DIR/.env"
echo "     (Create database via hPanel -> Databases -> MySQL)"
echo ""
echo "  2. Add GitHub Secrets for auto-deploy:"
echo "     SSH_HOST     = Your Hostinger server IP"
echo "     SSH_USER     = Your SSH username (e.g., u123456789)"
echo "     SSH_PRIVATE_KEY = Your SSH private key"
echo "     SSH_PORT     = 65002 (Hostinger default)"
echo ""
echo "  3. Generate APP_KEY after first deploy:"
echo "     cd $APP_DIR && php artisan key:generate"
echo ""
echo "  4. Enable SSL via hPanel -> SSL -> Install"
echo ""
echo "  5. Push to main branch to trigger first deploy!"
echo ""
