import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

/// Design tokens — the small, fixed vocabulary every screen draws from
/// instead of inventing its own spacing/radius/shadow per widget. This is
/// what keeps a large app looking like one product instead of a pile of
/// independently-styled screens.
class Space {
  static const xs = 4.0;
  static const sm = 8.0;
  static const md = 12.0;
  static const lg = 16.0;
  static const xl = 20.0;
  static const xxl = 24.0;
  static const xxxl = 32.0;
}

class Radii {
  static const sm = 10.0;
  static const md = 14.0;
  static const lg = 18.0;
  static const xl = 24.0;
  static const pill = 999.0;
}

/// Mirrors the web app's core palette (public/css/dashboard.css) but the
/// *usage* is intentionally much more restrained than the web dashboard —
/// accent color is reserved for primary actions, active/selected states and
/// focus, not decorative icon backgrounds on every list row.
class AppColors {
  static const accent = Color(0xFF4F46E5);
  static const accentInk = Color(0xFFFFFFFF);

  static const bgLight = Color(0xFFF7F7FB);
  static const surfaceLight = Color(0xFFFFFFFF);
  static const surface2Light = Color(0xFFF7F8FC);
  static const surface3Light = Color(0xFFF0F1F6);
  static const borderLight = Color(0x0F0F172A);
  static const border2Light = Color(0x1A0F172A);
  static const textLight = Color(0xFF0E1116);
  static const dimLight = Color(0xFF585F6E);
  static const mutLight = Color(0xFF9096A3);

  static const bgDark = Color(0xFF08090D);
  static const surfaceDark = Color(0xFF121317);
  static const surface2Dark = Color(0xFF17181D);
  static const surface3Dark = Color(0xFF1E2027);
  static const borderDark = Color(0x14FFFFFF);
  static const border2Dark = Color(0x22FFFFFF);
  static const textDark = Color(0xFFF2F3F5);
  static const dimDark = Color(0xFFA0A5B1);
  static const mutDark = Color(0xFF6B7080);

  static const verdeLight = Color(0xFF0D9488);
  static const rojoLight = Color(0xFFDC4C4C);
  static const naranjaLight = Color(0xFFC2760C);
  static const azulLight = Color(0xFF2563EB);
  static const violetaLight = Color(0xFF7C3AED);

  static const verdeDark = Color(0xFF2DD4BF);
  static const rojoDark = Color(0xFFF37171);
  static const naranjaDark = Color(0xFFF0A93E);
  static const azulDark = Color(0xFF60A5FA);
  static const violetaDark = Color(0xFFA78BFA);
}

