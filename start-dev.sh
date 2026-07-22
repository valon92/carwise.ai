#!/bin/bash

# Script për të nisur projektin CarWise.ai në development

echo "🚀 Duke nisur projektin CarWise.ai..."

# Ndal proceset e vjetra
echo "🛑 Duke ndalur proceset e vjetra..."
killall php node vite 2>/dev/null || true

# Pres pak
sleep 2

# Set environment variable
export LARAVEL_BYPASS_ENV_CHECK=1

# Nis Laravel server në background
echo "📦 Duke nisur Laravel server në port 8000..."
php artisan serve --port=8000 > /tmp/laravel.log 2>&1 &
LARAVEL_PID=$!

# Pres pak për Laravel të niset
sleep 3

# Nis Vite dev server në background
echo "⚡ Duke nisur Vite dev server..."
npm run dev > /tmp/vite.log 2>&1 &
VITE_PID=$!

# Pres pak për Vite të niset
sleep 5

# Kontrollo nëse serverat po funksionojnë
if curl -s http://127.0.0.1:8000 > /dev/null 2>&1; then
    echo ""
    echo "✅ Serverat u nisën me sukses!"
    echo ""
    echo "🌐 Laravel: http://127.0.0.1:8000"
    echo "⚡ Vite: http://127.0.0.1:5174"
    echo ""
    echo "📋 Logs:"
    echo "   Laravel: tail -f /tmp/laravel.log"
    echo "   Vite: tail -f /tmp/vite.log"
    echo ""
    echo "💡 Për të ndalur serverat, ekzekuto: kill $LARAVEL_PID $VITE_PID"
    echo ""
    
    # Hap browser automatikisht (nëse është macOS)
    if [[ "$OSTYPE" == "darwin"* ]]; then
        open http://127.0.0.1:8000
    fi
    
    # Prit derisa përdoruesi të shtypë Ctrl+C
    echo "Prit... (Shtyp Ctrl+C për të ndalur serverat)"
    wait
else
    echo "❌ Serveri Laravel nuk u nis. Shiko /tmp/laravel.log për detaje."
    echo ""
    echo "💡 Zgjidhje alternative:"
    echo "   1. Kontrollo macOS Security Settings"
    echo "   2. Provo me port tjetër: php artisan serve --port=8001"
    echo "   3. Ose nis manualisht në terminale të ndryshme"
    exit 1
fi
