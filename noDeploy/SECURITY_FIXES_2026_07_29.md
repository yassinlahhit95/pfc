# Security Audit & Fixes — 2026-07-29

## Summary

Comprehensive security audit of logs, cron jobs, and API endpoints. **2 HIGH severity vulnerabilities fixed** + infrastructure improvements for observability.

---

## HIGH SEVERITY (Fixed)

### 1. IDOR in `api/v1/estudiante-prestamos.php`

**Vulnerability:** No authorization check; any authenticated user could query any student's device loans.

**Fix:** Added authorization check at line 21:
```php
if ($usuario['user_type'] === 'estudiante' && $usuario['user_id'] !== $idEstudiante) {
    v1Error('Forbidden', 403, 'access_denied');
}
```

**Impact:** Students can now only query their own loans.

### 2. Mixed Authentication in `api/v1/grupos.php`

**Vulnerability:** Used legacy `$_SESSION` auth while all other v1 endpoints use Bearer tokens.

**Fix:**
- Migrated to Bearer token auth via `v1Auth()`
- Added proper HTTP method validation
- Normalized response to v1 standard format: `{ok: true, grupos: [...]}`

**Impact:** Consistent authentication across API + consistent response shapes.

---

## MEDIUM SEVERITY (Fixed)

### 1. Silent Cron Failures — No Observability

**Problem:** If backup or email processing cron jobs silently failed, there was no way to detect it without manual log checking.

**Solution — Added Cron Execution Logging:**

**Database Schema:**
```sql
CREATE TABLE `cron_execution_log` (
  `job_name` VARCHAR(100) PRIMARY KEY,
  `last_run` DATETIME,
  `last_run_status` ENUM('success', 'failed'),
  `error_message` TEXT,
  `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

**Updated Cron Jobs:**
- `cron/cron_backup.php`: Logs backup status to DB
- `cron/procesar_cola_emails.php`: Logs email queue processing status
- `cron/rotate_logs.php`: NEW — weekly log cleanup (30-day retention)

**Health Check Endpoint:**
- `GET /api/v1/admin/cron-health.php` (admin-only)
- Returns job status, last run time, hours since last execution
- Flags jobs as unhealthy if status is `failed` OR last run > 25 hours ago

**Example response:**
```json
{
  "ok": true,
  "jobs": [
    {
      "job": "cron_backup.php",
      "status": "success",
      "last_run": "2026-07-29 03:15:42",
      "hours_ago": 2,
      "ok": true
    }
  ]
}
```

### 2. Unbounded Log Growth

**Problem:** `logs/activity.log` could grow indefinitely; no rotation policy.

**Solution:**
- Created `cron/rotate_logs.php` — deletes logs older than 30 days
- Recommended cron schedule: `0 2 * * 0` (weekly, Sunday 2am)
- Logs total deletion size for audit trail

### 3. API Error Message Leakage

**Problem:** `api/v1/inventory.php` catch block returned raw exception message, potentially leaking internal details.

**Fix:** Changed line 63 to return safe message instead:
```php
// Before: v1Error($msg ?: 'Could not register the loan.', 500, 'error');
// After:  v1Error('Could not register the loan.', 500, 'error');
```

**Impact:** Exception messages no longer exposed to clients.

---

## LOW SEVERITY (Notes)

### 1. API Documentation

**Status:** ✓ Complete  
**File:** `noDeploy/API_DOCS.md`  
**Update:** Added `GET /api/v1/admin/cron-health.php` documentation

All 35+ endpoints documented with:
- Request/response formats
- Authentication requirements
- Error codes
- Role-based access notes

### 2. Log Rotation

**Status:** ✓ Implemented  
**Script:** `cron/rotate_logs.php`  
**Retention:** 30 days (configurable)  
**Recommended Schedule:** Weekly

---

## Deployment Checklist

### Database Schema
Run once before deployment:
```bash
mysql -h localhost -u user -ppass database < noDeploy/database.sql
```

### Cron Schedule (if not already configured)
Add to crontab:
```bash
# Backup (daily, 3am)
0 3 * * * php /path/to/cron/cron_backup.php

# Email processing (every minute)
* * * * * php /path/to/cron/procesar_cola_emails.php

# Log rotation (weekly, Sunday 2am)
0 2 * * 0 php /path/to/cron/rotate_logs.php
```

### API Endpoint
New admin-only endpoint available immediately:
```
GET /api/v1/admin/cron-health.php
Authorization: Bearer <token>
```

Returns current status of all cron jobs.

---

## Commits

- `9b522eaa` — fix: patch IDOR and mixed auth vulnerabilities in API
- `e34f8fe9` — fix: add comprehensive cron monitoring and observability

---

## Verification

### 1. Check cron logging is working:
```sql
SELECT * FROM cron_execution_log;
```

Should show entries for `cron_backup.php` and `procesar_cola_emails.php` with timestamps.

### 2. Test health endpoint:
```bash
curl -H "Authorization: Bearer <token>" \
  https://aulapro.yassin.agency/api/v1/admin/cron-health.php
```

Should return JSON with job status.

### 3. Verify error handling:
Test an invalid loan request to `estudiante-prestamos.php` — should NOT expose internal details.

---

## Future Monitoring

1. **Check cron health regularly:**
   - Via admin dashboard widget (optional enhancement)
   - Via `cron-health.php` endpoint after adding to admin UI

2. **Log rotation:**
   - Monitor disk usage after first weekly rotation
   - Adjust retention period (currently 30 days) if needed

3. **Error tracking:**
   - Review `cron_execution_log` table for patterns
   - Alert on failed jobs (email to admin if `status = 'failed'`)

---

## Files Modified

### API Security
- `api/v1/estudiante-prestamos.php` — Added IDOR check
- `api/v1/grupos.php` — Migrated to Bearer token auth
- `api/v1/inventory.php` — Fixed error message leakage
- `api/v1/admin/cron-health.php` — NEW endpoint

### Cron Jobs
- `cron/cron_backup.php` — Added heartbeat logging
- `cron/procesar_cola_emails.php` — Added heartbeat logging
- `cron/rotate_logs.php` — NEW log rotation script

### Infrastructure
- `noDeploy/database.sql` — Added `cron_execution_log` table
- `noDeploy/API_DOCS.md` — Updated with cron-health docs
