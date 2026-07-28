<?php
/**
 * Spanish FP System Configuration
 * Central source of truth for academic structure
 */

class FPSystem {

    // Allowed FP types
    const TIPOS = ['basica', 'medio', 'superior'];

    // Years per type (all are 2 years in Spain)
    const YEARS_PER_TYPE = [
        'basica' => ['1º', '2º'],
        'medio' => ['1º', '2º'],
        'superior' => ['1º', '2º'],
    ];

    // Display labels
    const LABELS = [
        'basica' => 'FP Básica',
        'medio' => 'FP Grado Medio',
        'superior' => 'FP Grado Superior',
    ];

    // Which year has TFG
    const TFG_YEAR = '2º';

    /**
     * Get allowed years for a ciclo type
     */
    public static function getYearsForType($tipoFormacion) {
        return self::YEARS_PER_TYPE[$tipoFormacion] ?? self::YEARS_PER_TYPE['medio'];
    }

    /**
     * Check if a year is TFG year
     */
    public static function isTFGYear($anioEstudio) {
        return $anioEstudio === self::TFG_YEAR;
    }

    /**
     * Get display label for type
     */
    public static function getLabel($tipoFormacion) {
        return self::LABELS[$tipoFormacion] ?? 'Desconocido';
    }

    /**
     * Validate year for type
     */
    public static function isValidYear($tipoFormacion, $anioEstudio) {
        $allowed = self::getYearsForType($tipoFormacion);
        return in_array($anioEstudio, $allowed);
    }
}
?>
