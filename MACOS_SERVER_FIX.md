# 🔧 Zgjidhje për Problemin me Server në macOS

## ❌ Problemi
macOS po bllokon portet me gabimin: `Operation not permitted`

## ✅ Zgjidhje të Mundshme

### 1. **Kontrollo macOS Security Settings**
1. Shko te **System Settings** → **Privacy & Security**
2. Shiko nëse Terminal ose aplikacioni që po përdor ka permissions
3. Nëse ka ndonjë kufizim, lejo ato

### 2. **Përdor Port të Lartë (Recomanduar)**
Nëse portet e ulëta (8000, 8080, etj.) nuk funksionojnë, përdor port më të lartë:

```bash
# Nis Laravel në port 10001
php artisan serve --port=10001

# Nëse Vite nuk funksionon, ndrysho vite.config.js
# Në server.port vendos një port tjetër si 5175
```

### 3. **Përdor Docker (Nëse e ke të instaluar)**
```bash
docker-compose up
```

### 4. **Përdor Laravel Sail**
```bash
./vendor/bin/sail up
```

### 5. **Nis Manualisht në Terminal**
Hap Terminal dhe ekzekuto:

```bash
cd /Users/valonsylejmani/Projekte/carwise.ai
export LARAVEL_BYPASS_ENV_CHECK=1
php artisan serve --port=10001
```

Në një terminal tjetër:
```bash
cd /Users/valonsylejmani/Projekte/carwise.ai
export LARAVEL_BYPASS_ENV_CHECK=1
npm run dev
```

Pastaj hap browser në: **http://localhost:10001**

### 6. **Kontrollo nëse ka Firewall ose Antivirus**
- Kontrollo nëse macOS Firewall është aktiv
- Kontrollo nëse ka antivirus që po bllokon portet

## 🚀 Komanda e Shpejtë

```bash
# Ndal të gjitha proceset
./stop-servers.sh

# Nis serverat manualisht
export LARAVEL_BYPASS_ENV_CHECK=1
php artisan serve --port=10001 &
npm run dev &
```

## 📝 Shënim
Nëse asnjëra nga këto zgjidhje nuk funksionon, problemi mund të jetë me macOS security settings që kërkojnë ndryshime në System Preferences.
