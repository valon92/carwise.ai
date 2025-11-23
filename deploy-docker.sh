#!/bin/bash

# CarWise.ai Docker Production Deployment Script
# Script për deployment me Docker

echo "🐳 CarWise.ai - Docker Production Deployment"
echo "=========================================="

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

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker nuk është instaluar! Instaloni Docker para se të vazhdoni."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose nuk është instaluar! Instaloni Docker Compose para se të vazhdoni."
    exit 1
fi

# Check if .env file exists
if [ ! -f .env ]; then
    print_warning "File .env nuk ekziston. Kopjo production.env në .env"
    if [ -f production.env ]; then
        cp production.env .env
        print_success "Kopjuar production.env në .env"
    else
        print_error "production.env nuk ekziston!"
        exit 1
    fi
fi

# Check if .env is configured
if grep -q "CHANGE_THIS_TO" .env; then
    print_warning "Ju lutem konfiguroni .env file-in para se të vazhdoni!"
    print_status "Hapni .env file-in dhe plotësoni të gjitha vlerat e nevojshme."
    exit 1
fi

print_status "Duke filluar Docker deployment-in..."

# 1. Stop existing containers
print_status "Duke ndalur containers ekzistues..."
docker-compose -f docker-compose.production.yml down

# 2. Remove old images (optional)
read -p "A doni të fshini imazhet e vjetra? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Duke fshirë imazhet e vjetra..."
    docker system prune -f
fi

# 3. Build and start containers
print_status "Duke ndërtuar dhe startuar containers..."
docker-compose -f docker-compose.production.yml up -d --build

if [ $? -ne 0 ]; then
    print_error "Gabim në ndërtimin e containers!"
    exit 1
fi

# 4. Wait for services to be ready
print_status "Duke pritur që services të jenë gati..."
sleep 30

# 5. Run database migrations
print_status "Duke ekzekutuar database migrations..."
docker-compose -f docker-compose.production.yml exec app php artisan migrate --force

if [ $? -ne 0 ]; then
    print_error "Gabim në database migrations!"
    exit 1
fi

# 6. Seed database (optional)
read -p "A doni të seed database-in? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Duke seed database-in..."
    docker-compose -f docker-compose.production.yml exec app php artisan db:seed --force
fi

# 7. Set permissions
print_status "Duke vendosur permissions..."
docker-compose -f docker-compose.production.yml exec app chmod -R 755 storage bootstrap/cache

# 8. Health check
print_status "Duke kontrolluar sistemin..."
sleep 10

# Check if containers are running
if docker-compose -f docker-compose.production.yml ps | grep -q "Up"; then
    print_success "Containers janë duke punuar!"
else
    print_error "Disa containers nuk janë duke punuar!"
    docker-compose -f docker-compose.production.yml ps
    exit 1
fi

# 9. Show container status
print_status "Statusi i containers:"
docker-compose -f docker-compose.production.yml ps

# 10. Show logs (last 20 lines)
print_status "Logs e fundit:"
docker-compose -f docker-compose.production.yml logs --tail=20

# 11. Create deployment info
print_status "Duke krijuar deployment info..."
cat > docker-deployment-info.txt << EOF
CarWise.ai Docker Production Deployment
=====================================
Deployment Date: $(date)
Deployment User: $(whoami)
Docker Version: $(docker --version)
Docker Compose Version: $(docker-compose --version)

Containers:
$(docker-compose -f docker-compose.production.yml ps)

Next Steps:
1. Konfiguroni domain-in tuaj
2. Vendosni SSL certificate
3. Konfiguroni AI API keys
4. Testoni të gjitha funksionalitetet
5. Lëshoni në internet!

Commands:
- View logs: docker-compose -f docker-compose.production.yml logs -f
- Stop: docker-compose -f docker-compose.production.yml down
- Restart: docker-compose -f docker-compose.production.yml restart
- Update: docker-compose -f docker-compose.production.yml up -d --build

For support, check the documentation or contact the development team.
EOF

print_success "Docker deployment u përfundua me sukses!"
print_status "Informacioni i deployment-it u ruajt në docker-deployment-info.txt"

# 12. Show next steps
echo ""
echo "🎉 CarWise.ai është gati për production me Docker!"
echo ""
echo "Containers që janë duke punuar:"
docker-compose -f docker-compose.production.yml ps
echo ""
echo "Hapat e ardhshëm:"
echo "1. Konfiguroni domain-in tuaj në .env file"
echo "2. Vendosni SSL certificate"
echo "3. Konfiguroni AI API keys në .env"
echo "4. Testoni aplikacionin"
echo "5. Lëshoni në internet!"
echo ""
echo "Për të parë logs: docker-compose -f docker-compose.production.yml logs -f"
echo "Për të ndalur: docker-compose -f docker-compose.production.yml down"
echo ""
echo "Për më shumë informacion, shikoni dokumentacionin në DEVELOPMENT_GUIDE_ALBANIAN.md"



