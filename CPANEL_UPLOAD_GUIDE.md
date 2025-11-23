# 📁 CarWise.ai - Udhëzues për Upload në cPanel

## 🎯 **File që duhet të uploadoni në cPanel:**

**`carwise-cpanel-upload-20251022-080113.zip`** (41MB)

Ky është file-i kryesor që duhet të uploadoni në cPanel File Manager.

---

## 📋 **Hapat e Plotë për Upload në cPanel**

### **1. Përgatitja e Database**
1. **Hyni në cPanel**
2. **Shkoni te "MySQL Databases"**
3. **Krijoni një database të re:**
   - Emri: `carwise_production` (ose emër tjetër)
   - Sigurohuni që të mbani mend emrin e database
4. **Krijoni një user të ri për database:**
   - Username: `carwise_user` (ose username tjetër)
   - Password: `secure_password123` (ose password i fortë)
5. **Shtoni user-in në database:**
   - Zgjidhni user-in dhe database-in
   - Shtoni të gjitha privileges (ALL PRIVILEGES)

### **2. Upload i Files**
1. **Hyni në "File Manager" në cPanel**
2. **Shkoni në `public_html` folder**
3. **Upload `carwise-cpanel-upload-20251022-080113.zip`**
4. **Extract files:**
   - Klikoni mbi ZIP file
   - Zgjidhni "Extract"
   - Sigurohuni që të gjitha files janë në `public_html` root

### **3. Konfigurimi i .env File**
1. **Hapni `.env` file në File Manager**
2. **Plotësoni vlerat e mëposhtme:**

```env
# Domain-in tuaj
APP_URL=https://yourdomain.com

# Database credentials (nga hapi 1)
DB_DATABASE=carwise_production
DB_USERNAME=carwise_user
DB_PASSWORD=secure_password123

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

### **4. Importimi i Database Structure**
1. **Hyni në "phpMyAdmin" në cPanel**
2. **Zgjidhni database-in tuaj**
3. **Klikoni "Import"**
4. **Upload `database_structure.sql` file**
5. **Klikoni "Go" për të importuar**

### **5. Set Permissions**
1. **Në File Manager, vendosni permissions:**
   - `storage` folder: **755**
   - `bootstrap/cache` folder: **755**
   - `.env` file: **644**
   - Të gjitha files: **644**

### **6. Testimi i Aplikacionit**
1. **Shkoni në domain-in tuaj në browser**
2. **Kontrolloni që website-i hapet**
3. **Testoni user registration**
4. **Testoni AI diagnosis**
5. **Kontrolloni të gjitha funksionalitetet**

---

## 🔧 **Troubleshooting**

### **Problemet e zakonshme:**

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
   - Kontrolloni internet connection

4. **Asset Loading Issues**
   - Sigurohuni që `build` folder ekziston
   - Kontrolloni file permissions
   - Shikoni .htaccess file

---

## 📊 **Requirements për cPanel**

### **Server Requirements:**
- **PHP**: 8.2+ (kërkohet)
- **MySQL**: 5.7+ (kërkohet)
- **Memory**: 256MB+ (rekomanduar)
- **Disk Space**: 1GB+ (për aplikacion)

### **cPanel Features të nevojshme:**
- **File Manager** (për upload)
- **MySQL Databases** (për database)
- **phpMyAdmin** (për import)
- **Terminal/SSH** (opsionale, për troubleshooting)

---

## 🎯 **Checklist për Upload**

### **Para Upload-it:**
- [ ] Database është krijuar në cPanel
- [ ] Database user është krijuar dhe i shtuar
- [ ] AI API keys janë marrë
- [ ] Domain është konfiguruar

### **Gjatë Upload-it:**
- [ ] ZIP file është uploaduar në public_html
- [ ] Files janë extractuar në root
- [ ] .env file është konfiguruar
- [ ] Database structure është importuar

### **Pas Upload-it:**
- [ ] Permissions janë vendosur (755/644)
- [ ] Website është accessible
- [ ] User registration funksionon
- [ ] AI diagnosis funksionon
- [ ] Të gjitha features janë testuar

---

## 🚀 **Hapat e Fundit**

1. **Testoni aplikacionin plotësisht**
2. **Kontrolloni error logs**
3. **Sigurohuni që të gjitha funksionalitetet punojnë**
4. **Lëshoni në internet!**

---

## 📞 **Mbështetja**

Për mbështetje shtesë:
- Shikoni `CPANEL_SETUP_INSTRUCTIONS.md` në package
- Kontrolloni `UPLOAD_CHECKLIST.md` për lista të detajuara
- Kontaktoni ekipin e zhvillimit

---

**🎉 Suksese me lëshimin e CarWise.ai në internet!** 🚀



