// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'auth_state.dart';

// **************************************************************************
// RiverpodGenerator
// **************************************************************************

// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint, type=warning

@ProviderFor(secureStorage)
final secureStorageProvider = SecureStorageProvider._();

final class SecureStorageProvider
    extends $FunctionalProvider<SecureStorage, SecureStorage, SecureStorage>
    with $Provider<SecureStorage> {
  SecureStorageProvider._()
      : super(
          from: null,
          argument: null,
          retry: null,
          name: r'secureStorageProvider',
          isAutoDispose: false,
          dependencies: null,
          $allTransitiveDependencies: null,
        );

  @override
  String debugGetCreateSourceHash() => _$secureStorageHash();

  @$internal
  @override
  $ProviderElement<SecureStorage> $createElement($ProviderPointer pointer) =>
      $ProviderElement(pointer);

  @override
  SecureStorage create(Ref ref) {
    return secureStorage(ref);
  }

  /// {@macro riverpod.override_with_value}
  Override overrideWithValue(SecureStorage value) {
    return $ProviderOverride(
      origin: this,
      providerOverride: $SyncValueProvider<SecureStorage>(value),
    );
  }
}

String _$secureStorageHash() => r'5c9908c0046ad0e39469ee7acbb5540397b36693';

@ProviderFor(OnboardingCompletedController)
final onboardingCompletedControllerProvider =
    OnboardingCompletedControllerProvider._();

final class OnboardingCompletedControllerProvider
    extends $AsyncNotifierProvider<OnboardingCompletedController, bool> {
  OnboardingCompletedControllerProvider._()
      : super(
          from: null,
          argument: null,
          retry: null,
          name: r'onboardingCompletedControllerProvider',
          isAutoDispose: false,
          dependencies: null,
          $allTransitiveDependencies: null,
        );

  @override
  String debugGetCreateSourceHash() => _$onboardingCompletedControllerHash();

  @$internal
  @override
  OnboardingCompletedController create() => OnboardingCompletedController();
}

String _$onboardingCompletedControllerHash() =>
    r'5b5d000197b7ca4ec8444e962ad572c37729bd89';

abstract class _$OnboardingCompletedController extends $AsyncNotifier<bool> {
  FutureOr<bool> build();
  @$mustCallSuper
  @override
  WhenComplete runBuild() {
    final ref = this.ref as $Ref<AsyncValue<bool>, bool>;
    final element = ref.element as $ClassProviderElement<
        AnyNotifier<AsyncValue<bool>, bool>,
        AsyncValue<bool>,
        Object?,
        Object?>;
    return element.handleCreate(ref, build);
  }
}

/// Holds the current session (or null when logged out). Hydrated from
/// [SecureStorage] on first read; every write is mirrored to storage so a
/// force-killed app restores the session on next launch.

@ProviderFor(SessionController)
final sessionControllerProvider = SessionControllerProvider._();

/// Holds the current session (or null when logged out). Hydrated from
/// [SecureStorage] on first read; every write is mirrored to storage so a
/// force-killed app restores the session on next launch.
final class SessionControllerProvider
    extends $AsyncNotifierProvider<SessionController, Session?> {
  /// Holds the current session (or null when logged out). Hydrated from
  /// [SecureStorage] on first read; every write is mirrored to storage so a
  /// force-killed app restores the session on next launch.
  SessionControllerProvider._()
      : super(
          from: null,
          argument: null,
          retry: null,
          name: r'sessionControllerProvider',
          isAutoDispose: false,
          dependencies: null,
          $allTransitiveDependencies: null,
        );

  @override
  String debugGetCreateSourceHash() => _$sessionControllerHash();

  @$internal
  @override
  SessionController create() => SessionController();
}

String _$sessionControllerHash() => r'4076741e0263853f383cb7b644a6aa487fb72673';

/// Holds the current session (or null when logged out). Hydrated from
/// [SecureStorage] on first read; every write is mirrored to storage so a
/// force-killed app restores the session on next launch.

abstract class _$SessionController extends $AsyncNotifier<Session?> {
  FutureOr<Session?> build();
  @$mustCallSuper
  @override
  WhenComplete runBuild() {
    final ref = this.ref as $Ref<AsyncValue<Session?>, Session?>;
    final element = ref.element as $ClassProviderElement<
        AnyNotifier<AsyncValue<Session?>, Session?>,
        AsyncValue<Session?>,
        Object?,
        Object?>;
    return element.handleCreate(ref, build);
  }
}
