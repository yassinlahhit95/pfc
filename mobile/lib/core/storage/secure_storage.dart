import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// Keystore-backed storage for the session token. Never use
/// shared_preferences for the token itself — only for the light
/// response-cache described in the per-feature repositories.
class SecureStorage {
  SecureStorage()
      : _storage = const FlutterSecureStorage(
          aOptions: AndroidOptions(encryptedSharedPreferences: true),
        );

  final FlutterSecureStorage _storage;

  static const _keyToken = 'auth_token';
  static const _keyExpiresAt = 'auth_expires_at';
  static const _keyUserType = 'auth_user_type';
  static const _keyUserId = 'auth_user_id';

  Future<void> saveSession({
    required String token,
    required String expiresAt,
    required String userType,
    required int userId,
  }) async {
    await Future.wait([
      _storage.write(key: _keyToken, value: token),
      _storage.write(key: _keyExpiresAt, value: expiresAt),
      _storage.write(key: _keyUserType, value: userType),
      _storage.write(key: _keyUserId, value: userId.toString()),
    ]);
  }

  Future<String?> readToken() => _storage.read(key: _keyToken);
  Future<String?> readUserType() => _storage.read(key: _keyUserType);
  Future<String?> readExpiresAt() => _storage.read(key: _keyExpiresAt);
  Future<int?> readUserId() async {
    final raw = await _storage.read(key: _keyUserId);
    return raw == null ? null : int.tryParse(raw);
  }

  Future<void> clear() async {
    await Future.wait([
      _storage.delete(key: _keyToken),
      _storage.delete(key: _keyExpiresAt),
      _storage.delete(key: _keyUserType),
      _storage.delete(key: _keyUserId),
    ]);
  }
}
