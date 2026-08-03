import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';
import '../../../core/utils/cache_extension.dart';

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
        isTutor: json['esTutor'] == 1 ||
            json['esTutor'] == true ||
            json['esTutor'] == 'true',
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
    String? query,
  }) async {
    final queryParams = {
      'limit': limit.toString(),
      'offset': offset.toString(),
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

  Future<void> createTeacher(Map<String, dynamic> data) async {
    await _client.post('/profesores.php', data: data);
  }

  Future<void> updateTeacher(Map<String, dynamic> data) async {
    await _client.put('/profesores.php', data: data);
  }

  Future<void> deleteTeacher(int id, String password) async {
    await _client.delete('/profesores.php',
        query: {'id': id.toString()}, data: {'password': password});
  }

  Future<void> changeTeacherPassword(
      int idProfesor, String nuevaPassword) async {
    await _client.put('/profesores.php', data: {
      'action': 'password',
      'idProfesor': idProfesor,
      'nuevaPassword': nuevaPassword,
    });
  }
}

final teachersRepositoryProvider = Provider<TeachersRepository>(
  (ref) => TeachersRepository(ref.read(apiClientProvider)),
);

class TeachersNotifier extends AutoDisposeFamilyAsyncNotifier<
    ({List<Teacher> teachers, int total}), String?> {
  bool _isLoadingMore = false;

  @override
  Future<({List<Teacher> teachers, int total})> build(String? arg) async {
    ref.cacheFor(const Duration(minutes: 5));
    return ref.read(teachersRepositoryProvider).fetchTeachers(
          limit: 20,
          offset: 0,
          query: arg,
        );
  }

  Future<void> loadMore() async {
    if (_isLoadingMore) return;
    final current = state.valueOrNull;
    if (current == null) return;
    if (current.teachers.length >= current.total) return;

    _isLoadingMore = true;
    try {
      final nextData = await ref.read(teachersRepositoryProvider).fetchTeachers(
            limit: 20,
            offset: current.teachers.length,
            query: arg,
          );
      state = AsyncData((
        teachers: [...current.teachers, ...nextData.teachers],
        total: nextData.total,
      ));
    } finally {
      _isLoadingMore = false;
    }
  }
}

final teachersProvider = AsyncNotifierProvider.autoDispose
    .family<TeachersNotifier, ({List<Teacher> teachers, int total}), String?>(
  () => TeachersNotifier(),
);
