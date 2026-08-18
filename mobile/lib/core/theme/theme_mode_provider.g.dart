// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'theme_mode_provider.dart';

// **************************************************************************
// RiverpodGenerator
// **************************************************************************

// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint, type=warning
/// Persisted light/dark override — mirrors LocaleNotifier's pattern
/// (core/i18n/translations.dart) exactly, so both preferences are stored
/// and hydrated the same way. Defaults to following the OS setting until
/// the user picks one explicitly.

@ProviderFor(ThemeModeNotifier)
final themeModeProvider = ThemeModeNotifierProvider._();

/// Persisted light/dark override — mirrors LocaleNotifier's pattern
/// (core/i18n/translations.dart) exactly, so both preferences are stored
/// and hydrated the same way. Defaults to following the OS setting until
/// the user picks one explicitly.
final class ThemeModeNotifierProvider
    extends $NotifierProvider<ThemeModeNotifier, ThemeMode> {
  /// Persisted light/dark override — mirrors LocaleNotifier's pattern
  /// (core/i18n/translations.dart) exactly, so both preferences are stored
  /// and hydrated the same way. Defaults to following the OS setting until
  /// the user picks one explicitly.
  ThemeModeNotifierProvider._()
      : super(
          from: null,
          argument: null,
          retry: null,
          name: r'themeModeProvider',
          isAutoDispose: false,
          dependencies: null,
          $allTransitiveDependencies: null,
        );

  @override
  String debugGetCreateSourceHash() => _$themeModeNotifierHash();

  @$internal
  @override
  ThemeModeNotifier create() => ThemeModeNotifier();

  /// {@macro riverpod.override_with_value}
  Override overrideWithValue(ThemeMode value) {
    return $ProviderOverride(
      origin: this,
      providerOverride: $SyncValueProvider<ThemeMode>(value),
    );
  }
}

String _$themeModeNotifierHash() => r'0c30a39a50d8a605b9c60ddde725467932fe9e16';

/// Persisted light/dark override — mirrors LocaleNotifier's pattern
/// (core/i18n/translations.dart) exactly, so both preferences are stored
/// and hydrated the same way. Defaults to following the OS setting until
/// the user picks one explicitly.

abstract class _$ThemeModeNotifier extends $Notifier<ThemeMode> {
  ThemeMode build();
  @$mustCallSuper
  @override
  WhenComplete runBuild() {
    final ref = this.ref as $Ref<ThemeMode, ThemeMode>;
    final element = ref.element as $ClassProviderElement<
        AnyNotifier<ThemeMode, ThemeMode>, ThemeMode, Object?, Object?>;
    return element.handleCreate(ref, build);
  }
}
