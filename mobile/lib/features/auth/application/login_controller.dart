import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_sign_in/google_sign_in.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../data/auth_repository.dart';

/// Drives the login form: AsyncValue<void> gives the screen loading/error
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
      final googleSignIn = GoogleSignIn(
        scopes: ['email'],
      );
      // Disconnect/sign out first to always prompt the account chooser
      try {
        await googleSignIn.signOut();
      } catch (_) {}

      final account = await googleSignIn.signIn();
      if (account == null) {
        // User aborted the sign-in
        return;
      }

      final auth = await account.authentication;
      final idToken = auth.idToken;
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
