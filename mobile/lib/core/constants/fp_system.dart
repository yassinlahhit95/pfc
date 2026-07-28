/// Spanish FP System Configuration
/// Central source of truth for academic structure
class FPSystem {
  static const List<String> TIPOS = ['basica', 'medio', 'superior'];

  static const Map<String, String> LABELS = {
    'basica': 'FP Básica',
    'medio': 'FP Grado Medio',
    'superior': 'FP Grado Superior',
  };

  static const String TFG_YEAR = '2º';

  /// Get allowed years for a ciclo type
  static List<String> getYearsForType(String tipo) {
    // All FP types in Spain have exactly 2 years
    return ['1º', '2º'];
  }

  /// Check if a year is TFG year
  static bool isTFGYear(String anio) => anio == TFG_YEAR;

  /// Get display label for type
  static String getLabel(String tipo) {
    return LABELS[tipo] ?? 'Desconocido';
  }

  /// Validate year for type
  static bool isValidYear(String tipo, String anio) {
    return getYearsForType(tipo).contains(anio);
  }
}
