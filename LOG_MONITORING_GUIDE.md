# Log Monitoring & Error Tracking Guide - CarWise.ai

Complete guide for monitoring, analyzing, and managing application logs in production environment.

## 🚀 **Overview**

CarWise.ai includes a comprehensive log monitoring system that provides:

- **Real-time Error Tracking**: Monitor errors as they happen
- **Advanced Log Analysis**: Pattern recognition and trend analysis
- **Automated Alerting**: Critical error notifications
- **Health Monitoring**: System health checks and reports
- **Log Management**: Automatic archiving and cleanup
- **Performance Tracking**: Log-based performance metrics

---

## 📊 **Core Features**

### **1. Real-time Log Monitoring**
- Monitor logs in real-time with customizable time periods
- Automatic error pattern detection
- Critical error alerts and notifications
- Support for multiple log levels and channels

### **2. Advanced Analytics**
- Error rate calculations and trending
- Unique error detection and grouping
- Critical pattern recognition (database, memory, timeout errors)
- Historical error trend analysis

### **3. Automated Health Checks**
- Comprehensive system health scoring
- File accessibility and size monitoring
- Disk space and resource usage tracking
- Automated recommendations for issues

### **4. Log Management**
- Automatic log rotation and archiving
- Configurable retention policies
- Log export functionality (JSON/CSV)
- Cache management for performance

---

## 🛠️ **Implementation**

### **Services**

#### **LogMonitoringService.php**
Main service handling all log monitoring functionality:

```php
// Monitor recent logs
$analysis = LogMonitoringService::monitorRecentLogs(60); // Last 60 minutes

// Get system health
$health = LogMonitoringService::getSystemHealth();

// Get error trends
$trends = LogMonitoringService::getErrorTrends(24); // Last 24 hours

// Archive old logs
$result = LogMonitoringService::archiveLogs(30); // Keep 30 days
```

**Key Methods:**
- `monitorRecentLogs($minutes)` - Analyze recent log entries
- `getSystemHealth()` - Get overall system health status
- `getErrorTrends($hours)` - Get hourly error trends
- `monitorCriticalPatterns()` - Detect critical error patterns
- `archiveLogs($daysToKeep)` - Archive old log files
- `exportLogs($hours, $format)` - Export logs for analysis

### **Console Commands**

#### **1. Log Health Check**
```bash
# Basic health check
php artisan logs:health-check

# Detailed analysis
php artisan logs:health-check --detailed

# JSON output for automation
php artisan logs:health-check --json
```

#### **2. Log Monitoring**
```bash
# Monitor last 60 minutes
php artisan logs:monitor

# Monitor specific period with alerts
php artisan logs:monitor --minutes=120 --alert

# Export results
php artisan logs:monitor --export --format=json
```

#### **3. Log Archiving**
```bash
# Archive logs older than 30 days
php artisan logs:archive

# Keep only 7 days, force execution
php artisan logs:archive --days=7 --force
```

### **API Endpoints**

All endpoints require admin authentication (`/api/admin/logs/`):

#### **Dashboard & Overview**
```http
GET /api/admin/logs/dashboard
# Complete monitoring dashboard with all metrics

GET /api/admin/logs/health
# System health status

GET /api/admin/logs/statistics
# Log statistics and summaries
```

#### **Log Analysis**
```http
GET /api/admin/logs/recent?minutes=60&level=error
# Recent logs with optional filtering

GET /api/admin/logs/trends?hours=24
# Error trends over time

GET /api/admin/logs/patterns
# Critical error patterns

GET /api/admin/logs/search?query=database&level=error
# Search logs with specific criteria
```

#### **Management**
```http
POST /api/admin/logs/archive
# Archive old log files

POST /api/admin/logs/export
# Export logs for analysis

POST /api/admin/logs/clear-cache
# Clear log monitoring cache
```

#### **Real-time Alerts**
```http
GET /api/admin/logs/alerts
# Get current alerts and critical issues
```

---

## 📈 **Monitoring Dashboard**

### **System Health Score**
The health score (0-100) is calculated based on:

