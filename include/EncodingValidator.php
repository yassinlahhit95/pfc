<?php
declare(strict_types=1);

/**
 * Validates and fixes encoding issues throughout the application.
 * Prevents tilde corruption (ñ → corrupted characters) caused by encoding mismatches.
 */
class EncodingValidator {
    /**
     * Ensure all database strings are properly UTF-8 encoded.
     * Call this when inserting/updating text fields that may contain accented characters.
     */
    public static function sanitizeForDatabase(string $input): string {
        // Detect if string is already UTF-8, if not convert it
        if (!mb_detect_encoding($input, 'UTF-8', true)) {
            $input = mb_convert_encoding($input, 'UTF-8');
        }
        return $input;
    }

    /**
     * Validate database connection charset is UTF-8MB4.
     */
    public static function validateConnection(): bool {
        $con = obtenerConexion();
        $result = mysqli_query($con, "SELECT @@character_set_client, @@character_set_connection, @@character_set_database");
        if (!$result) return false;

        $charset = mysqli_fetch_assoc($result);
        $valid = (
            strpos($charset['@@character_set_client'], 'utf8') !== false &&
            strpos($charset['@@character_set_connection'], 'utf8') !== false &&
            strpos($charset['@@character_set_database'], 'utf8') !== false
        );

        if (!$valid) {
            error_log("⚠ Database charset mismatch detected. Charsets: " . json_encode($charset));
        }
        return $valid;
    }

    /**
     * Detect if a string has corrupted tildes (common encoding bug).
     * Example corrupted patterns: "niño" → "ni%%o" or "niño" → "ni%¡o"
     */
    public static function hasCorruptedTildes(string $text): bool {
        // Check for common corruption patterns
        $corruptedPatterns = [
            '%%',      // ñ corrupted as %%
            '%¡',      // ñ corrupted as %¡
            '├',       // UTF-8 decode error
            '┌',       // UTF-8 decode error
            '┤',       // UTF-8 decode error
            '├│',      // double-encoded error
            '├®',      // double-encoded error
        ];

        foreach ($corruptedPatterns as $pattern) {
            if (strpos($text, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Fix common tilde corruptions in text.
     * WARNING: This is a best-effort fix and may not handle all cases.
     */
    public static function fixCorruptedTildes(string $text): string {
        // Common corrupted patterns and their fixes
        $fixes = [
            'ni%%o' => 'niño',
            'se%%or' => 'señor',
            'a%%o' => 'año',
            'lecci%%n' => 'lección',
            '%%' => 'ñ',  // Last resort
            // UTF-8 double-encoding corruptions (from UTF-16 imports)
            'Configuraciâ€™n' => 'Configuración',
            'acadâ€™mica' => 'académica',
        ];

        foreach ($fixes as $corrupted => $correct) {
            $text = str_replace($corrupted, $correct, $text);
        }

        return $text;
    }

    /**
     * Validate that database.sql dump file is in proper UTF-8 encoding.
     * Returns array with validation results.
     */
    public static function validateDumpFile(string $filePath): array {
        $result = [
            'path' => $filePath,
            'exists' => file_exists($filePath),
            'valid_encoding' => false,
            'has_bom' => false,
            'has_corrupted_data' => false,
            'recommendation' => '',
        ];

        if (!$result['exists']) {
            $result['recommendation'] = 'File not found.';
            return $result;
        }

        // Check file encoding
        $encoding = shell_exec("file -b " . escapeshellarg($filePath));
        $result['valid_encoding'] = (strpos($encoding, 'UTF-8') !== false);
        $result['has_bom'] = (strpos($encoding, 'BOM') !== false);

        // Check for corruption patterns in first 1000 lines
        $lines = file($filePath, FILE_SKIP_EMPTY_LINES);
        $sampleSize = min(1000, count($lines));

        for ($i = 0; $i < $sampleSize; $i++) {
            if (self::hasCorruptedTildes($lines[$i])) {
                $result['has_corrupted_data'] = true;
                break;
            }
        }

        // Recommendations
        if (!$result['valid_encoding']) {
            $result['recommendation'] = 'File is not UTF-8 encoded. Run: iconv -f UTF-16LE -t UTF-8 database.sql > database_fixed.sql';
        } elseif ($result['has_bom']) {
            $result['recommendation'] = 'File has UTF-8 BOM which can cause import issues. Remove with: tail -c +4 database.sql > database_fixed.sql';
        } elseif ($result['has_corrupted_data']) {
            $result['recommendation'] = 'File contains corrupted tilde characters. Database.sql may have been exported with wrong encoding.';
        } else {
            $result['recommendation'] = 'File encoding is valid.';
        }

        return $result;
    }
}
