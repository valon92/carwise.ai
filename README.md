# CarWise.ai - Smart Car Diagnosis Platform

CarWise.ai është një platformë e mençur që i mundëson pronarëve të automjeteve të diagnostikojnë problemet e veturave të tyre duke përdorur fotografi, video dhe zëra të motorit. Në vend që të shkojnë direkt te mekaniku, përdoruesit mund të marrin një analizë AI mbi problemin dhe sugjerime për zgjidhje.

## 🚀 Funksionalitetet Kryesore

- **Diagnostikim me AI**: Analizë e fotove, videove dhe zërit për identifikimin e problemeve
- **Menaxhim i makinave**: Shtimi dhe menaxhimi i makinave të përdoruesit
- **Histori e diagnostikimeve**: Ruajtja e të gjitha diagnostikimeve për çdo makinë
- **Rrjeti i mekanikëve**: Lidhje me mekanikë të certifikuar
- **Autentifikim i sigurt**: Sistem i plotë i regjistrimit dhe hyrjes

## 🛠 Teknologjitë

### Frontend
- **Vue.js 3** - Framework JavaScript për UI
- **Vue Router** - Navigim në aplikacion
- **Tailwind CSS** - Styling dhe responsive design
- **Axios** - Komunikim me API

### Backend
- **Laravel 11** - PHP Framework
- **Laravel Sanctum** - Autentifikim API
- **MySQL/SQLite** - Database
- **File Storage** - Ruajtja e skedarëve

## 📦 Instalimi

### Kërkesat
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL ose SQLite

### Hapat e instalimit

1. **Klono repository-n**
```bash
git clone <repository-url>
cd carwise.ai
```

2. **Instalo dependencies për backend**
```bash
composer install
```

3. **Konfiguro environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Konfiguro database në .env**
```env
DB_CONNECTION=sqlite
# ose për MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=carwise
# DB_USERNAME=root
# DB_PASSWORD=
```

5. **Ekzekuto migracionet dhe seeders**
```bash
php artisan migrate
php artisan db:seed --class=CarWiseSeeder
php artisan storage:link
```

6. **Instalo dependencies për frontend**
```bash
npm install
```

7. **Build frontend assets**
```bash
npm run build
```

## 🚀 Startimi i aplikacionit

### Development mode
```bash
# Terminal 1 - Backend
php artisan serve

# Terminal 2 - Frontend (nëse përdor Vite)
npm run dev
```

### Production mode
```bash
# Build frontend
npm run build

# Start Laravel
php artisan serve
```

Aplikacioni do të jetë i disponueshëm në `http://localhost:8000`

## 👤 Përdoruesit e testimit

### Customer
- **Email**: demo@carwise.ai
- **Password**: password123

### Mechanic
- **Email**: arben@carwise.ai
- **Password**: password123

## 📁 Struktura e projektit

```
carwise.ai/
├── app/
│   ├── Http/Controllers/Api/     # API Controllers
│   └── Models/                   # Eloquent Models
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── resources/
│   ├── js/
│   │   ├── components/           # Vue Components
│   │   ├── views/                # Vue Views
│   │   └── services/             # API Services
│   └── css/                      # Styles
├── routes/
│   ├── api.php                   # API Routes
│   └── web.php                   # Web Routes
└── storage/app/public/           # File uploads
```

## 🔧 API Endpoints

### Authentication
- `POST /api/register` - Regjistrim i përdoruesit
- `POST /api/login` - Hyrje në sistem
- `POST /api/logout` - Dalje nga sistemi
- `GET /api/user` - Informacion për përdoruesin aktual

### Cars
- `GET /api/cars` - Lista e makinave
- `POST /api/cars` - Shtimi i makine të re
- `GET /api/cars/{id}` - Detajet e makines
- `PUT /api/cars/{id}` - Përditësimi i makines
- `DELETE /api/cars/{id}` - Fshirja e makines

### Diagnosis
- `GET /api/diagnoses` - Lista e diagnostikimeve
- `POST /api/diagnoses` - Krijimi i diagnostikimit të ri
- `GET /api/diagnoses/{id}` - Detajet e diagnostikimit

### Mechanics
- `GET /api/mechanics` - Lista e mekanikëve
- `GET /api/mechanics/{id}` - Detajet e mekanikut
- `PUT /api/mechanics/{id}` - Përditësimi i profilit të mekanikut

## 🎨 UI Components

- **Navbar** - Navigimi kryesor
- **UploadForm** - Ngarkimi i skedarëve
- **DiagnosisCard** - Shfaqja e rezultateve
- **CarCard** - Informacion për makinat

## 📱 Views

- **Home** - Faqja kryesore
- **Diagnose** - Diagnostikimi i makinave
- **MyCars** - Menaxhimi i makinave
- **Mechanics** - Lista e mekanikëve
- **Login/Register** - Autentifikimi

## 🔮 Zhvillimet e ardhshme

- [ ] Integrimi i AI-së së vërtetë për analizën e skedarëve
- [ ] Sistem i pagesave për konsultime me mekanikë
- [ ] Chat në kohë reale me mekanikë
- [ ] Notifikime push
- [ ] Mobile app (React Native/Flutter)
- [ ] Dashboard për admin
- [ ] Sistem i review-ve për mekanikë

## 🤝 Kontributimi

1. Fork repository-n
2. Krijoni një branch për feature-in tuaj (`git checkout -b feature/AmazingFeature`)
3. Commit ndryshimet tuaja (`git commit -m 'Add some AmazingFeature'`)
4. Push në branch (`git push origin feature/AmazingFeature`)
5. Hapni një Pull Request

## 📄 Licenca

Ky projekt është i licencuar nën MIT License - shihni skedarin [LICENSE](LICENSE) për detaje.

## 📞 Kontakti

- **Email**: info@carwise.ai
- **Website**: https://carwise.ai

---

**CarWise.ai** - Diagnostikimi i makinave të bërë inteligjent! 🚗✨