- **Error Rate** (40% weight): Current error percentage
- **Critical Patterns** (25% weight): Database, memory, timeout errors
- **File Health** (15% weight): Log file accessibility and size
- **Trends** (10% weight): Error rate changes over time
- **Resources** (10% weight): Disk space and system resources

### **Health Status Levels**
- **🟢 Healthy (80-100)**: System operating normally
- **🟡 Warning (60-79)**: Some issues detected, monitoring recommended
- **🔴 Critical (0-59)**: Immediate attention required

### **Dashboard Metrics**
```json
{
  "system_health": {
    "status": "healthy",
    "score": 95,
    "issues": [],
    "recommendations": ["Continue regular monitoring"]
  },
  "recent_logs": {
    "total_entries": 150,
    "error_rate": 2.5,
    "unique_errors": 3,
    "status": "healthy"
  },
  "error_trends": [
    {
      "hour": "2025-09-29 14:00",
      "errors": 2,
      "warnings": 5,
      "total": 25
    }
  ]
}
```

---

## 🔍 **Error Pattern Detection**

### **Critical Patterns Monitored**

#### **1. Database Errors**
- Connection failures
- Query timeouts
- SQL syntax errors
- Constraint violations

#### **2. Memory Errors**
- Out of memory exceptions
- Memory limit exceeded
- Memory leaks

#### **3. Timeout Errors**
- Script execution timeouts
- HTTP request timeouts
- Database query timeouts

#### **4. Authentication Errors**
- Failed login attempts
- Authorization failures
- Token validation errors

#### **5. API Errors**
- External service failures
- HTTP 5xx errors
- Rate limit exceeded

### **Pattern Analysis Example**
```php
$patterns = LogMonitoringService::monitorCriticalPatterns();

/*
Returns:
{
  "database_errors": {
    "count": 3,
    "recent_entries": [...]
  },
  "memory_errors": {
    "count": 0,
    "recent_entries": []
  },
  "timeout_errors": {
    "count": 1,
    "recent_entries": [...]
  }
}
*/
```

---

## ⚠️ **Alerting System**

### **Alert Triggers**

#### **Critical Alerts**
- Error rate > 10%
- Database connection failures
- Memory exhaustion
- Disk space < 5%
- System health score < 60

#### **Warning Alerts**
- Error rate > 5%
- High memory usage
- Disk space < 20%
- System health score < 80

### **Alert Channels**
1. **Application Logs**: All alerts logged to monitoring channel
2. **Database Storage**: Critical alerts stored for tracking
3. **External Webhooks**: Configurable webhook notifications
4. **Email Notifications**: For critical system issues

### **Alert Configuration**
```php
// In .env
LOG_ALERTS_ENABLED=true
LOG_ALERT_WEBHOOK_URL=https://hooks.slack.com/...
LOG_ALERT_EMAIL=admin@carwise.ai
LOG_CRITICAL_THRESHOLD=10  // Error rate %
LOG_WARNING_THRESHOLD=5    // Error rate %
```

---

## 🗂️ **Log Management**

### **Log Channels**
CarWise.ai uses multiple specialized log channels:

```php
// Performance logs
Log::channel('performance')->info('Slow query detected', $data);

// Security logs  
Log::channel('security')->warning('Failed login attempt', $data);

// API logs
Log::channel('api')->info('API request processed', $data);

// Error logs
Log::channel('errors')->error('Critical system error', $data);

// AI diagnosis logs
Log::channel('ai_diagnosis')->info('Diagnosis completed', $data);

// User activity logs
Log::channel('user_activity')->info('User action', $data);
```

### **Log Rotation**
Automatic log rotation configured in `config/logging.php`:

```php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'days' => env('LOG_DAILY_DAYS', 14),
],
```

### **Archiving Strategy**
- **Daily logs**: Kept for 14 days
- **Error logs**: Kept for 30 days  
- **Security logs**: Kept for 30 days
- **Performance logs**: Kept for 7 days
- **Archive format**: Gzipped files in `storage/logs/archive/`

---

## 📋 **Scheduled Tasks**

