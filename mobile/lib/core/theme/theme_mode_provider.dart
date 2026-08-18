import 'package:flutter/material.dart';
import 'package:riverpod_annotation/riverpod_annotation.dart';
import 'package:shared_preferences/shared_preferences.dart';

part 'theme_mode_provider.g.dart';

/// Persisted light/dark override — mirrors LocaleNotifier's pattern
/// (core/i18n/translations.dart) exactly, so both preferences are stored
/// and hydrated the same way. Defaults to following the OS setting until
/// the user picks one explicitly.
@Riverpod(keepAlive: true)
class ThemeModeNotifier extends _$ThemeModeNotifier {
  @override
  ThemeMode build() {
    _load();
    return ThemeMode.system;
  }

  Future<void> _load() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final saved = prefs.getString('app_theme_mode');
      if (saved == 'light') state = ThemeMode.light;
      if (saved == 'dark') state = ThemeMode.dark;
    } catch (_) {}
  }

  Future<void> toggle() async {
    final next =
        state == ThemeMode.dark ? ThemeMode.light : ThemeMode.dark;
    state = next;
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('app_theme_mode', next == ThemeMode.dark ? 'dark' : 'light');
    } catch (_) {}
  }
}
