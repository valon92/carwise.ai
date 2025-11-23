#!/bin/bash

# CarWise.ai - Create cPanel Upload Package
# Script për të krijuar paketën për upload në cPanel

echo "📁 CarWise.ai - Creating cPanel Upload Package"
echo "=============================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Create cPanel upload directory
CPANEL_DIR="carwise-cpanel-upload-$(date +%Y%m%d-%H%M%S)"
print_status "Duke krijuar cPanel upload directory: $CPANEL_DIR"
mkdir -p "$CPANEL_DIR"

# Build assets first
print_status "Duke ndërtuar frontend assets..."
npm run build
if [ $? -ne 0 ]; then
    print_error "Gabim në ndërtimin e assets!"
    exit 1
fi

# Copy application files (excluding development files)
print_status "Duke kopjuar application files për cPanel..."

# Core Laravel files
cp -r app "$CPANEL_DIR/"
cp -r bootstrap "$CPANEL_DIR/"
cp -r config "$CPANEL_DIR/"
cp -r database "$CPANEL_DIR/"
cp -r resources "$CPANEL_DIR/"
cp -r routes "$CPANEL_DIR/"

# Copy public folder contents to root (cPanel requirement)
print_status "Duke kopjuar public folder contents në root..."
cp -r public/* "$CPANEL_DIR/"

# Copy essential files
cp artisan "$CPANEL_DIR/"
cp composer.json "$CPANEL_DIR/"
cp composer.lock "$CPANEL_DIR/"

# Copy vendor folder (if exists)
if [ -d "vendor" ]; then
    print_status "Duke kopjuar vendor folder..."
    cp -r vendor "$CPANEL_DIR/"
else
    print_warning "Vendor folder nuk ekziston. Do të duhet të instaloni dependencies në server."
fi

# Create storage directories
print_status "Duke krijuar storage directories..."
mkdir -p "$CPANEL_DIR/storage/app"
mkdir -p "$CPANEL_DIR/storage/framework/cache"
mkdir -p "$CPANEL_DIR/storage/framework/sessions"
mkdir -p "$CPANEL_DIR/storage/framework/views"
mkdir -p "$CPANEL_DIR/storage/logs"
mkdir -p "$CPANEL_DIR/bootstrap/cache"

# Create .htaccess for cPanel
print_status "Duke krijuar .htaccess për cPanel..."
cat > "$CPANEL_DIR/.htaccess" << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Cache Control
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
</IfModule>

# Gzip Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
EOF

# Create cPanel specific .env
print_status "Duke krijuar .env për cPanel..."
cat > "$CPANEL_DIR/.env" << 'EOF'
# CarWise.ai - cPanel Configuration
# Konfigurimi për shared hosting

# Application
APP_NAME="CarWise.ai"
APP_ENV=production
APP_KEY=base64:CHANGE_THIS_TO_YOUR_APP_KEY
APP_DEBUG=false
APP_URL=https://carwise.ai
APP_TIMEZONE=Europe/Tirana

# Database (cPanel MySQL)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=CHANGE_THIS_TO_YOUR_DB_NAME
DB_USERNAME=CHANGE_THIS_TO_YOUR_DB_USER
DB_PASSWORD=CHANGE_THIS_TO_YOUR_DB_PASSWORD

# Cache & Session (File-based for shared hosting)
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_CONNECTION=sync

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mail.carwise.ai
MAIL_PORT=587
MAIL_USERNAME=noreply@carwise.ai
MAIL_PASSWORD=CHANGE_THIS_TO_YOUR_MAIL_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@carwise.ai
MAIL_FROM_NAME="CarWise.ai"

# AI Providers (at least one required)
DEFAULT_AI_PROVIDER=openai
AI_FALLBACK_ENABLED=true
MAX_AI_COST_PER_DIAGNOSIS=0.50

# OpenAI
OPENAI_API_KEY=CHANGE_THIS_TO_OPENAI_KEY
OPENAI_MODEL=gpt-3.5-turbo
OPENAI_MAX_TOKENS=2000
OPENAI_TEMPERATURE=0.3

# Claude
CLAUDE_API_KEY=CHANGE_THIS_TO_CLAUDE_KEY
CLAUDE_MODEL=claude-3-sonnet-20240229
CLAUDE_MAX_TOKENS=2000
CLAUDE_TEMPERATURE=0.3

# Gemini
GEMINI_API_KEY=CHANGE_THIS_TO_GEMINI_KEY
GEMINI_MODEL=gemini-1.5-flash
GEMINI_MAX_TOKENS=2000
GEMINI_TEMPERATURE=0.3

# File Storage
FILESYSTEM_DISK=local

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Broadcasting
BROADCAST_DRIVER=log

# Vite
VITE_APP_NAME="${APP_NAME}"

# Security
BCRYPT_ROUNDS=12

# Rate Limiting
RATE_LIMIT_PER_MINUTE=60
API_RATE_LIMIT_PER_MINUTE=1000

# Performance
OPCACHE_ENABLE=1

# Public APIs
NHTSA_API_ENABLED=true
EBAY_API_ENABLED=false
AMAZON_API_ENABLED=false
TECDOC_API_ENABLED=false
CARMED_API_ENABLED=false
AUTODOC_API_ENABLED=false
EOF

# Create cPanel setup instructions
print_status "Duke krijuar cPanel setup instructions..."
cat > "$CPANEL_DIR/CPANEL_SETUP_INSTRUCTIONS.md" << 'EOF'
# 🚀 CarWise.ai - cPanel Setup Instructions

## 📋 Hapat për Upload në cPanel

### 1. **Përgatitja e Database**
1. Hyni në cPanel
2. Shkoni te "MySQL Databases"
3. Krijoni një database të re (p.sh. `carwise_production`)
4. Krijoni një user të ri për database
5. Shtoni user-in në database me të gjitha privileges

### 2. **Upload i Files**
1. Shkoni te "File Manager" në cPanel
2. Hyni në `public_html` folder
3. Upload të gjitha files nga kjo paketë
4. Sigurohuni që të gjitha files janë në `public_html` root

### 3. **Konfigurimi i .env**
1. Hapni `.env` file në File Manager
2. Plotësoni vlerat e mëposhtme:
   - `APP_URL=https://yourdomain.com`
   - `DB_DATABASE=your_db_name`
   - `DB_USERNAME=your_db_user`
   - `DB_PASSWORD=your_db_password`
   - `OPENAI_API_KEY=your_openai_key`

### 4. **Instalimi i Dependencies**
1. Hyni në "Terminal" në cPanel (nëse është i disponueshëm)
2. Ose përdorni SSH nëse është i lejuar
3. Ekzekutoni:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

### 5. **Setup i Aplikacionit**
1. Ekzekutoni migrations:
   ```bash
   php artisan migrate --force
   ```
2. Krijoni storage link:
   ```bash
   php artisan storage:link
   ```
3. Optimizoni për production:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### 6. **Set Permissions**
1. Në File Manager, vendosni permissions:
   - `storage` folder: 755
   - `bootstrap/cache` folder: 755
   - `.env` file: 644

## 🔧 Troubleshooting

### Problemet e zakonshme:

1. **500 Internal Server Error**
   - Kontrolloni permissions
   - Kontrolloni .env configuration
   - Shikoni error logs në cPanel

2. **Database Connection Error**
   - Kontrolloni database credentials
   - Sigurohuni që database ekziston
   - Kontrolloni user permissions

3. **Asset Loading Issues**
   - Sigurohuni që `public` folder contents janë në root
   - Kontrolloni file permissions
   - Shikoni .htaccess file

## 📞 Mbështetja

Për mbështetje, shikoni dokumentacionin ose kontaktoni ekipin e zhvillimit.

---
**Data e krijimit:** $(date)
**Version:** cPanel Ready
EOF

# Create database structure file
print_status "Duke krijuar database structure file..."
if [ -f "database/mysql_structure.sql" ]; then
    cp database/mysql_structure.sql "$CPANEL_DIR/database_structure.sql"
else
    print_warning "Database structure file nuk u gjet. Do të duhet të importoni database manualisht."
fi

# Create upload checklist
print_status "Duke krijuar upload checklist..."
cat > "$CPANEL_DIR/UPLOAD_CHECKLIST.md" << 'EOF'
# ✅ cPanel Upload Checklist

## Para Upload-it
- [ ] Database është krijuar në cPanel
- [ ] Database user është krijuar dhe i shtuar në database
- [ ] AI API keys janë marrë
- [ ] Domain është konfiguruar

## Upload
- [ ] Të gjitha files janë uploaduar në public_html
- [ ] .env file është konfiguruar
- [ ] Database structure është importuar
- [ ] Permissions janë vendosur (755 për folders, 644 për files)

## Setup
- [ ] Dependencies janë instaluar (composer install)
- [ ] Migrations janë ekzekutuar
- [ ] Storage link është krijuar
- [ ] Cache është optimizuar

## Testing
- [ ] Website është accessible
- [ ] User registration funksionon
- [ ] AI diagnosis funksionon
- [ ] Të gjitha features janë testuar

## Launch
- [ ] SSL certificate është aktiv
- [ ] Error logs janë kontrolluar
- [ ] Performance është i mirë
- [ ] 🚀 LAUNCHED! 🎉
EOF

# Create archive
print_status "Duke krijuar archive..."
tar -czf "${CPANEL_DIR}.tar.gz" "$CPANEL_DIR"

# Create ZIP archive as well
print_status "Duke krijuar ZIP archive..."
zip -r "${CPANEL_DIR}.zip" "$CPANEL_DIR"

# Show package info
print_success "cPanel upload package u krijua me sukses!"
echo ""
echo "📦 Package Info:"
echo "==============="
echo "Directory: $CPANEL_DIR"
echo "TAR Archive: ${CPANEL_DIR}.tar.gz"
echo "ZIP Archive: ${CPANEL_DIR}.zip"
echo ""
echo "📊 Package Size:"
du -sh "$CPANEL_DIR"
du -sh "${CPANEL_DIR}.tar.gz"
du -sh "${CPANEL_DIR}.zip"
echo ""
echo "📁 Package Contents:"
ls -la "$CPANEL_DIR"
echo ""
echo "🎯 Hapat për Upload në cPanel:"
echo "1. Upload ${CPANEL_DIR}.zip në cPanel File Manager"
echo "2. Extract files në public_html folder"
echo "3. Krijoni database në cPanel"
echo "4. Konfiguroni .env file"
echo "5. Importoni database structure"
echo "6. Set permissions (755 për folders, 644 për files)"
echo "7. Testoni aplikacionin"
echo "8. Lëshoni në internet!"
echo ""
echo "📖 Për më shumë informacion, shikoni CPANEL_SETUP_INSTRUCTIONS.md në package."



