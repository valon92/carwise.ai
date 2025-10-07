# 🗄️ CarWise.ai Database Backup Strategy

## Overview

Comprehensive database backup and recovery solution për CarWise.ai që siguron integritetin e të dhënave dhe kontinuitetin e biznesit.

## 📋 Backup Types

### 1. Full Backup
- **Kur**: Daily at 3:00 AM
- **Përmbajtja**: Complete database with all data and structure
- **Retention**: 30 days
- **Compression**: gzip level 9
- **Use case**: Primary recovery option

### 2. Incremental Backup (MySQL only)
- **Kur**: Hourly during business hours (9 AM - 6 PM, weekdays)
- **Përmbajtja**: Changes since last backup
- **Retention**: 7 days
- **Compression**: gzip level 9
- **Use case**: Minimal data loss recovery

### 3. Structure Backup
- **Kur**: Weekly on Sundays at 4:00 AM
- **Përmbajtja**: Database schema only (no data)
- **Retention**: 90 days
- **Compression**: gzip level 9
- **Use case**: Schema recovery and development

### 4. Data-Only Backup
- **Kur**: On-demand
- **Përmbajtja**: Data without schema
- **Use case**: Data migration and analysis

## 🛠️ CLI Commands

### Basic Backup Operations

```bash
# Create full backup
php artisan db:backup

# Create specific type backup
php artisan db:backup --type=structure --compression=gzip

# Create backup with custom options
php artisan db:backup --type=full --compression=bzip2 --retention=60 --storage=s3

# No verification (faster)
php artisan db:backup --no-verify

# Keep old backups
php artisan db:backup --no-cleanup
```

### Backup Management

```bash
# List all backups
php artisan backup:list

# Show detailed backup information
php artisan backup:list --detailed

# Show backup statistics
php artisan backup:list --stats

# Export backup list as JSON
php artisan backup:list --json
```

### Database Restore

```bash
# List available backups for restore
php artisan db:restore --list

# Interactive backup selection
php artisan db:restore

# Restore specific backup
php artisan db:restore backup_carwise_full_2025-09-29_03-00-15.gz

# Test restore (doesn't affect production database)
php artisan db:restore backup_file.gz --test

# Force restore without confirmation
php artisan db:restore backup_file.gz --force
```

## 🔧 Configuration

### Environment Variables

```env
# Basic backup settings
BACKUP_ENABLED=true
BACKUP_DEFAULT_TYPE=full
BACKUP_DEFAULT_COMPRESSION=gzip
BACKUP_RETENTION_DAYS=30

# Scheduling
BACKUP_DAILY_ENABLED=true
BACKUP_DAILY_TIME=03:00
BACKUP_WEEKLY_ENABLED=true
BACKUP_WEEKLY_TIME=04:00
BACKUP_HOURLY_ENABLED=false

# Verification
BACKUP_VERIFICATION_ENABLED=true
BACKUP_VERIFICATION_DAILY_CHECK=true
BACKUP_VERIFICATION_TIME=06:00

# Storage
BACKUP_STORAGE_DISK=local
BACKUP_CLOUD_ENABLED=false
BACKUP_CLOUD_DISK=s3
BACKUP_KEEP_LOCAL_COPY=true

# Notifications
BACKUP_NOTIFICATIONS_ENABLED=false
BACKUP_NOTIFY_FAILURE=true
BACKUP_NOTIFY_SUCCESS=false
BACKUP_SLACK_WEBHOOK_URL=
BACKUP_WEBHOOK_URL=

# Security
BACKUP_ENCRYPT=false
BACKUP_MAX_SIZE_MB=1024
BACKUP_TIMEOUT_SECONDS=3600
```

### Database-Specific Settings

#### SQLite
```env
BACKUP_SQLITE_VACUUM=false
```

#### MySQL
```env
MYSQL_BACKUP_SINGLE_TRANSACTION=true
MYSQL_BACKUP_ROUTINES=true
MYSQL_BACKUP_TRIGGERS=true
MYSQL_BACKUP_EVENTS=true
```

#### PostgreSQL
```env
PGSQL_BACKUP_FORMAT=custom
PGSQL_BACKUP_NO_OWNER=true
PGSQL_BACKUP_NO_PRIVILEGES=true
```

