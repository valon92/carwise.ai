#!/bin/bash

# Script për të nisur projektin CarWise.ai

echo "🚀 Duke nisur projektin CarWise.ai..."

# Ndal proceset e vjetra
echo "🛑 Duke ndalur proceset e vjetra..."
killall php node vite 2>/dev/null
./stop-servers.sh 2>/dev/null

# Set environment variable për Vite
export LARAVEL_BYPASS_ENV_CHECK=1

# Nis Laravel server në port 9000 (nëse 8000 nuk funksionon)
echo "📦 Duke nisur Laravel server..."
php artisan serve --port=9000 &
LARAVEL_PID=$!

# Pres pak për Laravel të niset
sleep 3

# Nis Vite dev server
echo "⚡ Duke nisur Vite dev server..."
npm run dev &
VITE_PID=$!

# Pres pak për Vite të niset
sleep 5

echo ""
echo "✅ Serverat u nisën!"
echo ""
echo "🌐 Laravel: http://localhost:9000"
echo "⚡ Vite: http://localhost:5173"
echo ""
echo "💡 Për të ndalur serverat, ekzekuto: ./stop-servers.sh"
echo ""

# Ruaj PID-et në një file
echo $LARAVEL_PID > .server-pids
echo $VITE_PID >> .server-pids
