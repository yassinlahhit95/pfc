# Character Encoding Standards — AulaPro

## CRITICAL: UTF-8 Encoding Requirement

All data in this project MUST be UTF-8 encoded. This includes:
- Database character set: `utf8mb4`
- Database collation: `utf8mb4_unicode_ci`
- PHP files: UTF-8 without BOM
- SQL dumps: UTF-8 without BOM
- Configuration files: UTF-8

**Failure to maintain UTF-8 will corrupt Spanish accented characters:**
- ñ → %% or other garbled characters
- á, é, í, ó, ú → corrupted
- ü → corrupted

---

## Root Cause: database.sql UTF-16 Corruption

**Problem:** The `noDeploy/database.sql` was saved in UTF-16 encoding instead of UTF-8, causing:
- Tildes (ñ) to appear as `%%` or corrupted characters
- Student names like "Peña" → "Pe%%a"
- Subject names like "Educación" → "Educaci%%n"

**Solution:** The file has been converted to UTF-8 and validated.

---

## Preventing Tilde Corruption

### 1. **Database Exports**
Always export with explicit UTF-8 settings:

```bash
# Correct (UTF-8)
mysqldump --default-character-set=utf8mb4 database_name > backup.sql

# WRONG (will corrupt UTF-8 text)
mysqldump database_name > backup.sql
```

Use the provided script:
```bash
cd noDeploy/
bash dump-database.sh
```

### 2. **PHP Code**
When handling user input with accented characters:

```php
// CORRECT: Ensure UTF-8
require_once __DIR__ . '/../include/EncodingValidator.php';
$safeText = EncodingValidator::sanitizeForDatabase($_POST['nombre']);

// Store safely
$sql = "INSERT INTO estudiantes (nombreEstudiante) VALUES (?)";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $safeText);
mysqli_stmt_execute($stmt);
```

### 3. **Database Operations**
Connection is auto-configured in `modelos/conectar.php`:
```php
mysqli_set_charset($conexion, "utf8mb4");  // ← Always set
```

**Never override this!**

### 4. **Validation Tool**
Check if data is corrupted:

```php
require_once __DIR__ . '/include/EncodingValidator.php';

// Detect corruption
if (EncodingValidator::hasCorruptedTildes($text)) {
    error_log("Corrupted data detected: $text");
}

// Attempt to fix
$fixed = EncodingValidator::fixCorruptedTildes($text);
```

---

## Checking Current State

### Verify database.sql encoding:
```bash
file noDeploy/database.sql
# Should output: "UTF-8 Unicode ... text"
# NOT: "Unicode text, UTF-16, little-endian"
```

### Validate database connection:
```php
$con = obtenerConexion();
$result = mysqli_query($con, "SELECT @@character_set_client, @@character_set_connection");
$charset = mysqli_fetch_assoc($result);
var_dump($charset);
// Should show: utf8mb4 / utf8mb4
```

### Find corrupted data in database:
```sql
-- Find any corrupted tilde patterns
SELECT * FROM estudiantes WHERE nombreEstudiante LIKE '%/%' OR nombreEstudiante LIKE '%¡%';
SELECT * FROM ciclos WHERE nombreCiclo LIKE '%/%' OR nombreCiclo LIKE '%¡%';
```

---

## Migration Guide: Fixing Corrupted Database

If you have corrupted data from UTF-16 imports:

### Option A: Reload from backup
```bash
# Use the corrected database.sql (UTF-8 encoded)
mysql -u root pfc < noDeploy/database.sql
```

### Option B: Fix in-place
```php
require_once __DIR__ . '/include/EncodingValidator.php';

// Find and fix corrupted names
$con = obtenerConexion();
$result = mysqli_query($con, "SELECT idEstudiante, nombreEstudiante FROM estudiantes");

while ($row = mysqli_fetch_assoc($result)) {
    if (EncodingValidator::hasCorruptedTildes($row['nombreEstudiante'])) {
        $fixed = EncodingValidator::fixCorruptedTildes($row['nombreEstudiante']);
        $sql = "UPDATE estudiantes SET nombreEstudiante = ? WHERE idEstudiante = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $fixed, $row['idEstudiante']);
        mysqli_stmt_execute($stmt);
        echo "Fixed: {$row['nombreEstudiante']} → $fixed\n";
    }
}
```

---

## Deployment Checklist

Before deploying to production:

- [ ] Verify `noDeploy/database.sql` encoding: `file database.sql` shows UTF-8
- [ ] Remove BOM if present: `head -c 3 database.sql | xxd` should NOT show `EF BB BF`
- [ ] Test database import on fresh database
- [ ] Check for corrupted data: Query for `%%` or `%¡` patterns
- [ ] PHP files are UTF-8 without BOM (IDE setting)
- [ ] No non-ASCII characters in database.sql comments (they may not be preserved)

---

## References

- MySQL Character Sets: https://dev.mysql.com/doc/refman/8.0/en/charset.html
- UTF-8 and HTML: https://www.w3.org/International/questions/qa-what-is-encoding
- Common Encoding Issues: https://www.fileformat.info/info/unicode/category/index.htm

---

## Questions?

If you encounter tilde corruption:
1. Check `EncodingValidator::validateDumpFile()` output
2. Review error logs for "Database charset mismatch" warnings
3. Verify database.sql file encoding with `file` command
4. Use `EncodingValidator::hasCorruptedTildes()` to identify affected fields
