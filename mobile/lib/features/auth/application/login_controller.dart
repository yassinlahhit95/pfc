import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_sign_in/google_sign_in.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../data/auth_repository.dart';

/// Drives the login form: `AsyncValue<void>` gives the screen loading/error
/// state for free, and success is signalled by sessionControllerProvider
/// transitioning to a non-null Session (app_router reacts to that).
class LoginController extends AsyncNotifier<void> {
  @override
  Future<void> build() async {}

  Future<void> submit({
    required String email,
    required String password,
    String? role,
  }) async {
    state = const AsyncLoading();
    state = await AsyncValue.guard(() async {
      final result = await ref.read(authRepositoryProvider).login(
            email: email,
            password: password,
            role: role,
            deviceInfo: 'Android',
          );
      await ref.read(sessionControllerProvider.notifier).setSession(
            token: result.token,
            expiresAt: result.expiresAt,
            role: userRoleFromApi(result.userType),
            userId: result.userId,
          );
    });
  }

  Future<void> submitGoogle() async {
    state = const AsyncLoading();
    state = await AsyncValue.guard(() async {
      final googleSignIn = await ref.read(googleSignInProvider.future);

      // Disconnect/sign out first to always prompt the account chooser
      try {
        await googleSignIn.signOut();
      } catch (_) {}

      final GoogleSignInAccount account;
      try {
        account = await googleSignIn.authenticate();
      } on GoogleSignInException catch (e) {
        if (e.code == GoogleSignInExceptionCode.canceled) {
          // User aborted the sign-in
          return;
        }
        rethrow;
      }

      final idToken = account.authentication.idToken;
      if (idToken == null) {
        throw Exception('No se pudo obtener el ID token de Google.');
      }

      final result = await ref.read(authRepositoryProvider).loginGoogle(
            idToken: idToken,
            deviceInfo: 'Android',
          );
      await ref.read(sessionControllerProvider.notifier).setSession(
            token: result.token,
            expiresAt: result.expiresAt,
            role: userRoleFromApi(result.userType),
            userId: result.userId,
          );
    });
  }
}

final loginControllerProvider =
    AsyncNotifierProvider<LoginController, void>(LoginController.new);

/// google_sign_in v7+ requires `initialize()` to be called exactly once
/// before any other method — calling it more than once is undefined
/// behavior per the package's own docs. A FutureProvider's cached future
/// gives that "exactly once" guarantee for the app's lifetime without a
/// separate bootstrap step.
final googleSignInProvider = FutureProvider<GoogleSignIn>((ref) async {
  final instance = GoogleSignIn.instance;
  await instance.initialize();
  return instance;
});