### **Automated Monitoring**
Configured in `app/Console/Kernel.php`:

```php
// Monitor logs every 5 minutes with alerts
$schedule->command('logs:monitor --alert')
         ->everyFiveMinutes()
         ->withoutOverlapping();

// Health check every hour
$schedule->command('logs:health-check')
         ->hourly()
         ->withoutOverlapping();

// Archive logs daily at 2 AM
$schedule->command('logs:archive --days=30')
         ->daily()
         ->at('02:00');

// Clear old cache daily at 1 AM  
$schedule->call(function () {
    LogMonitoringService::clearLogCache();
})->daily()->at('01:00');
```

### **Production Schedule**
```bash
# Add to crontab for production
* * * * * cd /path/to/carwise.ai && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔧 **Configuration**

### **Environment Variables**
```bash
# Logging configuration
LOG_CHANNEL=stack
LOG_LEVEL=info
LOG_DAILY_DAYS=14

# Monitoring settings
LOG_MONITORING_ENABLED=true
LOG_HEALTH_CHECK_INTERVAL=60    # minutes
LOG_ARCHIVE_DAYS=30
LOG_EXPORT_ENABLED=false

# Alert settings
LOG_ALERTS_ENABLED=true
LOG_ALERT_WEBHOOK_URL=
LOG_CRITICAL_THRESHOLD=10
LOG_WARNING_THRESHOLD=5

# Performance thresholds
LOG_SLOW_QUERY_THRESHOLD=1000   # milliseconds
LOG_MEMORY_THRESHOLD=50         # MB
LOG_DISK_SPACE_THRESHOLD=20     # percentage
```

### **Logging Channels**
Customize in `config/logging.php`:

```php
'channels' => [
    'production_errors' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack'],  // Log to file and Slack
        'ignore_exceptions' => false,
    ],
    
    'monitoring' => [
        'driver' => 'daily',
        'path' => storage_path('logs/monitoring.log'),
        'level' => 'info',
        'days' => 7,
    ],
],
```

---

## 📊 **Usage Examples**

### **1. Basic Log Monitoring**
```bash
# Check current system health
php artisan logs:health-check

# Monitor last 2 hours with alerts
php artisan logs:monitor --minutes=120 --alert

# Export recent errors for analysis
php artisan logs:monitor --export --format=json
```

### **2. API Integration**
```php
// Get system health in application
$health = app(LogMonitoringController::class)->systemHealth();

// Check for recent critical errors
$alerts = app(LogMonitoringController::class)->alerts();

// Export logs programmatically
$export = LogMonitoringService::exportLogs(24, 'json');
```

### **3. Custom Monitoring**
```php
// Monitor specific error patterns
$patterns = LogMonitoringService::monitorCriticalPatterns();

// Analyze custom time period
$analysis = LogMonitoringService::monitorRecentLogs(180); // 3 hours

// Check error trends
$trends = LogMonitoringService::getErrorTrends(48); // 2 days
```

---

## 🚨 **Troubleshooting**

### **Common Issues**

#### **1. High Error Rate**
```bash
# Check detailed health report
php artisan logs:health-check --detailed

# Search for specific errors
curl -X GET "/api/admin/logs/search?query=database&level=error"

# Export logs for analysis
php artisan logs:monitor --export
```

#### **2. Log File Issues**
```bash
# Check file permissions
ls -la storage/logs/

