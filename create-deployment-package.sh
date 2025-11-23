#!/bin/bash

# CarWise.ai - Create Deployment Package
# Script për të krijuar paketën e deployment-it

echo "📦 CarWise.ai - Creating Deployment Package"
echo "==========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Create deployment directory
DEPLOYMENT_DIR="carwise-deployment-$(date +%Y%m%d-%H%M%S)"
print_status "Duke krijuar deployment directory: $DEPLOYMENT_DIR"
mkdir -p "$DEPLOYMENT_DIR"

# Copy application files
print_status "Duke kopjuar application files..."
cp -r app "$DEPLOYMENT_DIR/"
cp -r bootstrap "$DEPLOYMENT_DIR/"
cp -r config "$DEPLOYMENT_DIR/"
cp -r database "$DEPLOYMENT_DIR/"
cp -r public "$DEPLOYMENT_DIR/"
cp -r resources "$DEPLOYMENT_DIR/"
cp -r routes "$DEPLOYMENT_DIR/"
cp -r storage "$DEPLOYMENT_DIR/"

# Copy configuration files
cp artisan "$DEPLOYMENT_DIR/"
cp composer.json "$DEPLOYMENT_DIR/"
cp composer.lock "$DEPLOYMENT_DIR/"
cp package.json "$DEPLOYMENT_DIR/"
cp package-lock.json "$DEPLOYMENT_DIR/"
cp tailwind.config.js "$DEPLOYMENT_DIR/"
cp vite.config.js "$DEPLOYMENT_DIR/"
cp phpunit.xml "$DEPLOYMENT_DIR/"

# Copy deployment files
cp deploy-production.sh "$DEPLOYMENT_DIR/"
cp deploy-docker.sh "$DEPLOYMENT_DIR/"
cp production.env "$DEPLOYMENT_DIR/"
cp docker-compose.production.yml "$DEPLOYMENT_DIR/"
cp Dockerfile.production "$DEPLOYMENT_DIR/"

# Copy documentation
cp DEVELOPMENT_GUIDE_ALBANIAN.md "$DEPLOYMENT_DIR/"
cp DEPLOYMENT_GUIDE.md "$DEPLOYMENT_DIR/"
cp PRODUCTION_READY.md "$DEPLOYMENT_DIR/"

# Create deployment instructions
print_status "Duke krijuar deployment instructions..."
cat > "$DEPLOYMENT_DIR/DEPLOYMENT_INSTRUCTIONS.md" << 'EOF'
# 🚀 CarWise.ai - Deployment Instructions

## Quick Start

### Option 1: Traditional Deployment
```bash
# 1. Konfiguroni .env file
cp production.env .env
nano .env

# 2. Ekzekutoni deployment
chmod +x deploy-production.sh
./deploy-production.sh
```

### Option 2: Docker Deployment
```bash
# 1. Konfiguroni .env file
cp production.env .env
nano .env

# 2. Ekzekutoni Docker deployment
chmod +x deploy-docker.sh
./deploy-docker.sh
```

## Requirements

### Server Requirements:
- PHP 8.2+
- MySQL 8.0+
- Redis 6.0+
- Node.js 18+
- Nginx 1.18+

### For Docker:
- Docker 20.10+
- Docker Compose 2.0+

## Configuration

### Required Environment Variables:
- APP_URL=https://yourdomain.com
- DB_DATABASE=carwise_production
- DB_USERNAME=your_db_user
- DB_PASSWORD=your_secure_password
- OPENAI_API_KEY=your_openai_key (or other AI provider)

### Optional but Recommended:
- REDIS_PASSWORD=your_redis_password
- MAIL_HOST=your_smtp_host
- MAIL_USERNAME=your_email
- MAIL_PASSWORD=your_email_password

## Support

For detailed instructions, see:
- DEVELOPMENT_GUIDE_ALBANIAN.md (Albanian)
- DEPLOYMENT_GUIDE.md (English)
- PRODUCTION_READY.md (Status)

## Quick Commands

```bash
# Health check
php artisan health:check

# View logs
tail -f storage/logs/laravel.log

# Restart services
sudo systemctl restart nginx php8.2-fpm redis

# Docker commands
docker-compose -f docker-compose.production.yml ps
docker-compose -f docker-compose.production.yml logs -f
```
EOF

