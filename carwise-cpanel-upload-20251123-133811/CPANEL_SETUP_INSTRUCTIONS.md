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
