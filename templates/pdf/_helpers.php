<?php
// PDF template helpers — loaded in templates via: <?php include __DIR__ . '/_helpers.php'; ?>

/**
 * Assert a value exists and is not empty; log error if missing
 * @param mixed $value The value to check
 * @param string $field Field name (for logging)
 * @param string $default Fallback value if missing
 * @return string The value (escaped) or default
 */
function pdfAssertField($value, string $field, string $default = '—'): string {
    if ($value === null || $value === '') {
        error_log("[PDF Template] Missing required field: {$field}");
        return $default;
    }
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Safe number formatting for PDF grades/scores
 * @param mixed $value The number to format
 * @param int $decimals Decimal places
 * @param string $ifNull Fallback if not numeric
 * @return string Formatted number or fallback
 */
function pdfFormatNumber($value, int $decimals = 1, string $ifNull = '—'): string {
    return is_numeric($value) ? number_format((float)$value, $decimals) : $ifNull;
}

/**
 * Truncate text to fit PDF cells, preserve UTF-8
 * @param string $text Text to truncate
 * @param int $length Max character length
 * @param string $suffix Truncation marker (default: …)
 * @return string Truncated text
 */
function pdfTruncate(string $text, int $length = 42, string $suffix = '…'): string {
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    return mb_strimwidth($text, 0, $length - mb_strlen($suffix, 'UTF-8'), $suffix, 'UTF-8');
}

/**
 * Format a grade/estado cell (handles special codes like NP, EX, CO)
 * @param mixed $nota The numeric grade
 * @param string $estado The status code (NP, EX, CO, etc)
 * @return string Display value
 */
function pdfFormatGradeCell($nota, ?string $estado): string {
    $especiales = ['NP', 'EX', 'CO'];
    if ($estado && in_array($estado, $especiales, true)) {
        return htmlspecialchars($estado, ENT_QUOTES, 'UTF-8');
    }
    return pdfFormatNumber($nota);
}

/**
 * Determine grade circle CSS class
 * @param mixed $nota The numeric grade
 * @param string $estado Status code
 * @param int $threshold Pass threshold (default: 5)
 * @return string CSS class name
 */
function pdfGradeClass($nota, ?string $estado, int $threshold = 5): string {
    $especiales = ['NP', 'EX', 'CO'];
    if ($estado && in_array($estado, $especiales, true)) {
        return 'pdf-grade-circle especial';
    }
    if (!is_numeric($nota)) {
        return 'pdf-grade-circle vacio';
    }
    $valor = (float)$nota;
    return $valor >= $threshold ? 'pdf-grade-circle aprobado' : 'pdf-grade-circle suspenso';
}

/**
 * Get grade display (number or status code)
 * @param mixed $nota The numeric grade
 * @param string $estado Status code
 * @return string Display value
 */
function pdfGradeDisplay($nota, ?string $estado): string {
    $especiales = ['NP', 'EX', 'CO'];
    if ($estado && in_array($estado, $especiales, true)) {
        return htmlspecialchars($estado, ENT_QUOTES, 'UTF-8');
    }
    return is_numeric($nota) ? (string)(int)$nota : '—';
}
?>
