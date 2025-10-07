# CarWise.ai - Namecheap Deployment Guide

## 🎯 Deployment në Namecheap Hosting

### Option 1: Shared Hosting (Stellar Plus ose më i lartë)

**Requirements:**
- PHP 8.2+
- MySQL 5.7+
- Node.js support (në disa plana)
- SSH access (në plana më të larta)

**Steps:**

1. **Prepare Project for Shared Hosting**
   ```bash
   # Build assets locally
   npm run build
   
   # Create deployment package
   tar -czf carwise-deployment.tar.gz \
     --exclude=node_modules \
     --exclude=.git \
     --exclude=storage/logs \
     --exclude=storage/framework/cache \
     --exclude=storage/framework/sessions \
     --exclude=storage/framework/views \
     --exclude=vendor \
     .
   ```

2. **Upload to Namecheap**
   - Login to cPanel
   - Go to File Manager
   - Upload `carwise-deployment.tar.gz`
   - Extract to `public_html` folder

3. **Database Setup**
   - Create MySQL database in cPanel
   - Import database structure
   - Update `.env` file with database credentials

4. **Configure Environment**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://carwise.ai
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```

### Option 2: VPS Hosting (Recommended)

**If you have VPS access:**

1. **Connect via SSH**
   ```bash
   ssh username@carwise.ai
   ```

2. **Install Dependencies**
   ```bash
   # Update system
   sudo apt update && sudo apt upgrade -y
   
   # Install PHP 8.2
   sudo apt install -y php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath
   
   # Install Composer
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   
   # Install Node.js
   curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
   sudo apt-get install -y nodejs
   
   # Install MySQL
   sudo apt install -y mysql-server
   
   # Install Nginx
   sudo apt install -y nginx
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
   nano .env
   
   # Generate app key
   php artisan key:generate
   
   # Setup database
   sudo mysql -u root -p
   CREATE DATABASE carwise_production;
   CREATE USER 'carwise_user'@'localhost' IDENTIFIED BY 'secure_password';
   GRANT ALL PRIVILEGES ON carwise_production.* TO 'carwise_user'@'localhost';
   FLUSH PRIVILEGES;
   
   # Run migrations
   php artisan migrate --force
   php artisan db:seed --force
   
   # Set permissions
   sudo chown -R www-data:www-data /var/www/carwise
   sudo chmod -R 755 /var/www/carwise/storage
   sudo chmod -R 755 /var/www/carwise/bootstrap/cache
   
   # Create storage link
   php artisan storage:link
   
   # Optimize for production
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Configure Nginx**
   ```bash
   sudo nano /etc/nginx/sites-available/carwise
   ```
   
   ```nginx
   server {
       listen 80;
       server_name carwise.ai www.carwise.ai;
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
   sudo ln -s /etc/nginx/sites-available/carwise /etc/nginx/sites-enabled/
   sudo rm /etc/nginx/sites-enabled/default
   
   # Test and restart
   sudo nginx -t
   sudo systemctl restart nginx
   sudo systemctl restart php8.2-fpm
   ```

5. **Setup SSL**
   ```bash
   sudo apt install certbot python3-certbot-nginx
   sudo certbot --nginx -d carwise.ai -d www.carwise.ai
   ```

### Option 3: cPanel Deployment (Shared Hosting)

**If you only have cPanel access:**

1. **Upload Files**
   - Use File Manager or FTP
   - Upload all files to `public_html` folder
   - Make sure `public` folder contents are in root

2. **Database Setup**
   - Create MySQL database in cPanel
   - Import database structure
   - Update `.env` file

3. **Configure .htaccess**
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ public/$1 [L]
   </IfModule>
   ```

4. **Set Permissions**
   - Set 755 for folders
   - Set 644 for files
   - Set 777 for storage and bootstrap/cache

## 🔧 Environment Configuration for Namecheap

```env
# Application
APP_NAME="CarWise.ai"
APP_ENV=production
APP_KEY=base64:YOUR_PRODUCTION_APP_KEY
APP_DEBUG=false
APP_URL=https://carwise.ai

# Database (Namecheap MySQL)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_namecheap_db_name
DB_USERNAME=your_namecheap_db_user
DB_PASSWORD=your_namecheap_db_password

# AI Providers
OPENAI_API_KEY=your_openai_key
CLAUDE_API_KEY=your_claude_key
GEMINI_API_KEY=your_gemini_key

# Mail (Namecheap Email)
MAIL_MAILER=smtp
MAIL_HOST=mail.carwise.ai
MAIL_PORT=587
MAIL_USERNAME=noreply@carwise.ai
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@carwise.ai
MAIL_FROM_NAME="CarWise.ai"

# Cache (if Redis available)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

## 📊 Namecheap Specific Considerations

### Shared Hosting Limitations:
- **File upload limits**: Usually 2MB-64MB
- **Memory limits**: 128MB-512MB
- **Execution time**: 30-300 seconds
- **Database size**: 1GB-10GB
- **No SSH access** (in basic plans)
- **No cron jobs** (in basic plans)

### VPS Advantages:
- **Full control** over server
- **SSH access**
- **Custom configurations**
- **Better performance**
- **Cron jobs support**
- **Multiple domains**

## 🚀 Quick Start Commands

### For VPS:
```bash
# Quick deployment script
curl -sSL https://raw.githubusercontent.com/YOUR_USERNAME/carwise.ai/main/deploy.sh | bash
```

### For Shared Hosting:
```bash
# Build and package locally
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create deployment package
zip -r carwise-deployment.zip . -x "node_modules/*" ".git/*" "storage/logs/*" "storage/framework/cache/*" "storage/framework/sessions/*" "storage/framework/views/*"
```

## 🔍 Troubleshooting

### Common Issues:

1. **500 Internal Server Error**
   - Check file permissions
   - Check .env configuration
   - Check error logs in cPanel

2. **Database Connection Error**
   - Verify database credentials
   - Check if database exists
   - Check MySQL service status

3. **Asset Loading Issues**
   - Run `npm run build`
   - Check public/build folder
   - Verify file permissions

4. **SSL Certificate Issues**
   - Use Let's Encrypt (if VPS)
   - Use Namecheap SSL (if shared hosting)
   - Check domain DNS settings

## 💡 Recommendations

1. **Upgrade to VPS** if possible for better performance
2. **Use Cloudflare** for CDN and security
3. **Setup automated backups**
4. **Monitor resource usage**
5. **Use staging environment** for testing

