# CarWise.ai Deployment Guide

## 🚀 Quick Deployment Options

### Option 1: DigitalOcean Droplet (Recommended)

1. **Create Droplet**
   - Go to [DigitalOcean](https://digitalocean.com)
   - Create new Droplet: Ubuntu 22.04 LTS
   - Size: 2GB RAM, 1 CPU, 50GB SSD ($12/month)
   - Add SSH key

2. **Server Setup**
   ```bash
   # Connect to server
   ssh root@YOUR_SERVER_IP
   
   # Update system
   apt update && apt upgrade -y
   
   # Install required software
   apt install -y nginx mysql-server redis-server php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath nodejs npm git
   
   # Install Composer
   curl -sS https://getcomposer.org/installer | php
   mv composer.phar /usr/local/bin/composer
   ```

3. **Deploy Application**
   ```bash
   # Clone repository
   git clone https://github.com/YOUR_USERNAME/carwise.ai.git /var/www/carwise
   cd /var/www/carwise
   
   # Install dependencies
   composer install --no-dev --optimize-autoloader
   npm ci --production
   npm run build
   
   # Setup environment
   cp env.production.example .env
   nano .env  # Configure your settings
   
   # Generate app key
   php artisan key:generate
   
   # Setup database
   mysql -u root -p
   CREATE DATABASE carwise_production;
   CREATE USER 'carwise_user'@'localhost' IDENTIFIED BY 'secure_password';
   GRANT ALL PRIVILEGES ON carwise_production.* TO 'carwise_user'@'localhost';
   FLUSH PRIVILEGES;
   
   # Run migrations
   php artisan migrate --force
   php artisan db:seed --force
   
   # Set permissions
   chown -R www-data:www-data /var/www/carwise
   chmod -R 755 /var/www/carwise/storage
   chmod -R 755 /var/www/carwise/bootstrap/cache
   
   # Create storage link
   php artisan storage:link
   
   # Optimize for production
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Configure Nginx**
   ```bash
   # Create Nginx config
   nano /etc/nginx/sites-available/carwise
   ```
   
   ```nginx
   server {
       listen 80;
       server_name your-domain.com www.your-domain.com;
       root /var/www/carwise/public;
       
       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";
       
       index index.php;
       
       charset utf-8;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }
       
       error_page 404 /index.php;
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
       
       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```
   
   ```bash
   # Enable site
   ln -s /etc/nginx/sites-available/carwise /etc/nginx/sites-enabled/
   rm /etc/nginx/sites-enabled/default
   
   # Test and restart
   nginx -t
   systemctl restart nginx
   systemctl restart php8.2-fpm
   ```

5. **Setup SSL with Let's Encrypt**
   ```bash
   apt install certbot python3-certbot-nginx
   certbot --nginx -d your-domain.com -d www.your-domain.com
   ```

### Option 2: Laravel Forge (Easiest)

1. **Sign up at [Laravel Forge](https://forge.laravel.com)**
2. **Connect DigitalOcean account**
3. **Create new server**
4. **Deploy from Git repository**
5. **Configure environment variables**
6. **Setup SSL certificate**

### Option 3: Railway

1. **Sign up at [Railway](https://railway.app)**
2. **Connect GitHub repository**
3. **Add MySQL database**
4. **Configure environment variables**
5. **Deploy automatically**

## 🔧 Environment Configuration

### Required Environment Variables

```env
# Application
APP_NAME="CarWise.ai"
APP_ENV=production
APP_KEY=base64:YOUR_PRODUCTION_APP_KEY
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=carwise_production
DB_USERNAME=carwise_user
DB_PASSWORD=YOUR_SECURE_PASSWORD

# AI Providers (at least one required)
OPENAI_API_KEY=your_openai_key
CLAUDE_API_KEY=your_claude_key
GEMINI_API_KEY=your_gemini_key

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="CarWise.ai"
```

## 📊 Monitoring & Maintenance

### Setup Monitoring
```bash
# Install monitoring tools
apt install htop iotop nethogs

# Setup log rotation
nano /etc/logrotate.d/carwise
```

### Backup Strategy
```bash
# Database backup
mysqldump -u carwise_user -p carwise_production > backup_$(date +%Y%m%d).sql

# Files backup
tar -czf carwise_backup_$(date +%Y%m%d).tar.gz /var/www/carwise
```

## 🚨 Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   chown -R www-data:www-data /var/www/carwise
   chmod -R 755 /var/www/carwise/storage
   ```

2. **Database Connection Issues**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Asset Loading Issues**
   ```bash
   npm run build
   php artisan storage:link
   ```

## 💰 Cost Estimation

### Monthly Costs
- **DigitalOcean Droplet**: $12-24
- **Domain**: $10-15/year
- **SSL Certificate**: Free (Let's Encrypt)
- **AI API Usage**: $5-50 (depending on usage)
- **Email Service**: $0-10

**Total**: ~$15-40/month

## 🔄 Updates & Maintenance

### Deploy Updates
```bash
cd /var/www/carwise
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Regular Maintenance
- Weekly database backups
- Monthly security updates
- Monitor AI API usage
- Check error logs
- Update dependencies quarterly

