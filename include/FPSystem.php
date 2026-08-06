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

    // Qué curso tiene TFG
    const TFG_YEAR = '2º';

    /**
     * Devuelve los cursos permitidos para un tipo de ciclo
     */
    public static function getYearsForType($tipoFormacion) {
        return self::YEARS_PER_TYPE[$tipoFormacion] ?? self::YEARS_PER_TYPE['medio'];
    }

    /**
     * Comprueba si un curso es el curso de TFG
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