# Fix permissions
chmod 755 storage/logs/
chmod 644 storage/logs/*.log
```

#### **3. Cache Issues**
```bash
# Clear log monitoring cache
php artisan cache:clear

# Or via API
curl -X POST "/api/admin/logs/clear-cache"
```

#### **4. Disk Space**
```bash
# Archive old logs immediately
php artisan logs:archive --days=7 --force

# Check disk usage
df -h
```

### **Performance Optimization**

#### **1. Large Log Files**
- Enable log rotation: Set `LOG_DAILY_DAYS` to smaller value
- Archive more frequently: Reduce `--days` parameter
- Enable compression in archive process

#### **2. Memory Usage**
- Limit log analysis period for large files
- Use streaming for very large log files
- Implement pagination for API responses

#### **3. Cache Performance**
- Enable Redis for better cache performance
- Tune cache TTL values for your needs
- Monitor cache hit rates

---

## 📈 **Monitoring Best Practices**

### **1. Regular Health Checks**
- Run health checks every hour in production
- Set up automated alerts for critical issues
- Monitor error trends daily

### **2. Log Retention**
- Keep error logs for at least 30 days
- Archive older logs to save disk space
- Export critical logs for compliance

### **3. Alert Management**
- Configure appropriate thresholds for your environment
- Test alert channels regularly
- Document incident response procedures

### **4. Performance Monitoring**
- Track log file sizes and growth
- Monitor system resource usage
- Optimize log rotation schedules

---

## 🔗 **Integration**

### **External Monitoring Services**

#### **Slack Integration**
```php
// In config/logging.php
'slack' => [
    'driver' => 'slack',
    'url' => env('LOG_SLACK_WEBHOOK_URL'),
    'username' => 'CarWise Bot',
    'emoji' => ':boom:',
    'level' => 'critical',
],
```

#### **Webhook Notifications**
```php
// Custom webhook for alerts
if (config('services.monitoring.webhook_url')) {
    Http::post(config('services.monitoring.webhook_url'), [
        'alert_type' => 'critical_error',
        'message' => 'High error rate detected',
        'data' => $alertData
    ]);
}
```

#### **Email Alerts**
```php
// Email critical alerts
Mail::to(config('services.monitoring.alert_email'))
    ->send(new CriticalAlertMail($alertData));
```

---

## 📚 **API Reference**

### **Dashboard Endpoint**
```http
GET /api/admin/logs/dashboard

Response:
{
  "success": true,
  "data": {
    "system_health": {...},
    "recent_logs": {...},
    "critical_patterns": {...},
    "error_trends": [...],
    "updated_at": "2025-09-29T15:00:00Z"
  }
}
```

### **Health Check Endpoint**
```http
GET /api/admin/logs/health

Response:
{
  "success": true,
  "data": {
    "status": "healthy",
    "score": 95,
    "issues": [],
    "recommendations": [...]
  }
}
```

### **Log Search Endpoint**
```http
GET /api/admin/logs/search?query=database&level=error&hours=24

Response:
{
  "success": true,
  "data": {
    "query": "database",
    "level": "error", 
    "period_hours": 24,
    "total_matches": 5,
    "results": [...]
  }
}
```

---

## 🎯 **Key Benefits**

### **For Development**
- **Real-time debugging**: Instantly see errors as they occur
- **Pattern recognition**: Identify recurring issues quickly  
- **Performance insights**: Track slow queries and memory usage
- **Testing support**: Monitor test environments

### **For Operations**
- **Proactive monitoring**: Catch issues before they affect users
- **Automated alerting**: Get notified of critical problems
- **Trend analysis**: Understand system behavior over time
- **Compliance**: Maintain audit logs for security

### **For Business**
- **Uptime optimization**: Reduce system downtime
- **User experience**: Fix issues affecting customers
- **Cost efficiency**: Prevent expensive outages
- **Data insights**: Make informed technical decisions

---

## 📞 **Support & Maintenance**

### **Regular Tasks**
1. **Daily**: Review system health and critical alerts
2. **Weekly**: Analyze error trends and patterns  
3. **Monthly**: Review log retention and archive policies
4. **Quarterly**: Optimize monitoring thresholds and alerts

### **Emergency Procedures**
1. **Critical Errors**: Check `/api/admin/logs/alerts` immediately
2. **System Down**: Run `php artisan logs:health-check --detailed`
3. **High Error Rate**: Export logs and analyze patterns
4. **Disk Full**: Run `php artisan logs:archive --force`

### **Contact Information**
- **Log Monitoring Issues**: Check application logs first
- **System Health**: Use built-in health check tools
- **Emergency**: Follow incident response procedures

---

**🎉 CarWise.ai Log Monitoring is now fully operational!**

*Complete error tracking, health monitoring, and automated alerting for production-ready operations.*