/// Single subtle elevation shadow, reused everywhere instead of each screen
/// picking its own blur/opacity — that consistency is a large part of what
/// separates a "designed" app from one that merely uses cards.
List<BoxShadow> cardShadow(Brightness brightness) {
  return [
    BoxShadow(
      color: brightness == Brightness.dark
          ? Colors.black.withValues(alpha: 0.35)
          : const Color(0xFF0E1116).withValues(alpha: 0.05),
      blurRadius: 20,
      offset: const Offset(0, 6),
      spreadRadius: -4,
    ),
  ];
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
      onSurfaceVariant: AppColors.dimLight,
      surfaceContainerHighest: AppColors.surface3Light,
      outline: AppColors.border2Light,
      outlineVariant: AppColors.borderLight,
    );
    return _build(colorScheme, AppColors.bgLight);
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
      onSurfaceVariant: AppColors.dimDark,
      surfaceContainerHighest: AppColors.surface3Dark,
      outline: AppColors.border2Dark,
      outlineVariant: AppColors.borderDark,
    );
    return _build(colorScheme, AppColors.bgDark);
  }

  static ThemeData _build(ColorScheme scheme, Color bg) {
    final isDark = scheme.brightness == Brightness.dark;

    final base = GoogleFonts.interTextTheme(
      isDark ? Typography.material2021().white : Typography.material2021().black,
    );

    // A tighter, more considered scale than Material's defaults — smaller
    // display sizes, negative tracking on headings (the detail that makes
    // headings in Linear/Vercel/Stripe read as "designed" rather than just
    // "big text"), calmer body copy.
    final textTheme = base.copyWith(
      headlineSmall: base.headlineSmall?.copyWith(
        fontWeight: FontWeight.w700,
        letterSpacing: -0.6,
        height: 1.15,
        color: scheme.onSurface,
      ),
      titleLarge: base.titleLarge?.copyWith(
        fontWeight: FontWeight.w700,
        letterSpacing: -0.4,
        fontSize: 20,
        color: scheme.onSurface,
      ),
      titleMedium: base.titleMedium?.copyWith(
        fontWeight: FontWeight.w600,
        letterSpacing: -0.2,
        color: scheme.onSurface,
      ),
      titleSmall: base.titleSmall?.copyWith(
        fontWeight: FontWeight.w600,
        letterSpacing: -0.1,
        color: scheme.onSurface,
      ),
      bodyLarge: base.bodyLarge?.copyWith(height: 1.45, color: scheme.onSurface),
      bodyMedium: base.bodyMedium?.copyWith(height: 1.4, color: scheme.onSurface),
      bodySmall: base.bodySmall?.copyWith(height: 1.35, color: scheme.onSurfaceVariant),
      labelLarge: base.labelLarge?.copyWith(fontWeight: FontWeight.w600, letterSpacing: 0.1),
      labelSmall: base.labelSmall?.copyWith(
        fontWeight: FontWeight.w600,
        letterSpacing: 0.4,
        color: scheme.onSurfaceVariant,
      ),
    );

    return ThemeData(
      useMaterial3: true,
      colorScheme: scheme,
      scaffoldBackgroundColor: bg,
      splashFactory: InkSparkle.splashFactory,
      textTheme: textTheme,
      appBarTheme: AppBarTheme(
        centerTitle: false,
        backgroundColor: bg,
        foregroundColor: scheme.onSurface,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        scrolledUnderElevation: 0,
        titleTextStyle: textTheme.titleLarge,
        iconTheme: IconThemeData(color: scheme.onSurface, size: 22),
      ),
      cardTheme: CardThemeData(
        color: scheme.surface,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        margin: EdgeInsets.zero,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(Radii.lg),
          side: BorderSide(color: scheme.outlineVariant),
        ),
      ),
      navigationBarTheme: NavigationBarThemeData(
        backgroundColor: scheme.surface,
        indicatorColor: Colors.transparent,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        height: 64,
        labelTextStyle: WidgetStateProperty.resolveWith((states) {
          final selected = states.contains(WidgetState.selected);
          return GoogleFonts.inter(
            fontSize: 11,
            fontWeight: selected ? FontWeight.w600 : FontWeight.w500,
            color: selected ? scheme.primary : scheme.onSurfaceVariant,
          );
        }),
        iconTheme: WidgetStateProperty.resolveWith((states) {
          final selected = states.contains(WidgetState.selected);
          return IconThemeData(
            color: selected ? scheme.primary : scheme.onSurfaceVariant,
            size: 23,
          );
        }),
      ),
      dividerTheme: DividerThemeData(color: scheme.outlineVariant, thickness: 1, space: 1),
      chipTheme: ChipThemeData(
        backgroundColor: scheme.surfaceContainerHighest,
        side: BorderSide.none,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(Radii.pill)),
        labelStyle: textTheme.labelLarge?.copyWith(color: scheme.onSurface),
        padding: const EdgeInsets.symmetric(horizontal: 4),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: isDark ? scheme.surfaceContainerHighest : AppColors.surface2Light,
        contentPadding: const EdgeInsets.symmetric(horizontal: Space.lg, vertical: Space.lg),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(Radii.md),
          borderSide: BorderSide(color: scheme.outlineVariant),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(Radii.md),
          borderSide: BorderSide(color: scheme.outlineVariant),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(Radii.md),
          borderSide: BorderSide(color: scheme.primary, width: 1.6),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(Radii.md),
          borderSide: BorderSide(color: scheme.error),
        ),
        labelStyle: TextStyle(color: scheme.onSurfaceVariant),
        floatingLabelStyle: TextStyle(color: scheme.primary),
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          backgroundColor: scheme.primary,
          foregroundColor: scheme.onPrimary,
          textStyle: textTheme.labelLarge?.copyWith(fontSize: 15),
          padding: const EdgeInsets.symmetric(vertical: Space.lg),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(Radii.md)),
        ),
      ),
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: scheme.onSurface,
          side: BorderSide(color: scheme.outlineVariant),
          textStyle: textTheme.labelLarge?.copyWith(fontSize: 15),
          padding: const EdgeInsets.symmetric(vertical: Space.lg),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(Radii.md)),
        ),
      ),
      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: scheme.primary,
          textStyle: textTheme.labelLarge,
        ),
      ),
      iconButtonTheme: IconButtonThemeData(
        style: IconButton.styleFrom(foregroundColor: scheme.onSurfaceVariant),
      ),
      tabBarTheme: TabBarThemeData(
        labelColor: scheme.primary,
        unselectedLabelColor: scheme.onSurfaceVariant,
        labelStyle: textTheme.labelLarge,
        unselectedLabelStyle: textTheme.labelLarge,
        indicatorColor: scheme.primary,
        indicatorSize: TabBarIndicatorSize.label,
        dividerColor: scheme.outlineVariant,
      ),
      listTileTheme: ListTileThemeData(
        iconColor: scheme.onSurfaceVariant,
        textColor: scheme.onSurface,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(Radii.md)),
      ),
      snackBarTheme: SnackBarThemeData(
        backgroundColor: isDark ? AppColors.surface3Dark : AppColors.textLight,
        contentTextStyle: TextStyle(color: isDark ? scheme.onSurface : Colors.white),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(Radii.md)),
      ),
      progressIndicatorTheme: ProgressIndicatorThemeData(color: scheme.primary),
      pageTransitionsTheme: const PageTransitionsTheme(builders: {
        TargetPlatform.android: _FadeThroughTransitionsBuilder(),
        TargetPlatform.iOS: CupertinoPageTransitionsBuilder(),
      }),
    );
  }
}

/// A calmer page transition than Android's default slide-up-from-bottom —
/// a soft cross-fade with a hint of motion, closer to how Linear/Notion
/// move between views.
class _FadeThroughTransitionsBuilder extends PageTransitionsBuilder {
  const _FadeThroughTransitionsBuilder();

  @override
  Widget buildTransitions<T>(
    PageRoute<T> route,
    BuildContext context,
    Animation<double> animation,
    Animation<double> secondaryAnimation,
    Widget child,
  ) {
    final curved = CurvedAnimation(parent: animation, curve: Curves.easeOutCubic);
    return FadeTransition(
      opacity: curved,
      child: SlideTransition(
        position: Tween<Offset>(begin: const Offset(0, 0.02), end: Offset.zero).animate(curved),
        child: child,
      ),
    );
  }
}
