#!/bin/bash

# Script për të nisur projektin CarWise.ai manualisht
# Ekzekuto këtë script në Terminal

echo "🚀 Duke nisur projektin CarWise.ai..."
echo ""

# Ndal proceset e vjetra
echo "🛑 Duke ndalur proceset e vjetra..."
killall php node vite 2>/dev/null
echo "✅ Proceset e vjetra u ndalën"
echo ""

# Set environment variable
export LARAVEL_BYPASS_ENV_CHECK=1

# Build frontend (nëse nuk është bërë)
if [ ! -d "public/build" ]; then
    echo "📦 Duke build-uar frontend..."
    npm run build
    echo "✅ Build u krye"
    echo ""
fi

# Nis Laravel server
echo "🌐 Duke nisur Laravel server në port 10001..."
php artisan serve --port=10001 &
LARAVEL_PID=$!

# Pres pak
sleep 3

# Kontrollo nëse serveri po funksionon
if curl -s http://localhost:10001 > /dev/null 2>&1; then
    echo "✅ Laravel server po funksionon!"
    echo ""
    echo "🌐 Hap browser në: http://localhost:10001"
    echo ""
    echo "💡 Për të ndalur serverin, shtyp Ctrl+C ose ekzekuto: kill $LARAVEL_PID"
    echo ""
    
    # Hap browser automatikisht (nëse është macOS)
    if [[ "$OSTYPE" == "darwin"* ]]; then
        open http://localhost:10001
    fi
    
    # Prit derisa përdoruesi të shtypë Ctrl+C
    wait $LARAVEL_PID
else
    echo "❌ Serveri nuk u nis. Kontrollo gabimet më sipër."
    echo ""
    echo "💡 Zgjidhje alternative:"
    echo "   1. Kontrollo macOS Security Settings"
    echo "   2. Provo me port tjetër: php artisan serve --port=10002"
    echo "   3. Ose nis manualisht: php artisan serve"
    exit 1
fi
