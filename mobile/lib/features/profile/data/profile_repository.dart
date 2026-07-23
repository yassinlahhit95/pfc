import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class Profile {
  const Profile({required this.userType, required this.data, this.ciclo});

  final String userType;
  final Map<String, dynamic> data;
  final Map<String, dynamic>? ciclo;

  String get displayName {
    // The row's name column varies by role (nombreEstudiante, nombreProfesor, ...)
    for (final key in data.keys) {
      if (key.startsWith('nombre')) return data[key] as String? ?? '';
    }
    return '';
  }

  String get email {
    for (final key in data.keys) {
      if (key.startsWith('email')) return data[key] as String? ?? '';
    }
    return '';
  }

  String get roleLabel => switch (userType) {
        'estudiante' => 'Estudiante',
        'profesor' => 'Profesor',
        'director' => 'Dirección',
        'secretaria' => 'Secretaría',
        'tutor' => 'Tutor',
        _ => userType,
      };
}

class ProfileRepository {
  ProfileRepository(this._client);
  final ApiClient _client;

  Future<Profile> fetchMe() async {
    final data = await _client.get('/me.php');
    return Profile(
      userType: data['user_type'] as String,
      data: (data['profile'] as Map).cast<String, dynamic>(),
      ciclo: (data['ciclo'] as Map?)?.cast<String, dynamic>(),
    );
  }
}

final profileRepositoryProvider = Provider<ProfileRepository>(
  (ref) => ProfileRepository(ref.read(apiClientProvider)),
);

final profileProvider = FutureProvider.autoDispose<Profile>(
  (ref) => ref.read(profileRepositoryProvider).fetchMe(),
);
