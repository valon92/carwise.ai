# Price Alerts System Documentation

## Overview

The Price Alerts System is a comprehensive real-time price monitoring and notification system for CarWise.ai that allows users to track price changes, set custom alerts, and receive notifications when prices meet their criteria.

## Features

### 🔔 Alert Types
- **Price Drop Alerts**: Notify when prices decrease by a specified percentage
- **Target Price Alerts**: Notify when prices reach or drop below a target amount
- **Significant Change Alerts**: Notify when prices change dramatically (15%+ by default)
- **Price Increase Alerts**: Notify when prices increase by a specified percentage

### 📊 Price Tracking
- Real-time price monitoring via WebSocket connections
- Price history tracking and visualization
- Price trend indicators (rising, falling, stable)
- Market fluctuation analysis

### 🎯 Smart Notifications
- Browser notifications with permission management
- In-app notification system with priority levels
- Visual indicators on product cards
- Alert management with read/unread status

### 💼 Business Features
- Seasonal pricing adjustments
- Market-based fluctuations
- Promotional pricing with automatic restoration
- Bulk price management

## Frontend Implementation

### Vue.js Composable (`usePriceAlerts.js`)

```javascript
import { usePriceAlerts } from '../composables/usePriceAlerts'

const {
  priceAlerts,           // All user's price alerts
  activePriceAlerts,     // Recent alert notifications
  createPriceAlert,      // Create new alert
  removePriceAlert,      // Remove alert
  updatePartPrice,       // Update price and check alerts
  getPriceTrend,         // Get price trend (rising/falling/stable)
  unreadAlerts,          // Unread alert notifications
  ALERT_TYPES           // Alert type constants
} = usePriceAlerts()
```

### Key Methods

#### Creating Price Alerts
```javascript
// Price drop alert (5% or more)
createPriceAlert(partId, ALERT_TYPES.PRICE_DROP, null, 5)

// Target price alert
createPriceAlert(partId, ALERT_TYPES.TARGET_PRICE, 50.00)

// Significant change alert (15% or more)
createPriceAlert(partId, ALERT_TYPES.SIGNIFICANT_CHANGE, null, 15)
```

#### Price Updates
```javascript
// Update price and trigger alert checks
updatePartPrice(partId, newPrice, oldPrice)

// Listen for price update events
window.addEventListener('priceUpdate', (event) => {
  const { partId, newPrice, oldPrice } = event.detail
  // Handle price update
})
```

### UI Components

#### Price Alert Button
Each product card includes a price alert toggle button:
```vue
<button @click="togglePriceAlert(part)" 
        :class="hasPriceAlert(part.id) ? 'active' : 'inactive'">
  <!-- Bell icon -->
</button>
```

#### Price Trend Indicators
Visual indicators show price trends:
```vue
<div v-if="getPriceTrend(part.id) !== 'stable'">
  <svg v-if="getPriceTrend(part.id) === 'decreasing'" class="text-green-500">
    <!-- Down arrow -->
  </svg>
  <span>{{ getPriceTrend(part.id) === 'decreasing' ? 'Dropping' : 'Rising' }}</span>
</div>
```

#### Alert Notifications Panel
Displays active price alerts:
```vue
<div v-if="activePriceAlerts.length > 0">
  <h3>Price Alerts</h3>
  <div v-for="alert in activePriceAlerts" :key="alert.id">
    <p>{{ alert.message }}</p>
    <button @click="markAlertAsRead(alert.id)">Mark as Read</button>
  </div>
</div>
```

## Backend Implementation

### Laravel Services

#### PriceUpdateService
Handles price updates and business logic:
```php
// Update single price
$priceService->updatePrice($partId, $newPrice, $oldPrice);

// Simulate random changes
$priceService->simulateRandomPriceChanges(5);

// Apply seasonal pricing
$priceService->applySeasonalPricing('winter');

// Set promotional pricing
$priceService->setPromotionalPricing([1, 2, 3], 20, 24); // 20% off for 24 hours
```

### API Endpoints

#### Public Endpoints
```
GET    /api/price/{partId}              - Get current price
GET    /api/price/{partId}/history      - Get price history
GET    /api/price/statistics/overview   - Get price statistics
GET    /api/price/promotions/active     - Get active promotions
POST   /api/price/simulate              - Simulate price changes
POST   /api/price/seasonal              - Apply seasonal pricing
POST   /api/price/market-fluctuations   - Apply market fluctuations
```

#### Protected Endpoints (Authenticated)
```
PUT    /api/price/{partId}              - Update price
POST   /api/price/bulk-update           - Bulk price updates
POST   /api/price/promotions            - Set promotional pricing
POST   /api/price/promotions/restore    - Restore promotional pricing
```

### WebSocket Events

#### PriceUpdated Event
Broadcasts price changes in real-time:
```php
event(new PriceUpdated($partId, $newPrice, $oldPrice));
```

