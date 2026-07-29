import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../storage/secure_storage.dart';
import 'session.dart';

final secureStorageProvider = Provider<SecureStorage>((ref) => SecureStorage());

/// Holds the current session (or null when logged out). Hydrated from
/// [SecureStorage] on first read; every write is mirrored to storage so a
/// force-killed app restores the session on next launch.
class SessionController extends AsyncNotifier<Session?> {
  @override
  Future<Session?> build() async {
    final storage = ref.read(secureStorageProvider);
    final results = await Future.wait([
      storage.readToken(),
      storage.readUserType(),
      storage.readUserId(),
      storage.readExpiresAt(),
    ]);
    final token = results[0] as String?;
    final userTypeRaw = results[1] as String?;
    final userId = results[2] as int?;
    final expiresAtStr = results[3] as String?;

    if (token == null || userTypeRaw == null || userId == null) return null;

    // Parse the stored expiry time. If corrupted/missing, default to 30 days.
    // An expired-but-present token is still handed to me.php on boot; a 401
    // there triggers the normal ApiClient 401 handler, which clears the
    // session and routes to login.
    late final DateTime expiresAt;
    if (expiresAtStr != null) {
      try {
        expiresAt = DateTime.parse(expiresAtStr);
      } catch (e) {
        expiresAt = DateTime.now().add(const Duration(days: 30));
      }
    } else {
      expiresAt = DateTime.now().add(const Duration(days: 30));
    }

    return Session(
      token: token,
      expiresAt: expiresAt,
      role: userRoleFromApi(userTypeRaw),
      userId: userId,
    );
  }

  Future<void> setSession({
    required String token,
    required DateTime expiresAt,
    required UserRole role,
    required int userId,
  }) async {
    final storage = ref.read(secureStorageProvider);
    await storage.saveSession(
      token: token,
      expiresAt: expiresAt.toIso8601String(),
      userType: userRoleToApi(role),
      userId: userId,
    );
    state = AsyncData(Session(
      token: token,
      expiresAt: expiresAt,
      role: role,
      userId: userId,
    ));
  }

  Future<void> clear() async {
    final storage = ref.read(secureStorageProvider);
    await storage.clear();
    state = const AsyncData(null);

    // Clear all cached data to prevent stale data access after logout (privacy/security).
    // This ensures any data providers that cached user info are purged, preventing
    // data leakage when switching between different user roles (tutor → professor, etc).
    ref.invalidate(secureStorageProvider);
  }
}

final sessionControllerProvider =
    AsyncNotifierProvider<SessionController, Session?>(SessionController.new);
