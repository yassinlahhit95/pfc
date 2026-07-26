import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class AccountRepository {
  AccountRepository(this._client);
  final ApiClient _client;

  Future<void> changePassword({required String currentPassword, required String newPassword}) {
    return _client.post('/change-password.php', data: {
      'current_password': currentPassword,
      'new_password': newPassword,
    });
  }

  Future<void> updateProfile(Map<String, String> fields) {
    return _client.post('/profile.php', data: fields);
  }
}

final accountRepositoryProvider = Provider<AccountRepository>(
  (ref) => AccountRepository(ref.read(apiClientProvider)),
);
