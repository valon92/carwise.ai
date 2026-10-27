#!/usr/bin/env bash
# One-time setup on a fresh Ubuntu 22.04/24.04 VPS for carwise.ai
# Run as root: bash scripts/vps-bootstrap.sh

set -euo pipefail

APP_DIR=/var/www/carwise.ai
DEPLOY_USER=${DEPLOY_USER:-deploy}
DOMAIN=${DOMAIN:-carwise.ai}

if [[ $EUID -ne 0 ]]; then
  echo "Run this script as root."
  exit 1
fi

export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get upgrade -y
apt-get install -y software-properties-common curl git unzip nginx mysql-server redis-server ufw
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get install -y \
  php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring \
  php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-intl php8.2-redis

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

if ! id -u "$DEPLOY_USER" >/dev/null 2>&1; then
  adduser --disabled-password --gecos "" "$DEPLOY_USER"
fi
usermod -aG www-data "$DEPLOY_USER"
mkdir -p /home/"$DEPLOY_USER"/.ssh
chmod 700 /home/"$DEPLOY_USER"/.ssh
touch /home/"$DEPLOY_USER"/.ssh/authorized_keys
chmod 600 /home/"$DEPLOY_USER"/.ssh/authorized_keys
chown -R "$DEPLOY_USER":"$DEPLOY_USER" /home/"$DEPLOY_USER"/.ssh

mkdir -p "$APP_DIR"/shared/storage/{app/public,framework/{cache,sessions,views},logs}
mkdir -p "$APP_DIR"/releases
chown -R "$DEPLOY_USER":www-data "$APP_DIR"
chmod -R 775 "$APP_DIR"/shared/storage

mysql -e "CREATE DATABASE IF NOT EXISTS carwise_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS 'carwise'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';"
mysql -e "GRANT ALL PRIVILEGES ON carwise_production.* TO 'carwise'@'localhost'; FLUSH PRIVILEGES;"

ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

cat >/etc/sudoers.d/"$DEPLOY_USER"-carwise <<SUDO
$DEPLOY_USER ALL=(root) NOPASSWD: /bin/systemctl reload nginx, /bin/systemctl reload php8.2-fpm
SUDO
chmod 440 /etc/sudoers.d/"$DEPLOY_USER"-carwise

echo
echo "Bootstrap done."
echo "1. Put the GitHub Actions public key into /home/$DEPLOY_USER/.ssh/authorized_keys"
echo "2. Create $APP_DIR/shared/.env from your production values"
echo "3. Point Namecheap A records for $DOMAIN and www to this server IP"
echo "4. Copy nginx.conf to /etc/nginx/sites-available/$DOMAIN and enable it"
echo "5. After DNS propagates: certbot --nginx -d $DOMAIN -d www.$DOMAIN"
echo "6. Add GitHub secrets and push to main"
