#!/bin/bash

# CarWise.ai - Production Deployment Script
# Script për të lëshuar CarWise.ai në internet

echo "🚀 CarWise.ai - Production Deployment"
echo "======================================"

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

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   print_error "Mos ekzekuto këtë script si root. Përdor një user normal."
   exit 1
fi

# Check if .env file exists
if [ ! -f .env ]; then
    print_warning "File .env nuk ekziston. Kopjo env.production.example në .env"
    if [ -f env.production.example ]; then
        cp env.production.example .env
        print_success "Kopjuar env.production.example në .env"
    else
        print_error "env.production.example nuk ekziston!"
        exit 1
    fi
fi

# Check if .env is configured
if grep -q "YOUR_" .env; then
    print_warning "Ju lutem konfiguroni .env file-in para se të vazhdoni!"
    print_status "Hapni .env file-in dhe plotësoni të gjitha vlerat e nevojshme."
    exit 1
fi

print_status "Duke filluar deployment-in..."

# 1. Install Dependencies
print_status "Duke instaluar dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -ne 0 ]; then
    print_error "Gabim në instalimin e Composer dependencies!"
    exit 1
fi

npm ci --production --silent
if [ $? -ne 0 ]; then
    print_error "Gabim në instalimin e NPM dependencies!"
    exit 1
fi

# 2. Build Frontend Assets
print_status "Duke ndërtuar frontend assets..."
npm run build
if [ $? -ne 0 ]; then
    print_error "Gabim në ndërtimin e frontend assets!"
    exit 1
fi

# 3. Clear Caches
print_status "Duke pastruar cache-t..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 4. Generate Application Key (if not exists)
if ! grep -q "APP_KEY=base64:" .env; then
    print_status "Duke gjeneruar APP_KEY..."
    php artisan key:generate --force
fi

# 5. Run Database Migrations
print_status "Duke ekzekutuar database migrations..."
php artisan migrate --force
if [ $? -ne 0 ]; then
    print_error "Gabim në database migrations!"
    exit 1
fi

# 6. Seed Database (optional)
read -p "A doni të seed database-in? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Duke seed database-in..."
    php artisan db:seed --force
fi

# 7. Optimize for Production
print_status "Duke optimizuar për production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 8. Create Storage Link
print_status "Duke krijuar storage link..."
php artisan storage:link

# 9. Set Permissions
print_status "Duke vendosur permissions..."
chmod -R 755 storage bootstrap/cache
if [ -w /var/www ]; then
    chown -R www-data:www-data storage bootstrap/cache
fi

# 10. Health Check
print_status "Duke kontrolluar sistemin..."
if php artisan health:check > /dev/null 2>&1; then
    print_success "Sistemi është në gjendje të mirë!"
else
    print_warning "Health check dështoi, por deployment-i mund të jetë i suksesshëm."
fi

# 11. Create deployment info
print_status "Duke krijuar deployment info..."
cat > deployment-info.txt << EOF
CarWise.ai Production Deployment
===============================
Deployment Date: $(date)
Deployment User: $(whoami)
PHP Version: $(php -v | head -n 1)
Node Version: $(node -v)
Laravel Version: $(php artisan --version)

Environment: Production
Debug Mode: Off
Cache: Enabled
Optimization: Enabled

Next Steps:
1. Konfiguroni domain-in tuaj
2. Vendosni SSL certificate
3. Konfiguroni AI API keys
4. Testoni të gjitha funksionalitetet
5. Lëshoni në internet!

For support, check the documentation or contact the development team.
EOF

print_success "Deployment u përfundua me sukses!"
print_status "Informacioni i deployment-it u ruajt në deployment-info.txt"

# 12. Show next steps
echo ""
echo "🎉 CarWise.ai është gati për production!"
echo ""
echo "Hapat e ardhshëm:"
echo "1. Konfiguroni domain-in tuaj në .env file"
echo "2. Vendosni SSL certificate"
echo "3. Konfiguroni AI API keys në .env"
echo "4. Testoni aplikacionin"
echo "5. Lëshoni në internet!"
echo ""
echo "Për më shumë informacion, shikoni dokumentacionin në DEPLOYMENT_GUIDE.md"



