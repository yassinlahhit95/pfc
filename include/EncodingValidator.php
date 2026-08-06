<?php
declare(strict_types=1);

/**
 * Valida y corrige problemas de codificación en toda la aplicación.
 * Evita la corrupción de tildes (ñ → caracteres corruptos) causada por incompatibilidades de codificación.
 */
class EncodingValidator {
    /**
     * Asegura que todas las cadenas de la base de datos estén correctamente codificadas en UTF-8.
     * Llamar aquí al insertar/actualizar campos de texto que puedan contener caracteres acentuados.
     */
    public static function sanitizeForDatabase(string $input): string {
        // Detectar si la cadena ya está en UTF-8; si no, convertirla
        if (!mb_detect_encoding($input, 'UTF-8', true)) {
            $input = mb_convert_encoding($input, 'UTF-8');
        }
        return $input;
    }

    /**
     * Valida que el charset de la conexión a la base de datos sea UTF-8MB4.
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
            error_log("⚠ Se detectó un charset de base de datos incorrecto. Charsets: " . json_encode($charset));
        }
        return $valid;
    }

    /**
     * Detecta si una cadena tiene tildes corruptas (bug de codificación habitual).
     * Ejemplos de patrones corruptos: "niño" → "ni%%o" o "niño" → "ni%¡o"
     */
    public static function hasCorruptedTildes(string $text): bool {
        // Comprueba patrones de corrupción habituales
        $corruptedPatterns = [
            '%%',      // ñ corrompida como %%
            '%¡',      // ñ corrompida como %¡
            '├',       // error de decodificación UTF-8
            '┌',       // error de decodificación UTF-8
            '┤',       // error de decodificación UTF-8
            '├│',      // error de doble codificación
            '├®',      // error de doble codificación
        ];

        foreach ($corruptedPatterns as $pattern) {
            if (strpos($text, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Corrige corrupciones habituales de tildes en un texto.
     * AVISO: esto es una corrección best-effort y puede no cubrir todos los casos.
     */
    public static function fixCorruptedTildes(string $text): string {
        // Patrones corruptos habituales y su corrección
        $fixes = [
            'ni%%o' => 'niño',
            'se%%or' => 'señor',
            'a%%o' => 'año',
            'lecci%%n' => 'lección',
            '%%' => 'ñ',  // último recurso
            // Corrupciones por doble codificación UTF-8 (de importaciones UTF-16)
            'Configuraciâ€™n' => 'Configuración',
            'acadâ€™mica' => 'académica',
        ];

        foreach ($fixes as $corrupted => $correct) {
            $text = str_replace($corrupted, $correct, $text);
        }

        return $text;
    }

    /**
     * Valida que el fichero de volcado database.sql esté correctamente codificado en UTF-8.
     * Devuelve un array con los resultados de la validación.
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

        // Comprueba la codificación del archivo
        $encoding = shell_exec("file -b " . escapeshellarg($filePath));
        $result['valid_encoding'] = (strpos($encoding, 'UTF-8') !== false);
        $result['has_bom'] = (strpos($encoding, 'BOM') !== false);

        // Comprueba patrones de corrupción en las primeras 1000 líneas
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
