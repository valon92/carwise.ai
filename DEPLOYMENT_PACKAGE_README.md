# 📦 CarWise.ai - Deployment Package për cPanel

## ✅ Paketa e Krijuar

**File:** `carwise-cpanel-upload-20251123-132547.zip` (41 MB)

Kjo paketë përmban **gjithçka që ju nevojitet** për të vendosur projektin CarWise.ai në cPanel.

---

## 📋 Çfarë Përmban Paketa

### ✅ Core Files
- ✅ Të gjitha skedarët e aplikacionit Laravel
- ✅ Frontend assets të build-uar (Vite)
- ✅ Vendor folder me të gjitha dependencies
- ✅ Database structure SQL file
- ✅ Konfigurim .env për cPanel
- ✅ .htaccess file për Apache

### ✅ Dokumentacion
- ✅ `CPANEL_SETUP_INSTRUCTIONS.md` - Udhëzime të detajuara
- ✅ `UPLOAD_CHECKLIST.md` - Checklist për upload
- ✅ `database_structure.sql` - SQL për import

### ✅ Storage & Cache
- ✅ Storage directories të krijuara
- ✅ Bootstrap cache directories
- ✅ Permissions të konfiguruara

---

## 🚀 Hapat për Upload në cPanel

### 1. **Përgatitja e Database**
1. Hyni në cPanel
2. Shkoni te **"MySQL Databases"**
3. Krijoni një database të re (p.sh. `carwise_production`)
4. Krijoni një user të ri për database
5. Shtoni user-in në database me **të gjitha privileges**

### 2. **Upload i Files**
1. Hyni në **"File Manager"** në cPanel
2. Shkoni në `public_html` folder
3. Upload `carwise-cpanel-upload-20251123-132547.zip`
4. **Extract** files:
   - Klikoni mbi ZIP file
   - Zgjidhni "Extract"
   - Sigurohuni që të gjitha files janë në `public_html` root

### 3. **Konfigurimi i .env**
1. Hapni `.env` file në File Manager
2. Plotësoni vlerat e mëposhtme:

```env
# Domain-in tuaj
APP_URL=https://yourdomain.com

# Database credentials (nga hapi 1)
DB_DATABASE=carwise_production
DB_USERNAME=carwise_user
DB_PASSWORD=your_secure_password

# AI API Key (të paktën një)
OPENAI_API_KEY=your_openai_key_here
# OSE
CLAUDE_API_KEY=your_claude_key_here
# OSE
GEMINI_API_KEY=your_gemini_key_here

# Email configuration (opsionale)
MAIL_HOST=mail.yourdomain.com
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
```

### 4. **Importimi i Database**
1. Hyni në **"phpMyAdmin"** në cPanel
2. Zgjidhni database-in tuaj
3. Klikoni **"Import"**
4. Upload `database_structure.sql` file
5. Klikoni **"Go"** për të importuar

### 5. **Set Permissions**
1. Në File Manager, vendosni permissions:
   - `storage` folder: **755**
   - `bootstrap/cache` folder: **755**
   - `.env` file: **644**
   - Të gjitha files: **644**

### 6. **Instalimi i Dependencies (nëse nevojitet)**
Nëse vendor folder nuk funksionon, hyni në Terminal në cPanel dhe ekzekutoni:
```bash
composer install --no-dev --optimize-autoloader
```

### 7. **Setup i Aplikacionit**
Në Terminal në cPanel, ekzekutoni:
```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. **Testimi**
1. Shkoni në domain-in tuaj në browser
2. Kontrolloni që website-i hapet
3. Testoni user registration
4. Testoni AI diagnosis
5. Kontrolloni të gjitha funksionalitetet

---

## 📊 Informacione të Paketës

- **Madhësia:** 41 MB (ZIP)
- **Version:** Production Ready
- **Data e krijimit:** 23 Nëntor 2025
- **PHP Version:** 8.2+
- **MySQL Version:** 5.7+

---

## 🔧 Troubleshooting

### Problemet e zakonshme:

1. **500 Internal Server Error**
   - Kontrolloni permissions (755 për folders, 644 për files)
   - Kontrolloni .env configuration
   - Shikoni error logs në cPanel

2. **Database Connection Error**
   - Kontrolloni database credentials në .env
   - Sigurohuni që database ekziston
   - Kontrolloni user permissions

3. **AI API Error**
   - Kontrolloni API keys në .env
   - Sigurohuni që të paktën një AI provider është konfiguruar

4. **Asset Loading Issues**
   - Sigurohuni që `build` folder ekziston
   - Kontrolloni file permissions
   - Shikoni .htaccess file

---

## ✅ Checklist për Upload

### Para Upload-it:
- [ ] Database është krijuar në cPanel
- [ ] Database user është krijuar dhe i shtuar
- [ ] AI API keys janë marrë
- [ ] Domain është konfiguruar

### Gjatë Upload-it:
- [ ] ZIP file është uploaduar në public_html
- [ ] Files janë extractuar në root
- [ ] .env file është konfiguruar
- [ ] Database structure është importuar

### Pas Upload-it:
- [ ] Permissions janë vendosur (755/644)
- [ ] Website është accessible
- [ ] User registration funksionon
- [ ] AI diagnosis funksionon
- [ ] Të gjitha features janë testuar

---

## 📞 Mbështetja

Për mbështetje shtesë:
- Shikoni `CPANEL_SETUP_INSTRUCTIONS.md` në package
- Kontrolloni `UPLOAD_CHECKLIST.md` për lista të detajuara
- Kontaktoni ekipin e zhvillimit

---

## 🎉 Suksese me lëshimin!

Paketa është gati për upload në cPanel. Ndiqni hapat e mësipërm dhe projekti do të jetë live në pak minuta!

**File për upload:** `carwise-cpanel-upload-20251123-132547.zip`