## 🔍 API Endpoints

### Admin Dashboard
```http
GET /api/admin/backups/dashboard
```

### List Backups
```http
GET /api/admin/backups?page=1&per_page=10&type=full&days=30
```

### Create Backup
```http
POST /api/admin/backups
Content-Type: application/json

{
  "type": "full",
  "compression": "gzip",
  "verify": true,
  "storage_disk": "local"
}
```

### Download Backup
```http
GET /api/admin/backups/{filename}/download
```

### Verify Backup
```http
GET /api/admin/backups/{filename}/verify
```

### Delete Backup
```http
DELETE /api/admin/backups/{filename}
```

### Statistics
```http
GET /api/admin/backups/statistics
```

## 📊 Monitoring & Health Checks

### Health Score Calculation

The backup health score (0-100) is calculated based on:

- **Recent Backup Age** (30 points)
  - Last backup < 24 hours: Full points
  - Last backup > 24 hours: Deduct points

- **Verification Status** (25 points)
  - All backups verified: Full points
  - Some unverified: Partial deduction
  - No verified backups: No points

- **Backup Diversity** (10 points)
  - Multiple backup types: Full points
  - Single type only: Deduction

- **Retention Coverage** (10 points)
  - >= 7 backups: Full points
  - < 7 backups: Deduction

- **Compression Usage** (5 points)
  - All compressed: Full points
  - Some uncompressed: Deduction

- **Error Trends** (20 points)
  - Stable error rates: Full points
  - Increasing errors: Deductions

### Health Status Levels

| Score | Status | Description |
|-------|--------|-------------|
| 95-100 | Excellent | Optimal backup health |
| 80-94 | Good | Minor improvements needed |
| 60-79 | Warning | Issues require attention |
| 0-59 | Critical | Immediate action required |

### Automated Monitoring

```bash
# Daily health check
php artisan schedule:run

# Manual health check
php artisan logs:health-check

# Monitor with alerts
php artisan logs:monitor --alert
```

## 🚨 Alerting & Notifications

### Notification Channels

1. **Email Notifications**
   - Admin email alerts
   - Backup success/failure reports
   - Daily health summaries

2. **Slack Integration**
   - Real-time backup status
   - Formatted alerts with details
   - Channel-specific notifications

3. **Webhook Notifications**
   - Custom endpoint integration
   - JSON payload with full details
   - Third-party system integration

### Notification Events

- ✅ **backup_successful**: Backup completed successfully
- ❌ **backup_failed**: Backup operation failed
- ⚠️ **verification_failed**: Backup verification failed
- 🗑️ **old_backup_deleted**: Old backups cleaned up
- 📊 **daily_health_report**: Daily system health summary

### Sample Slack Message

```json
{
  "username": "CarWise.ai Backup Bot",
  "icon_emoji": ":floppy_disk:",
  "attachments": [
    {
      "color": "good",
      "title": "✅ Database Backup Successful",
      "fields": [
        {"title": "Filename", "value": "backup_carwise_full_2025-09-29_03-00-15.gz", "short": true},
        {"title": "Size", "value": "12.5 MB", "short": true},
        {"title": "Duration", "value": "2.34s", "short": true},
        {"title": "Verification", "value": "✅ Passed", "short": true}
      ],
      "footer": "CarWise.ai | Production"
    }
  ]
}
```

## 🔐 Security Considerations

### File Permissions
```bash
# Backup directory permissions
chmod 750 storage/backups/
chown www-data:www-data storage/backups/

# Backup file permissions
chmod 640 storage/backups/database/*
```

### Encryption (Optional)
- AES-256 encryption for sensitive data
- Separate encryption key management
- Encrypted storage support

### Access Control
- Admin-only backup operations
- API authentication required
- Audit logging of all backup activities

## 📈 Storage Management

### Local Storage Structure
```
storage/backups/
├── database/
│   ├── backup_carwise_full_2025-09-29_03-00-15.gz
│   ├── backup_carwise_structure_2025-09-26_04-00-00.gz
│   └── backup_carwise_incremental_2025-09-29_14-00-00.gz
└── metadata/
    ├── backup_carwise_full_2025-09-29_03-00-15.json
    ├── backup_carwise_structure_2025-09-26_04-00-00.json
    └── backup_carwise_incremental_2025-09-29_14-00-00.json
```

