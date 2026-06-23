# DO NOT UPLOAD TO SERVER

All files/folders in this directory (`noDeploy/`) must never be FTP'd to production.

Additional root-level files that must also stay off the server:

| File / Folder         | Reason                                              |
|-----------------------|-----------------------------------------------------|
| `CLAUDE.md`           | AI dev instructions — internal only                 |
| `README.md`           | Dev documentation                                   |
| `composer.json`       | Exposes dependency names/versions                   |
| `composer.lock`       | Exposes exact package versions                      |
| `.gitignore`          | Dev tooling file                                    |
| `.env`                | Credentials (gitignored, never commit)              |
| `config/db.php`       | DB credentials (gitignored)                         |
| `config/service-account.json` | Firebase key (gitignored)                  |
| `vendor/`             | Rebuild with `composer install --no-dev` on server  |
| `vendor/bin/`         | CLI tools — not needed on server                    |
| `logs/`               | Gitignored — local dev logs only                    |
| `docs/`               | Gitignored — local dev notes only                   |
| `noDeploy/`           | This folder itself                                  |

## Contents of noDeploy/

```
noDeploy/
  database.sql                    ← Full DB dump (use for local restore only)
  migrations/
    migration_2026_06_21.sql      ← Already applied
    migration_2026_06_22.sql      ← Already applied
  scripts/
    test.php                      ← One-time migration script (already executed)
  security/
    RUNBOOK.md                    ← Ops runbook (sensitive)
    SECURITY_AUDIT_REPORT.md      ← Audit report — reveals past vulnerabilities
    backups/                      ← Backup scripts (run locally or on DB server)
    configs/                      ← Apache/MySQL/PHP hardening reference configs
```
