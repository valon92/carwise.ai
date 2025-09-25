#!/bin/bash

# CarWise.ai Production Deployment Script
echo "🚀 Starting CarWise.ai Production Deployment..."

# Set production environment
export APP_ENV=production

# Install/Update Dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader
npm ci --production

# Build Frontend Assets
echo "🔨 Building frontend assets..."
npm run build

# Clear and Cache
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for Production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run Migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed Database (if needed)
echo "🌱 Seeding database..."
php artisan db:seed --force

# Set Permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Create Storage Link
echo "🔗 Creating storage link..."
php artisan storage:link

# Restart Services
echo "🔄 Restarting services..."
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart redis

# Health Check
echo "🏥 Running health check..."
php artisan health:check

echo "✅ Deployment completed successfully!"
echo "🌐 CarWise.ai is now live at: https://carwise.ai"
