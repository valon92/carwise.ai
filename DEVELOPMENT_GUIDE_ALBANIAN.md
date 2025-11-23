# 🚀 CarWise.ai - Udhëzues për Lëshimin në Internet

## 📋 Përmbajtja
1. [Përgatitja për Deployment](#përgatitja-për-deployment)
2. [Opsionet e Deployment-it](#opsionet-e-deployment-it)
3. [Konfigurimi i Server-it](#konfigurimi-i-server-it)
4. [Konfigurimi i Aplikacionit](#konfigurimi-i-aplikacionit)
5. [Testimi dhe Lëshimi](#testimi-dhe-lëshimi)
6. [Mbështetja dhe Mirëmbajtja](#mbështetja-dhe-mirëmbajtja)

---

## 🎯 Përgatitja për Deployment

### Çfarë ju nevojitet:
- **Domain name** (p.sh. carwise.ai)
- **Server** (VPS ose shared hosting)
- **AI API keys** (OpenAI, Claude, ose Gemini)
- **Email service** (për notifikime)
- **SSL certificate** (për siguri)

### Para se të filloni:
1. **Kopjoni production.env në .env**
2. **Konfiguroni të gjitha vlerat në .env**
3. **Sigurohuni që të keni të gjitha API keys**
4. **Testoni aplikacionin lokal**

---

## 🌐 Opsionet e Deployment-it

### 1. **DigitalOcean Droplet (Rekomanduar)**
- **Kostoja**: $12-24/muaj
- **Kontrolli i plotë** mbi server
- **Performancë e mirë**
- **SSL falas** me Let's Encrypt

### 2. **Namecheap Hosting**
- **Shared Hosting**: $3-10/muaj
- **VPS Hosting**: $10-30/muaj
- **Kontroll i kufizuar** (shared)
- **Kontroll i plotë** (VPS)

### 3. **Railway (Më i lehtë)**
- **Deployment automatik**
- **Kostoja**: $5-20/muaj
- **Nuk kërkon njohuri server**
- **SSL automatik**

### 4. **Laravel Forge (Profesional)**
- **Kostoja**: $12-39/muaj
- **Deployment automatik**
- **Monitoring i plotë**
- **Backup automatik**

---

## 🖥️ Konfigurimi i Server-it

### Për DigitalOcean VPS:

```bash
# 1. Lidhuni me server
ssh root@YOUR_SERVER_IP

# 2. Përditësoni sistemin
apt update && apt upgrade -y

# 3. Instaloni software të nevojshëm
apt install -y nginx mysql-server redis-server php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath nodejs npm git

# 4. Instaloni Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# 5. Konfiguroni MySQL
mysql_secure_installation
mysql -u root -p
CREATE DATABASE carwise_production;
CREATE USER 'carwise_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON carwise_production.* TO 'carwise_user'@'localhost';
FLUSH PRIVILEGES;
```

### Për Namecheap Shared Hosting:

1. **Hyni në cPanel**
2. **Krijoni MySQL database**
3. **Upload files në public_html**
4. **Konfiguroni .env file**

---

## ⚙️ Konfigurimi i Aplikacionit

### 1. **Kopjoni dhe konfiguroni .env file:**

```bash
# Kopjoni production configuration
cp production.env .env

# Hapni .env file dhe plotësoni vlerat
nano .env
```

### 2. **Vlerat e rëndësishme që duhet të ndryshoni:**

```env
# Domain-in tuaj
APP_URL=https://carwise.ai

# Database credentials
DB_DATABASE=carwise_production
DB_USERNAME=carwise_user
DB_PASSWORD=your_secure_password

# AI API Keys (të paktën një)
OPENAI_API_KEY=your_openai_key
CLAUDE_API_KEY=your_claude_key
GEMINI_API_KEY=your_gemini_key

# Email configuration
MAIL_HOST=your_smtp_host
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_email_password
```

### 3. **Ekzekutoni deployment script:**

```bash
# Bëni script-in të ekzekutueshëm
chmod +x deploy-production.sh

# Ekzekutoni deployment
./deploy-production.sh
```

---

## 🔧 Konfigurimi i Nginx

### Për VPS (DigitalOcean, Namecheap VPS):

```bash
# Krijoni Nginx configuration
nano /etc/nginx/sites-available/carwise
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
# Aktivizoni site-in
ln -s /etc/nginx/sites-available/carwise /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default

# Testoni dhe restartoni
nginx -t
systemctl restart nginx
systemctl restart php8.2-fpm
```

---

## 🔒 Konfigurimi i SSL

### Me Let's Encrypt (falas):

```bash
# Instaloni Certbot
apt install certbot python3-certbot-nginx

# Merrni SSL certificate
certbot --nginx -d carwise.ai -d www.carwise.ai

# Testoni auto-renewal
certbot renew --dry-run
```

---

## 🧪 Testimi dhe Lëshimi

### 1. **Testoni aplikacionin:**

```bash
# Kontrolloni sistemin
php artisan health:check

# Testoni AI providers
php artisan ai:test

# Kontrolloni database
php artisan migrate:status
```

### 2. **Testoni në browser:**
- Shkoni në `https://carwise.ai`
- Regjistrohuni si përdorues
- Testoni AI diagnosis
- Kontrolloni të gjitha funksionalitetet

### 3. **Kontrolloni logs:**

```bash
# Shikoni error logs
tail -f storage/logs/laravel.log

# Shikoni Nginx logs
tail -f /var/log/nginx/error.log
```

---

## 📊 Monitoring dhe Mirëmbajtja

### 1. **Kontrolli i performancës:**

```bash
# Kontrolloni përdorimin e memories
htop

# Kontrolloni disk space
df -h

# Kontrolloni database
mysql -u carwise_user -p carwise_production
```

### 2. **Backup i rregullt:**

```bash
# Database backup
mysqldump -u carwise_user -p carwise_production > backup_$(date +%Y%m%d).sql

# Files backup
tar -czf carwise_backup_$(date +%Y%m%d).tar.gz /var/www/carwise
```

### 3. **Përditësimet:**

```bash
# Përditësoni aplikacionin
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

---

## 🆘 Troubleshooting

### Problemet e zakonshme:

1. **500 Internal Server Error**
   - Kontrolloni permissions: `chmod -R 755 storage bootstrap/cache`
   - Kontrolloni .env configuration
   - Shikoni error logs

2. **Database Connection Error**
   - Kontrolloni database credentials
   - Sigurohuni që MySQL është running
   - Kontrolloni firewall settings

3. **AI API Error**
   - Kontrolloni API keys
   - Kontrolloni internet connection
   - Shikoni API usage limits

4. **SSL Certificate Error**
   - Kontrolloni domain DNS settings
   - Sigurohuni që domain pointon në server
   - Rikontrolloni Certbot configuration

---

## 💰 Kostoja e Deployment-it

### Opsioni 1: DigitalOcean
- **Server**: $12-24/muaj
- **Domain**: $10-15/vit
- **SSL**: Falas
- **Total**: ~$15-30/muaj

### Opsioni 2: Namecheap
- **Shared Hosting**: $3-10/muaj
- **VPS**: $10-30/muaj
- **Domain**: $10-15/vit
- **SSL**: $10-20/vit
- **Total**: ~$5-40/muaj

### Opsioni 3: Railway
- **Hosting**: $5-20/muaj
- **Domain**: $10-15/vit
- **SSL**: Falas
- **Total**: ~$10-25/muaj

---

## 🎉 Përfundimi

CarWise.ai është tani gati për lëshim në internet! Ndiqni hapat e mësipërm dhe aplikacioni juaj do të jetë live në pak orë.

### Hapat e fundit:
1. ✅ Konfiguroni domain-in
2. ✅ Vendosni SSL certificate
3. ✅ Testoni të gjitha funksionalitetet
4. ✅ Lëshoni në internet!

**🚀 Suksese me lëshimin!** 🎉



