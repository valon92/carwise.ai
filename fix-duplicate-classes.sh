#!/bin/bash

# Script për të rregulluar controller-et që kanë klasa të deklaruara dy herë

echo "🔧 Duke rregulluar controller-et me klasa të deklaruara dy herë..."

cd /Users/valonsylejmani/Projekte/carwise.ai

# Lista e controller-eve që duhen rregulluar
CONTROLLERS=(
    "app/Http/Controllers/Api/PartsMarketplaceController.php"
)

for CONTROLLER in "${CONTROLLERS[@]}"; do
    if [ -f "$CONTROLLER" ]; then
        echo "Rregulloj $CONTROLLER..."
        
        # Gjej linjën e dytë të deklarimit të klasës
        SECOND_CLASS_LINE=$(grep -n "^class " "$CONTROLLER" | tail -1 | cut -d: -f1)
        
        if [ ! -z "$SECOND_CLASS_LINE" ] && [ "$SECOND_CLASS_LINE" -gt 1 ]; then
            # Gjej linjën para deklarimit të dytë të klasës (duhet të jetë mbyllja e klasës së parë)
            END_LINE=$((SECOND_CLASS_LINE - 1))
            
            # Krijo file të ri me vetëm pjesën e parë
            head -n "$END_LINE" "$CONTROLLER" > "${CONTROLLER}.fixed"
            mv "${CONTROLLER}.fixed" "$CONTROLLER"
            
            echo "✅ $CONTROLLER u rregullua (mbyllur në linjën $END_LINE)"
        fi
    fi
done

echo "✅ Përfunduar!"
