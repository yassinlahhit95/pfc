import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';
import '../../../core/utils/cache_extension.dart';

class Student {
  const Student({
    required this.id,
    required this.nombre,
    required this.email,
    required this.ciclo,
    required this.abreviaturaCiclo,
    required this.course,
    required this.year,
    required this.estado,
    required this.dateEnrolled,
    required this.telefono,
  });

  factory Student.fromJson(Map<String, dynamic> json) => Student(
        id: json['idEstudiante'] as int,
        nombre: json['nombreEstudiante'] as String? ?? '',
        email: json['emailEstudiante'] as String? ?? '',
        ciclo: json['nombreCiclo'] as String? ?? '',
        abreviaturaCiclo: json['abreviaturaCiclo'] as String? ?? '',
        course: json['curso'] as String? ?? '',
        year: json['anioEstudio'] as String?,
        estado: json['estado'] as String? ?? 'activo',
        dateEnrolled: json['fechaAlta'] as String?,
        telefono: json['telefonoEstudiante'] as String? ?? '',
      );

  final int id;
  final String nombre;
  final String email;
  final String ciclo;
  final String abreviaturaCiclo;
  final String course; // Grado Medio | Grado Superior
  final String? year; // 1º | 2º
  final String estado; // activo | inactivo
  final String? dateEnrolled;
  final String telefono;
}

class StudentsRepository {
  StudentsRepository(this._client);
  final ApiClient _client;

  Future<({List<Student> students, int total})> fetchStudents({
    int limit = 20,
    int offset = 0,
    int? cicloId,
    int? nivelId,
    String? status,
    String? query,
  }) async {
    final queryParams = {
      'limit': limit.toString(),
      'offset': offset.toString(),
      if (cicloId != null) 'ciclo': cicloId.toString(),
      if (nivelId != null) 'nivel': nivelId.toString(),
      if (status != null && status.isNotEmpty) 'status': status,
      if (query != null && query.isNotEmpty) 'q': query,
    };

    final data = await _client.get('/estudiantes.php', query: queryParams);
    return (
      students: (data['students'] as List)
          .cast<Map<String, dynamic>>()
          .map(Student.fromJson)
          .toList(),
      total: data['total'] as int? ?? 0,
    );
  }
}

final studentsRepositoryProvider = Provider<StudentsRepository>(
  (ref) => StudentsRepository(ref.read(apiClientProvider)),
);

final studentsProvider = FutureProvider.autoDispose
    .family<({List<Student> students, int total}), ({int limit, int offset, int? cicloId, int? nivelId, String? status, String? query})>(
  (ref, params) {
    ref.cacheFor(const Duration(minutes: 5));
    return ref.read(studentsRepositoryProvider).fetchStudents(
        limit: params.limit,
        offset: params.offset,
        cicloId: params.cicloId,
        nivelId: params.nivelId,
        status: params.status,
        query: params.query,
      );
  },
);