# Create .gitignore for deployment
print_status "Duke krijuar .gitignore për deployment..."
cat > "$DEPLOYMENT_DIR/.gitignore" << 'EOF'
# Laravel
/vendor/
/node_modules/
/storage/logs/*
/storage/framework/cache/*
/storage/framework/sessions/*
/storage/framework/views/*
/bootstrap/cache/*

# Environment
.env
.env.local
.env.production

# IDE
.vscode/
.idea/
*.swp
*.swo

# OS
.DS_Store
Thumbs.db

# Logs
*.log

# Temporary files
*.tmp
*.temp
EOF

# Create deployment checklist
print_status "Duke krijuar deployment checklist..."
cat > "$DEPLOYMENT_DIR/DEPLOYMENT_CHECKLIST.md" << 'EOF'
# ✅ CarWise.ai Deployment Checklist

## Pre-Deployment
- [ ] Server është gati (PHP 8.2+, MySQL 8.0+, Redis 6.0+)
- [ ] Domain name është konfiguruar
- [ ] SSL certificate është gati
- [ ] AI API keys janë marrë
- [ ] Email service është konfiguruar

## Configuration
- [ ] .env file është konfiguruar
- [ ] Database credentials janë vendosur
- [ ] AI API keys janë vendosur
- [ ] Email settings janë konfiguruar
- [ ] Domain URL është vendosur

## Deployment
- [ ] Dependencies janë instaluar
- [ ] Assets janë ndërtuar
- [ ] Database migrations janë ekzekutuar
- [ ] Permissions janë vendosur
- [ ] Services janë restartuar

## Testing
- [ ] Website është accessible
- [ ] User registration funksionon
- [ ] AI diagnosis funksionon
- [ ] Email notifications funksionon
- [ ] All features janë testuar

## Post-Deployment
- [ ] SSL certificate është aktiv
- [ ] Monitoring është setup
- [ ] Backup strategy është vendosur
- [ ] Error logs janë kontrolluar
- [ ] Performance është optimizuar

## Launch
- [ ] Final testing është përfunduar
- [ ] Team është informuar
- [ ] Users janë notifikuar
- [ ] 🚀 LAUNCHED! 🎉
EOF

# Create quick start script
print_status "Duke krijuar quick start script..."
cat > "$DEPLOYMENT_DIR/quick-start.sh" << 'EOF'
#!/bin/bash

echo "🚀 CarWise.ai - Quick Start"
echo "=========================="

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Konfiguroni .env file-in..."
    cp production.env .env
    echo "✅ .env file u krijua. Hapni dhe konfiguroni vlerat e nevojshme."
    echo "💡 Shikoni DEPLOYMENT_INSTRUCTIONS.md për më shumë informacion."
    exit 0
fi

# Check if .env is configured
if grep -q "CHANGE_THIS_TO" .env; then
    echo "⚠️  Ju lutem konfiguroni .env file-in para se të vazhdoni!"
    echo "📖 Shikoni DEPLOYMENT_INSTRUCTIONS.md për më shumë informacion."
    exit 1
fi

echo "🎯 Zgjidhni opsionin e deployment-it:"
echo "1) Traditional Deployment"
echo "2) Docker Deployment"
echo ""
read -p "Shkruani numrin (1 ose 2): " choice

case $choice in
    1)
        echo "🔧 Duke filluar Traditional Deployment..."
        chmod +x deploy-production.sh
        ./deploy-production.sh
        ;;
    2)
        echo "🐳 Duke filluar Docker Deployment..."
        chmod +x deploy-docker.sh
        ./deploy-docker.sh
        ;;
    *)
        echo "❌ Opsion i pavlefshëm!"
        exit 1
        ;;
esac
EOF

chmod +x "$DEPLOYMENT_DIR/quick-start.sh"

# Create README for deployment package
print_status "Duke krijuar README për deployment package..."
cat > "$DEPLOYMENT_DIR/README.md" << EOF
# 🚀 CarWise.ai - Production Deployment Package

## 📋 Përmbajtja e Paketës

Kjo është paketa e plotë për deployment të CarWise.ai në production.

### 📁 Files të Rëndësishëm

- \`quick-start.sh\` - Script për fillim të shpejtë
- \`deploy-production.sh\` - Traditional deployment
- \`deploy-docker.sh\` - Docker deployment
- \`production.env\` - Production configuration
- \`docker-compose.production.yml\` - Docker configuration
- \`Dockerfile.production\` - Docker image

### 📖 Dokumentacioni

- \`DEPLOYMENT_INSTRUCTIONS.md\` - Udhëzime të shkurtra
- \`DEPLOYMENT_CHECKLIST.md\` - Lista e kontrollit
- \`DEVELOPMENT_GUIDE_ALBANIAN.md\` - Udhëzues i plotë (Shqip)
- \`DEPLOYMENT_GUIDE.md\` - Udhëzues i plotë (Anglisht)

## 🚀 Quick Start

1. **Konfiguroni .env file:**
   \`\`\`bash
   cp production.env .env
   nano .env
   \`\`\`

2. **Ekzekutoni quick start:**
   \`\`\`bash
   chmod +x quick-start.sh
   ./quick-start.sh
   \`\`\`

## 📞 Mbështetja

Për mbështetje, shikoni dokumentacionin ose kontaktoni ekipin e zhvillimit.

---
**Data e krijimit:** $(date)
**Version:** Production Ready
EOF

# Create archive
print_status "Duke krijuar archive..."
tar -czf "${DEPLOYMENT_DIR}.tar.gz" "$DEPLOYMENT_DIR"

# Create ZIP archive as well
print_status "Duke krijuar ZIP archive..."
zip -r "${DEPLOYMENT_DIR}.zip" "$DEPLOYMENT_DIR"

# Show package info
print_success "Deployment package u krijua me sukses!"
echo ""
echo "📦 Package Info:"
echo "==============="
echo "Directory: $DEPLOYMENT_DIR"
echo "TAR Archive: ${DEPLOYMENT_DIR}.tar.gz"
echo "ZIP Archive: ${DEPLOYMENT_DIR}.zip"
echo ""
echo "📊 Package Size:"
du -sh "$DEPLOYMENT_DIR"
du -sh "${DEPLOYMENT_DIR}.tar.gz"
du -sh "${DEPLOYMENT_DIR}.zip"
echo ""
echo "📁 Package Contents:"
ls -la "$DEPLOYMENT_DIR"
echo ""
echo "🎯 Next Steps:"
echo "1. Upload package në server-in tuaj"
echo "2. Extract files"
echo "3. Konfiguroni .env file"
echo "4. Ekzekutoni quick-start.sh"
echo "5. Lëshoni në internet!"
echo ""
echo "📖 Për më shumë informacion, shikoni dokumentacionin në package."



