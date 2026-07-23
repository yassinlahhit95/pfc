import 'package:flutter_riverpod/flutter_riverpod.dart';

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
}

final loginControllerProvider =
    AsyncNotifierProvider<LoginController, void>(LoginController.new);
