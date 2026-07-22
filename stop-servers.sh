#!/bin/bash

# Script për të ndalur të gjitha serverat që po përdorin portin 8000

echo "🛑 Duke ndalur serverat në port 8000..."

# Ndal proceset PHP
killall php 2>/dev/null && echo "✅ PHP processes killed" || echo "⚠️  No PHP processes found"

# Ndal proceset Node
killall node 2>/dev/null && echo "✅ Node processes killed" || echo "⚠️  No Node processes found"

# Ndal proceset Vite
killall vite 2>/dev/null && echo "✅ Vite processes killed" || echo "⚠️  No Vite processes found"

# Ndal proceset që përdorin portin 8000 (nëse lsof funksionon)
if command -v lsof &> /dev/null; then
    PIDS=$(lsof -ti:8000 2>/dev/null)
    if [ ! -z "$PIDS" ]; then
        echo "🔍 Found processes on port 8000: $PIDS"
        kill -9 $PIDS 2>/dev/null && echo "✅ Processes on port 8000 killed" || echo "⚠️  Could not kill processes"
    else
        echo "✅ Port 8000 is free"
    fi
fi

echo ""
echo "✅ Përfunduar! Porti 8000 duhet të jetë i lirë tani."
echo "💡 Tani mund të nisni serverin me: php artisan serve"
