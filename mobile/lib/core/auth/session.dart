/// The 5 roles the API's `V1_USER_MAP` recognizes (api/v1/_api.php).
enum UserRole { estudiante, profesor, director, tutor, secretaria }

UserRole userRoleFromApi(String value) => switch (value) {
      'estudiante' => UserRole.estudiante,
      'profesor' => UserRole.profesor,
      'director' => UserRole.director,
      'tutor' => UserRole.tutor,
      'secretaria' => UserRole.secretaria,
      _ => throw ArgumentError('Unknown user_type from API: $value'),
    };

String userRoleToApi(UserRole role) => role.name;

/// Immutable snapshot of the current authenticated session.
class Session {
  const Session({
    required this.token,
    required this.expiresAt,
    required this.role,
    required this.userId,
  });

  final String token;
  final DateTime expiresAt;
  final UserRole role;
  final int userId;

  bool get isExpired => DateTime.now().isAfter(expiresAt);
}
