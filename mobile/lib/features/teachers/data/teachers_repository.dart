import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class Teacher {
  const Teacher({
    required this.id,
    required this.nombre,
    required this.email,
    required this.telefono,
    required this.isTutor,
    required this.cicloTutoria,
  });

  factory Teacher.fromJson(Map<String, dynamic> json) => Teacher(
        id: json['idProfesor'] as int,
        nombre: json['nombreProfesor'] as String? ?? '',
        email: json['emailProfesor'] as String? ?? '',
        telefono: json['telefonoProfesor'] as String?,
        isTutor: json['esTutor'] == 1 || json['esTutor'] == true || json['esTutor'] == 'true',
        cicloTutoria: json['cicloTutoria'] as String?,
      );

  final int id;
  final String nombre;
  final String email;
  final String? telefono;
  final bool isTutor;
  final String? cicloTutoria;
}

class TeachersRepository {
  TeachersRepository(this._client);
  final ApiClient _client;

  Future<({List<Teacher> teachers, int total})> fetchTeachers({
    int limit = 20,
    int offset = 0,
    String? status,
    String? query,
  }) async {
    final queryParams = {
      'limit': limit.toString(),
      'offset': offset.toString(),
      if (status != null && status.isNotEmpty) 'status': status,
      if (query != null && query.isNotEmpty) 'q': query,
    };

    final data = await _client.get('/profesores.php', query: queryParams);
    return (
      teachers: (data['professors'] as List)
          .cast<Map<String, dynamic>>()
          .map(Teacher.fromJson)
          .toList(),
      total: data['total'] as int? ?? 0,
    );
  }
}

final teachersRepositoryProvider = Provider<TeachersRepository>(
  (ref) => TeachersRepository(ref.read(apiClientProvider)),
);

final teachersProvider = FutureProvider.autoDispose
    .family<({List<Teacher> teachers, int total}), ({int limit, int offset, String? status, String? query})>(
  (ref, params) => ref.read(teachersRepositoryProvider).fetchTeachers(
        limit: params.limit,
        offset: params.offset,
        status: params.status,
        query: params.query,
      ),
);
