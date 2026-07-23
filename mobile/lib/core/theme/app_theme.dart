import 'package:flutter/material.dart';

/// Mirrors the web app's design tokens (public/css/dashboard.css custom
/// properties) so the mobile app reads as the same product, not a generic
/// Material scaffold. Values copied 1:1 from :root (light) and
/// [data-theme="dark"] (dark) — keep in sync if the web palette changes.
class AppColors {
  // Shared across both themes (not overridden under [data-theme="dark"]).
  static const accent = Color(0xFF4F46E5);
  static const accentInk = Color(0xFFFFFFFF);

  // Light (:root)
  static const bgLight = Color(0xFFEAEDF6);
  static const surfaceLight = Color(0xFFFFFFFF);
  static const surface2Light = Color(0xFFF7F8FC);
  static const surface3Light = Color(0xFFEEF1F8);
  static const borderLight = Color(0x120F172A);
  static const border2Light = Color(0x1F0F172A);
  static const textLight = Color(0xFF0F172A);
  static const dimLight = Color(0xFF51607A);
  static const mutLight = Color(0xFF9AA6BC);

  // Dark ([data-theme="dark"])
  static const bgDark = Color(0xFF06080F);
  static const surfaceDark = Color(0xFF121826);
  static const surface2Dark = Color(0xFF171F30);
  static const surface3Dark = Color(0xFF1D2638);
  static const borderDark = Color(0x12FFFFFF);
  static const border2Dark = Color(0x21FFFFFF);
  static const textDark = Color(0xFFEEF2F9);
  static const dimDark = Color(0xFF9AA7BD);
  static const mutDark = Color(0xFF5D6B83);

  // Status colors (`.texto-estado` chips) — light values
  static const verdeLight = Color(0xFF10B981);
  static const rojoLight = Color(0xFFEF4444);
  static const naranjaLight = Color(0xFFF59E0B);
  static const azulLight = Color(0xFF3B82F6);
  static const amarilloLight = Color(0xFFEAB308);
  static const violetaLight = Color(0xFF8B5CF6);

  // Status colors — dark values
  static const verdeDark = Color(0xFF34D399);
  static const rojoDark = Color(0xFFF87171);
  static const naranjaDark = Color(0xFFFBBF24);
  static const azulDark = Color(0xFF60A5FA);
  static const amarilloDark = Color(0xFFFACC15);
  static const violetaDark = Color(0xFFA78BFA);
}

class AppTheme {
  static ThemeData light() {
    const colorScheme = ColorScheme.light(
      primary: AppColors.accent,
      onPrimary: AppColors.accentInk,
      secondary: AppColors.accent,
      onSecondary: AppColors.accentInk,
      error: AppColors.rojoLight,
      onError: Colors.white,
      surface: AppColors.surfaceLight,
      onSurface: AppColors.textLight,
      surfaceContainerHighest: AppColors.surface3Light,
      outline: AppColors.border2Light,
      outlineVariant: AppColors.borderLight,
    );
    return _build(colorScheme, AppColors.bgLight, AppColors.dimLight);
  }

  static ThemeData dark() {
    const colorScheme = ColorScheme.dark(
      primary: AppColors.accent,
      onPrimary: AppColors.accentInk,
      secondary: AppColors.accent,
      onSecondary: AppColors.accentInk,
      error: AppColors.rojoDark,
      onError: Colors.black,
      surface: AppColors.surfaceDark,
      onSurface: AppColors.textDark,
      surfaceContainerHighest: AppColors.surface3Dark,
      outline: AppColors.border2Dark,
      outlineVariant: AppColors.borderDark,
    );
    return _build(colorScheme, AppColors.bgDark, AppColors.dimDark);
  }

  static ThemeData _build(ColorScheme scheme, Color bg, Color dim) {
    return ThemeData(
      useMaterial3: true,
      colorScheme: scheme,
      scaffoldBackgroundColor: bg,
      appBarTheme: AppBarTheme(
        centerTitle: false,
        backgroundColor: bg,
        foregroundColor: scheme.onSurface,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
      ),
      cardTheme: CardThemeData(
        color: scheme.surface,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(14),
          side: BorderSide(color: scheme.outlineVariant),
        ),
      ),
      navigationBarTheme: NavigationBarThemeData(
        backgroundColor: scheme.surface,
        indicatorColor: scheme.primary.withValues(alpha: 0.12),
        surfaceTintColor: Colors.transparent,
      ),
      chipTheme: ChipThemeData(
        backgroundColor: scheme.surfaceContainerHighest,
        side: BorderSide(color: scheme.outlineVariant),
        labelStyle: TextStyle(color: scheme.onSurface),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: scheme.surfaceContainerHighest,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide.none,
        ),
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          backgroundColor: scheme.primary,
          foregroundColor: scheme.onPrimary,
          padding: const EdgeInsets.symmetric(vertical: 14),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
        ),
      ),
      textTheme:
          (scheme.brightness == Brightness.dark
                  ? Typography.material2021(platform: TargetPlatform.android)
                      .white
                  : Typography.material2021(platform: TargetPlatform.android)
                      .black)
              .apply(bodyColor: scheme.onSurface, displayColor: scheme.onSurface),
    );
  }
}
