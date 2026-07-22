#!/bin/bash

# Script për të nisur Laravel server në macOS
# Ekzekuto: ./start-server.sh

echo "🚀 Duke nisur Laravel server..."

cd /Users/valonsylejmani/Projekte/carwise.ai

# Provo portet në rend
PORTS=(8000 8001 8080 8888 9000 10000)

for PORT in "${PORTS[@]}"; do
    echo "Provo port $PORT..."
    php artisan serve --port=$PORT 2>&1 &
    SERVER_PID=$!
    sleep 2
    
    if curl -s http://127.0.0.1:$PORT > /dev/null 2>&1; then
        echo ""
        echo "✅ Serveri u nis me sukses në port $PORT!"
        echo ""
        echo "🌐 Hap browser në: http://127.0.0.1:$PORT"
        echo ""
        echo "💡 Për të ndalur serverin, ekzekuto: kill $SERVER_PID"
        echo ""
        
        # Hap browser automatikisht (nëse është macOS)
        if [[ "$OSTYPE" == "darwin"* ]]; then
            open http://127.0.0.1:$PORT
        fi
        
        # Prit derisa përdoruesi të shtypë Ctrl+C
        echo "Prit... (Shtyp Ctrl+C për të ndalur serverin)"
        wait $SERVER_PID
        exit 0
    else
        kill $SERVER_PID 2>/dev/null || true
    fi
done

echo ""
echo "❌ Asnjë port nuk funksionoi. macOS po bllokon portet."
echo ""
echo "💡 Zgjidhje:"
echo "   1. Kontrollo macOS Security Settings"
echo "   2. Ose nis manualisht: php artisan serve"
echo ""
exit 1
