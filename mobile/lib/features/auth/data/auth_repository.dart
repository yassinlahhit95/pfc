import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class LoginResult {
  const LoginResult({
    required this.token,
    required this.expiresAt,
    required this.userType,
    required this.userId,
    required this.mustChangePassword,
  });

  final String token;
  final DateTime expiresAt;
  final String userType;
  final int userId;
  final bool mustChangePassword;
}

class AuthRepository {
  AuthRepository(this._client);
  final ApiClient _client;

  /// [role] narrows the login search to one user table; omit to let the
  /// API probe all 5 (auth.php tries each V1_USER_MAP entry in turn).
  Future<LoginResult> login({
    required String email,
    required String password,
    String? role,
    String? deviceInfo,
  }) async {
    final data = await _client.post('/auth.php', data: {
      'email': email,
      'password': password,
      if (role != null) 'role': role,
      if (deviceInfo != null) 'device_info': deviceInfo,
    });
    return LoginResult(
      token: data['token'] as String,
      expiresAt:
          DateTime.parse((data['expires_at'] as String).replaceFirst(' ', 'T')),
      userType: data['user_type'] as String,
      userId: data['user_id'] as int,
      mustChangePassword: data['must_change_password'] == true,
    );
  }

  Future<LoginResult> loginGoogle({
    required String idToken,
    String? deviceInfo,
  }) async {
    final data = await _client.post('/auth.php', data: {
      'google_token': idToken,
      if (deviceInfo != null) 'device_info': deviceInfo,
    });
    return LoginResult(
      token: data['token'] as String,
      expiresAt:
          DateTime.parse((data['expires_at'] as String).replaceFirst(' ', 'T')),
      userType: data['user_type'] as String,
      userId: data['user_id'] as int,
      mustChangePassword: data['must_change_password'] == true,
    );
  }

  /// Idempotent — always resolves even if the token was already invalid.
  Future<void> logout() async {
    try {
      await _client.delete('/auth.php');
    } catch (_) {
      // Logout is best-effort server-side; the local session is cleared
      // by the caller regardless (see SessionController.clear()).
    }
  }
}

final authRepositoryProvider = Provider<AuthRepository>(
  (ref) => AuthRepository(ref.read(apiClientProvider)),
);
