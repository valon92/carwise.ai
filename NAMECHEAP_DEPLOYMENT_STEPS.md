# CarWise.ai - Namecheap Deployment Steps

## 🚀 Hapat për Deployment në Namecheap

### **Hapi 1: Upload Files në cPanel**

1. **Login në cPanel**
   - Shko në `https://carwise.ai:2083` ose `https://198.177.120.15:2083`
   - Login me username: `carwvigr`

2. **File Manager**
   - Kliko "File Manager"
   - Shko në `/home/carwvigr/public_html`
   - Fshi të gjitha files ekzistuese (nëse ka)

3. **Upload Deployment Package**
   - Upload `carwise-deployment.zip` që kriova
   - Extract në `public_html` folder
   - **E rëndësishme**: Lëviz përmbajtjen e `public/` folder-it në root (`public_html`)

### **Hapi 2: Database Setup**

1. **MySQL Database**
   - Në cPanel, shko në "MySQL Databases"
   - Krijo database: `carwvigr_carwise`
   - Krijo user: `carwvigr_carwise`
   - Jep të gjitha privileges për user-in në database

2. **Import Database Structure**
   - Shko në "phpMyAdmin"
   - Zgjidh database-in `carwvigr_carwise`
   - Import `database/database.sqlite` (konverto në SQL format)

### **Hapi 3: Environment Configuration**

1. **Rename .env file**
   ```bash
   # Në File Manager, rename:
   env.namecheap.production → .env
   ```

2. **Update .env file me të dhënat e tua**
   ```env
   # Database
   DB_DATABASE=carwvigr_carwise
   DB_USERNAME=carwvigr_carwise
   DB_PASSWORD=YOUR_ACTUAL_DB_PASSWORD
   
   # App Key (generate në terminal)
   APP_KEY=base64:YOUR_GENERATED_KEY
   
   # AI API Keys
   OPENAI_API_KEY=your_actual_openai_key
   CLAUDE_API_KEY=your_actual_claude_key
   GEMINI_API_KEY=your_actual_gemini_key
   ```

### **Hapi 4: Laravel Setup**

1. **Generate App Key**
   ```bash
   # Në terminal (nëse ke SSH access):
   php artisan key:generate
   
   # Ose manual në .env file:
   APP_KEY=base64:YOUR_32_CHARACTER_KEY
   ```

2. **Run Migrations**
   ```bash
   # Nëse ke SSH access:
   php artisan migrate --force
   php artisan db:seed --force
   
   # Ose import database structure manual në phpMyAdmin
   ```

3. **Set Permissions**
   ```bash
   # Nëse ke SSH access:
   chmod -R 755 storage bootstrap/cache
   chown -R carwvigr:carwvigr storage bootstrap/cache
   ```

### **Hapi 5: SSL Certificate**

1. **Namecheap SSL**
   - Në cPanel, shko në "SSL/TLS"
   - Kliko "Let's Encrypt"
   - Aktivizo për `carwise.ai` dhe `www.carwise.ai`

2. **Force HTTPS**
   - Në .htaccess file, shto:
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

### **Hai 6: Test Deployment**

1. **Check Website**
   - Shko në `https://carwise.ai`
   - Testo të gjitha funksionet

2. **Check Database Connection**
   - Testo login/register
   - Testo AI diagnosis

3. **Check AI Integration**
   - Testo diagnosis me AI
   - Verifiko që API keys funksionojnë

## 🔧 Troubleshooting

### **Common Issues:**

1. **500 Internal Server Error**
   - Check file permissions (755 për folders, 644 për files)
   - Check .env configuration
   - Check error logs në cPanel

2. **Database Connection Error**
   - Verifiko database credentials në .env
   - Check nëse database ekziston në phpMyAdmin

3. **Asset Loading Issues**
   - Check nëse `public/build/` folder ka files
   - Verifiko .htaccess configuration

4. **SSL Issues**
   - Aktivizo SSL në cPanel
   - Check nëse domain points to correct IP

## 📊 Post-Deployment Checklist

- [ ] Website loads në `https://carwise.ai`
- [ ] SSL certificate is active
- [ ] Database connection works
- [ ] User registration/login works
- [ ] AI diagnosis works
- [ ] Car parts search works
- [ ] Mobile responsive design works
- [ ] PWA features work
- [ ] Email notifications work (nëse konfiguruar)

## 🚀 Next Steps

1. **Setup AI API Keys** - Konfiguro OpenAI, Claude, ose Gemini
2. **Setup Email** - Konfiguro SMTP për notifications
3. **Setup Monitoring** - Monitor performance dhe errors
4. **Setup Backups** - Automated database backups
5. **SEO Optimization** - Meta tags, sitemap, robots.txt

## 💡 Tips

- **Performance**: Use Cloudflare CDN për better performance
- **Security**: Regular updates dhe security patches
- **Monitoring**: Check error logs regularly
- **Backups**: Setup automated backups
- **Updates**: Keep Laravel dhe dependencies updated