Broadcast payload:
```json
{
  "type": "priceUpdate",
  "payload": {
    "part_id": 123,
    "new_price": 89.99,
    "old_price": 99.99,
    "change": -10.00,
    "change_percentage": -10.01,
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

## Usage Examples

### Setting Up Price Alerts

1. **User clicks price alert button** on a product card
2. **Modal/prompt appears** with alert type options
3. **User selects alert type** and sets parameters
4. **Alert is created** and stored locally
5. **Real-time monitoring begins**

### Price Change Flow

1. **Price update occurs** (manual, automated, or simulated)
2. **PriceUpdateService processes** the change
3. **PriceUpdated event is fired**
4. **WebSocket broadcasts** to connected clients
5. **Frontend receives update** and checks alerts
6. **Matching alerts trigger notifications**

### Notification Types

#### Browser Notifications
- Requires user permission
- Shows outside the browser window
- Clickable to focus the application
- Auto-dismiss for low priority alerts

#### In-App Notifications
- Slide-in notifications from the right
- Color-coded by priority (red=high, orange=medium, blue=low)
- Manual dismiss or auto-dismiss after 8 seconds
- Persistent in the alerts panel

## Configuration

### Alert Settings
```javascript
const alertSettings = {
  enablePriceDropAlerts: true,
  enablePriceIncreaseAlerts: false,
  enableTargetPriceAlerts: true,
  minimumDropPercentage: 5,
  minimumIncreasePercentage: 10,
  checkInterval: 30000, // 30 seconds
  maxHistoryDays: 30
}
```

### Notification Thresholds
```javascript
const thresholds = {
  lowStockThreshold: 5,
  criticalStockThreshold: 2,
  significantChangeThreshold: 15 // percentage
}
```

## Testing

### Simulate Price Changes
```bash
# Simulate random price changes
curl -X POST http://localhost:8000/api/price/simulate \
  -H "Content-Type: application/json" \
  -d '{"count": 10}'

# Apply seasonal pricing
curl -X POST http://localhost:8000/api/price/seasonal \
  -H "Content-Type: application/json" \
  -d '{"season": "winter"}'

# Set promotional pricing
curl -X POST http://localhost:8000/api/price/promotions \
  -H "Content-Type: application/json" \
  -d '{
    "part_ids": [1, 2, 3],
    "discount_percentage": 25,
    "duration_hours": 48
  }'
```

### Frontend Testing
1. Open browser developer tools
2. Navigate to CarParts page
3. Set price alerts on various products
4. Use API endpoints to simulate price changes
5. Observe real-time notifications and UI updates

## Performance Considerations

### Frontend Optimization
- **Debounced price updates** to prevent excessive re-renders
- **Limited alert history** (50 notifications max)
- **Efficient WebSocket connection management**
- **Local storage for persistence**

### Backend Optimization
- **Batch price updates** for better performance
- **Event queuing** for high-volume scenarios
- **Database indexing** on price and timestamp fields
- **Caching** for frequently accessed price data

## Security

### Input Validation
- Price values must be positive numbers
- Alert thresholds within reasonable ranges
- Part ID validation against existing records

### Rate Limiting
- API endpoints have rate limiting
- WebSocket connection limits
- Bulk operation size restrictions

### Authentication
- Protected endpoints require authentication
- User-specific alert management
- Admin-only bulk operations

## Future Enhancements

### Planned Features
- **Email notifications** for price alerts
- **SMS notifications** for critical alerts
- **Price prediction** using machine learning
- **Advanced charting** for price history
- **Alert sharing** between users
- **Price comparison** across multiple suppliers
- **Mobile app integration**

### Analytics
- **Alert effectiveness tracking**
- **Price volatility analysis**
- **User engagement metrics**
- **Market trend reporting**

## Troubleshooting

### Common Issues

#### Notifications Not Working
1. Check browser notification permissions
2. Verify WebSocket connection status
3. Ensure alert settings are enabled
4. Check browser console for errors

#### Price Updates Not Reflecting
1. Verify WebSocket connection
2. Check network connectivity
3. Confirm API endpoint responses
4. Review browser console for errors

#### Performance Issues
1. Clear old alert notifications
2. Reduce alert check frequency
3. Limit number of active alerts
4. Check browser memory usage

### Debug Information
```javascript
// Check WebSocket status
console.log('WebSocket connected:', isConnected.value)

// View active alerts
console.log('Active alerts:', priceAlerts.value)

// Check notification permissions
console.log('Notification permission:', Notification.permission)

// View price history
console.log('Price history:', getPriceHistory(partId, 7))
```

## Conclusion

The Price Alerts System provides a comprehensive solution for real-time price monitoring in CarWise.ai. It combines modern web technologies (WebSockets, Vue.js composables, Laravel events) to deliver a seamless user experience with powerful business features for price management and customer engagement.

