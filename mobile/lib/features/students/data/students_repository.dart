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
    this.idCiclo,
    this.idGrupo,
    this.fechaNacimiento,
    this.dni,
    this.direccion,
    this.ciudad,
    this.codigoPostal,
    this.observaciones,
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
        idCiclo: json['idCiclo'] as int?,
        idGrupo: json['idGrupo'] as int?,
        fechaNacimiento: json['fechaNacimientoEstudiante'] as String? ?? '',
        dni: json['dniEstudiante'] as String? ?? '',
        direccion: json['direccionEstudiante'] as String? ?? '',
        ciudad: json['ciudadEstudiante'] as String? ?? '',
        codigoPostal: json['codigoPostalEstudiante'] as String? ?? '',
        observaciones: json['observacionesEstudiante'] as String? ?? '',
      );

  Map<String, dynamic> toJson() => {
        'idEstudiante': id,
        'nombreEstudiante': nombre,
        'emailEstudiante': email,
        'telefonoEstudiante': telefono,
        'idCiclo': idCiclo,
        'curso': course,
        'anioEstudio': year,
        'idGrupo': idGrupo,
        'fechaNacimientoEstudiante': fechaNacimiento,
        'dniEstudiante': dni,
        'direccionEstudiante': direccion,
        'ciudadEstudiante': ciudad,
        'codigoPostalEstudiante': codigoPostal,
        'observacionesEstudiante': observaciones,
      };

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
  
  final int? idCiclo;
  final int? idGrupo;
  final String? fechaNacimiento;
  final String? dni;
  final String? direccion;
  final String? ciudad;
  final String? codigoPostal;
  final String? observaciones;
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

  Future<void> createStudent(Student student) async {
    await _client.post('/estudiantes.php', data: student.toJson());
  }

  Future<void> updateStudent(Student student) async {
    await _client.put('/estudiantes.php', data: student.toJson());
  }

  Future<void> deleteStudent(int id, String password) async {
    await _client.delete('/estudiantes.php', query: {'id': id.toString()}, data: {'password': password});
  }

  Future<void> changeStudentPassword(int idEstudiante, String nuevaPassword) async {
    await _client.put('/estudiantes-password.php', data: {
      'idEstudiante': idEstudiante,
      'nuevaPassword': nuevaPassword,
    });
  }
}

final studentsRepositoryProvider = Provider<StudentsRepository>(
  (ref) => StudentsRepository(ref.read(apiClientProvider)),
);

typedef StudentFiltersArgs = ({int? cicloId, int? nivelId, String? status, String? query});

class StudentsNotifier extends AutoDisposeFamilyAsyncNotifier<({List<Student> students, int total}), StudentFiltersArgs> {
  bool _isLoadingMore = false;

  @override
  Future<({List<Student> students, int total})> build(StudentFiltersArgs arg) async {
    ref.cacheFor(const Duration(minutes: 5));
    return ref.read(studentsRepositoryProvider).fetchStudents(
          limit: 20,
          offset: 0,
          cicloId: arg.cicloId,
          nivelId: arg.nivelId,
          status: arg.status,
          query: arg.query,
        );
  }

  Future<void> loadMore() async {
    if (_isLoadingMore) return;
    final current = state.valueOrNull;
    if (current == null) return;
    if (current.students.length >= current.total) return;

    _isLoadingMore = true;
    try {
      final nextData = await ref.read(studentsRepositoryProvider).fetchStudents(
            limit: 20,
            offset: current.students.length,
            cicloId: arg.cicloId,
            nivelId: arg.nivelId,
            status: arg.status,
            query: arg.query,
          );
      state = AsyncData((
        students: [...current.students, ...nextData.students],
        total: nextData.total,
      ));
    } finally {
      _isLoadingMore = false;
    }
  }
}

final studentsProvider = AsyncNotifierProvider.autoDispose.family<StudentsNotifier, ({List<Student> students, int total}), StudentFiltersArgs>(
  () => StudentsNotifier(),
);