### Cloud Storage Support
- Amazon S3 integration
- Google Cloud Storage
- Azure Blob Storage
- Local copy retention option

### Cleanup Strategy
- Automatic cleanup based on retention policy
- Manual cleanup commands
- Storage usage monitoring
- Archive old backups to cold storage

## 🚀 Disaster Recovery

### Recovery Time Objectives (RTO)

| Backup Type | RTO | Use Case |
|-------------|-----|----------|
| Full Backup | 15-30 minutes | Complete system recovery |
| Incremental | 5-10 minutes | Recent data recovery |
| Structure | 5 minutes | Schema-only recovery |

### Recovery Point Objectives (RPO)

| Scenario | RPO | Strategy |
|----------|-----|----------|
| Normal Operations | 24 hours | Daily full backups |
| Business Hours | 1 hour | Hourly incremental backups |
| Critical Operations | 15 minutes | Real-time replication |

### Recovery Procedures

1. **Assess the situation**
   - Determine data loss extent
   - Identify last known good state
   - Check backup integrity

2. **Select recovery strategy**
   - Full restore for complete loss
   - Incremental restore for recent data
   - Point-in-time recovery if supported

3. **Execute recovery**
   - Stop application services
   - Restore database from backup
   - Verify data integrity
   - Restart services

4. **Post-recovery validation**
   - Application functionality tests
   - Data consistency checks
   - Performance monitoring

## 📋 Best Practices

### 🎯 Backup Strategy
1. **Follow 3-2-1 Rule**: 3 copies, 2 different media, 1 offsite
2. **Test Regularly**: Monthly restore tests
3. **Monitor Continuously**: Automated health checks
4. **Document Everything**: Recovery procedures and contacts

### 🔧 Performance Optimization
1. **Schedule wisely**: Avoid peak hours
2. **Use compression**: Save storage space
3. **Incremental backups**: Reduce backup time
4. **Parallel processing**: Multiple backup streams

### 🛡️ Security
1. **Encrypt sensitive backups**
2. **Secure backup storage**
3. **Audit backup access**
4. **Rotate encryption keys**

### 📊 Monitoring
1. **Set up alerts** for backup failures
2. **Monitor storage usage**
3. **Track backup performance**
4. **Review health reports**

## 🆘 Troubleshooting

### Common Issues

#### Backup Fails with "Permission Denied"
```bash
# Fix permissions
sudo chown -R www-data:www-data storage/backups/
sudo chmod -R 755 storage/backups/
```

#### MySQL Backup Timeout
```env
# Increase timeout
BACKUP_TIMEOUT_SECONDS=7200
```

#### Large Database Performance
```bash
# Use incremental backups
php artisan db:backup --type=incremental

# Compress with lower level for speed
php artisan db:backup --compression=gzip
```

#### Verification Failures
```bash
# Check backup file integrity
php artisan backup:list --detailed

# Re-run verification
curl -X GET /api/admin/backups/{filename}/verify
```

#### Disk Space Issues
```bash
# Clean old backups
php artisan db:backup --retention=7

# Move to cloud storage
php artisan db:backup --storage=s3 --retention=90
```

### Debug Commands

```bash
# Check backup configuration
php artisan config:show backup

# View backup logs
tail -f storage/logs/backup.log

# Test database connection
php artisan db:show

# Verify cron jobs
php artisan schedule:list
```

## 📞 Support & Contacts

### Emergency Contacts
- **System Administrator**: admin@carwise.ai
- **Database Team**: db-team@carwise.ai
- **DevOps**: devops@carwise.ai

### Documentation Links
- [Laravel Database Documentation](https://laravel.com/docs/database)
- [MySQL Backup Best Practices](https://dev.mysql.com/doc/refman/8.0/en/backup-and-recovery.html)
- [PostgreSQL Backup Documentation](https://www.postgresql.org/docs/current/backup.html)

---

**Last Updated**: September 29, 2025  
**Version**: 1.0  
**Next Review**: October 29, 2025



