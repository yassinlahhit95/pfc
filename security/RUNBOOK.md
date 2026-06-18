# AulaPro — Security & Operations Runbook

Operational hardening that lives **outside** the PHP code. Apply on the server.

---

## 1. Cloudflare / WAF

- **Proxy (orange cloud) ON** for the domain; hide the origin IP. Lock the origin
  firewall so it accepts 80/443 **only from Cloudflare IP ranges**.
- **WAF Managed Rules**: enable OWASP Core Ruleset + Cloudflare Managed Ruleset.
- **Custom WAF rules**:
  - Block/Challenge requests to `*/uploads/*.php`, `*.env`, `*.sql`, `/config/*`, `/vendor/*`.
  - Rate-limit `POST /controladores/validacion.php` → 5 req / 5 min / IP.
  - Rate-limit `POST /controladores/admisiones/acciones.php` → 30 req / 5 min / IP.
  - Managed challenge on `/controladores/auth/solicitar_reset.php`.
- **Bot Fight Mode** ON; **Security Level** = High.
- **Full (strict) TLS**; **Always Use HTTPS**; enable **HSTS** in Cloudflare too.
- Upload `Content-Type` enforcement via Transform Rules if available.

> The app already does DB-backed per-IP rate limiting (`include/RateLimiter.php`,
> `login_intentos`). Cloudflare is the outer layer (defense in depth).

---

## 2. Apache / Nginx / PHP / MySQL

Use the templates in `security/configs/`:
`apache-hardening.conf`, `nginx-hardening.conf`, `php-hardening.ini`, `mysql-hardening.cnf`.

Critical, non-negotiable items:
- **No PHP execution under `/public/uploads`** (already enforced by in-repo
  `.htaccess`; the Nginx block does the same with a `location` rule).
- `expose_php=Off`, `display_errors=Off`, secure session cookie flags.
- App DB user with **least privilege** (SELECT/INSERT/UPDATE/DELETE only).
- DB bound to `127.0.0.1`.

---

## 3. Linux host hardening

- `ufw default deny incoming`; allow only 443 (and 22 from your admin IP).
- SSH: `PermitRootLogin no`, key-only auth, `Fail2ban` on sshd + on the web log.
- Auto security updates (`unattended-upgrades`).
- Run PHP-FPM as a low-privilege user; web root **not** writable by that user
  **except** `public/uploads/` and `logs/`.
- File permissions: directories `755`, files `644`, secrets (`.env`, `config/db.php`) `600`.
- AppArmor/SELinux enforcing for the web stack.

---

## 4. Anti-malware / web-shell detection

- **Prevent execution** in upload dirs (done) — kills the #1 web-shell vector.
- Scheduled scan (cron, hourly) with **maldet (Linux Malware Detect)** + **ClamAV**:
  ```
  maldet -a /var/www/aulapro/public/uploads
  clamscan -r --infected /var/www/aulapro/public/uploads
  ```
- Quick grep tripwire for backdoor signatures (cron + alert on hit):
  ```
  grep -rlE 'eval\(|base64_decode\(|gzinflate\(|str_rot13\(|assert\(|system\(|shell_exec\(|passthru\(|`.*\$' \
       /var/www/aulapro/public/uploads /var/www/aulapro --include='*.php'
  ```
- **OSSEC/Wazuh** (host IDS) for file-integrity monitoring (FIM) of the codebase:
  alert on any new/modified `.php` outside a deploy window.

---

## 5. Anti-ransomware & backups

- **Versioned, offline/immutable backups**:
  - DB: `mysqldump` every 6 h → object storage with **versioning + Object Lock**
    (e.g. S3/B2 WORM). Keep 30 daily + 12 monthly.
  - Files (`public/uploads`): `restic`/`borg` to an append-only repo (immutable).
- **Anomaly / mass-encryption detection** (Wazuh FIM rule or inotify watcher):
  alert when > N files change in a short window, or when files gain unusual
  extensions (`.locked`, `.encrypted`) — early ransomware signal.
- **Test restores** monthly. An untested backup is not a backup.
- Recovery procedure documented: isolate host → restore DB+files from last clean
  immutable snapshot → rotate ALL secrets → re-deploy from git.

---

## 6. IDS / IPS

- **Fail2ban** jails: sshd, apache-auth, plus a custom jail reading
  `logs/security.log` (the app's `Logger::security`) to ban IPs on repeated
  `LOGIN_FAILED` / `CSRF_TOKEN_VALIDATION_FAILED`.
- **Wazuh** (or OSSEC) agent for host IDS + FIM + log correlation.
- Network IPS via Cloudflare WAF (managed rules in block mode).

---

## 7. Application-layer follow-ups (code roadmap)

These are **recommended next code changes**, not yet implemented:

1. **Force password change on first login.** Add `must_change_password TINYINT
   DEFAULT 1` to the 4 user tables; set `1` on creation, redirect to a change-
   password screen until cleared. (Random temp passwords are already issued.)
2. **MFA / 2FA** for admin/director: TOTP (RFC 6238) via
   `spomky-labs/otphp`, with backup codes. Enforce for any `idAdmin` session.
3. **Centralized RBAC**: a `Rbac::require('permission')` helper backed by a
   `roles`/`permissions` map, replacing the per-file `Guard` checks with explicit
   permission gates (least privilege per endpoint).
4. **CSP with nonce**: generate `$nonce = base64_encode(random_bytes(16))` per
   request, emit `script-src 'self' 'nonce-$nonce'`, add `nonce="<?=$nonce?>"`
   to every inline `<script>`, and drop `'unsafe-inline'`.
5. **Open-redirect guard**: validate every `header("Location: ...")` target
   against an allowlist of internal paths.
6. **mPDF SSRF/LFI**: keep user HTML escaped (done in `pdf_helper.php`) and set
   mPDF `'allow_remote_images' => false` / restrict `cssselectmedia` so attacker
   data can never trigger `<img src=internal>` fetches.
7. **Reset-token endpoints**: confirm tokens are single-use, hashed at rest, and
   expire (≤ 30 min) — see `modelos/password_reset.php`.

---

## 8. Secret rotation (do now)

The DB password and any API keys that ever sat in the working tree should be
**rotated**, even though `.env`/`config/db.php` were never committed:
- MySQL app user password
- `BREVO_API_KEY`, Firebase keys, `BOLETIN_SECRET`
Set `APP_ENV=production` and `APP_DEBUG=false` in production `.env`.
