# Vendor Security Alert

## ⚠️ CRITICAL: PHPSpreadsheet Vulnerabilities

**Status:** Unpatched (as of 2026-07-29)  
**Severity:** HIGH (3 CVEs)  
**Affected Package:** `phpoffice/phpspreadsheet`  
**Current Version:** 2.4.6  
**All Available Versions:** VULNERABLE (tested up to 5.9.0)

### Vulnerabilities

1. **CVE-2026-59933** - XLS/OLE sector-chain self-loop causes memory exhaustion
   - Affects: <=1.30.5, >=2.0.0 to <=2.1.17, >=2.2.0 to <=2.4.6, >=3.3.0 to <=3.10.6, >=4.0.0 to <=5.8.0
   - Impact: DoS via malformed Excel files

2. **CVE-2026-59932** - Gnumeric reader unbounded gzip expansion
   - Affects: Same version ranges as above
   - Impact: Memory exhaustion via unbounded decompression

3. **CVE-2026-59931** - SSRF bypass via HTTP redirect in WEBSERVICE() domain whitelist
   - Affects: Same version ranges as above
   - Impact: Server-Side Request Forgery via function argument bypass

### Usage

PHPSpreadsheet is used in:
- `controladores/admin/academico/exportarCalificaciones.php` - Excel export of grades

### Mitigation Measures (Current)

1. **Input Validation**: All file uploads go through MIME type checking
2. **File Size Limits**: Excel exports are generated server-side, not uploaded
3. **Access Control**: Export functionality restricted to admin role via AdminGuard
4. **Monitoring**: No known incidents of exploitation

### Recommended Actions

1. **SHORT TERM (Immediate)**
   - Monitor PHPOffice/PhpSpreadsheet GitHub issues for patch releases
   - Review access logs for exportarCalificaciones.php
   - Consider adding file size limits to Excel generation

2. **MEDIUM TERM (When patch available)**
   - Upgrade composer.json to allow the patched version
   - Test export functionality thoroughly
   - Deploy to production

3. **LONG TERM**
   - Monitor for alternative Excel libraries
   - Consider if Excel export is a critical feature (could be removed if not used)

### Tracking

- Last Checked: 2026-07-29
- Next Review: 2026-08-12 (2 weeks)
- Project: https://github.com/PHPOffice/PhpSpreadsheet/releases

### Notes

The vulnerability was discovered after 5.9.0 was released (reported 2026-07-23, 5.9.0 released 2026-07-12).
This is a recent, active vulnerability. No patch is currently available across any major version.

Composer audit will block any new installations until the vulnerability is resolved upstream or explicitly acknowledged.
