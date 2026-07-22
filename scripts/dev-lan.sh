#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

LAN_IP="$(ipconfig getifaddr en0 2>/dev/null || ipconfig getifaddr en1 2>/dev/null || true)"

if [ -z "$LAN_IP" ]; then
    echo "Could not detect LAN IP. Connect Wi-Fi and retry."
    exit 1
fi

APP_URL="http://${LAN_IP}:8001"
VITE_ORIGIN="http://${LAN_IP}:5174"

export APP_URL
export VITE_LAN=true
export VITE_LAN_IP="$LAN_IP"
export VITE_DEV_SERVER_PROXY="$APP_URL"
export VITE_HMR_HOST="$LAN_IP"
export VITE_DEV_SERVER_ORIGIN="$VITE_ORIGIN"
export SANCTUM_STATEFUL_DOMAINS="localhost,localhost:8001,127.0.0.1,127.0.0.1:8001,${LAN_IP},${LAN_IP}:8001,${LAN_IP}:5174"

echo ""
echo "  CarWise LAN dev"
echo "  Mac/iPhone URL: ${APP_URL}"
echo "  Vite network:   ${VITE_ORIGIN}"
echo "  iPhone must use the same Wi-Fi as this Mac."
echo ""

exec npx concurrently -k \
    "php artisan serve --host=0.0.0.0 --port=8001" \
    "npm run dev"
