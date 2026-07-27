import 'dart:io';

import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class Justification {
  const Justification({
    required this.id,
    required this.motivo,
    required this.estado,
    required this.motivoRechazo,
    required this.archivoUrl,
  });

  factory Justification.fromJson(Map<String, dynamic> json) => Justification(
        id: json['idJustificacion'] as int,
        motivo: json['motivo'] as String? ?? '',
        estado: json['estado'] as String? ?? 'pendiente',
        motivoRechazo: json['motivoRechazo'] as String?,
        archivoUrl: json['archivo_url'] as String?,
      );

  final int id;
  final String motivo;
  final String estado;
  final String? motivoRechazo;
  final String? archivoUrl;
}

class AttendanceRecord {
  const AttendanceRecord({
    required this.id,
    required this.fecha,
    required this.estado,
    required this.observacion,
    required this.idEstudiante,
    required this.nombreEstudiante,
    required this.idModulo,
    required this.nombreModulo,
    required this.nombreProfesor,
    required this.justificacion,
  });

  factory AttendanceRecord.fromJson(Map<String, dynamic> json) => AttendanceRecord(
        id: json['idAsistencia'] as int,
        fecha: json['fecha'] as String? ?? '',
        estado: json['estado'] as String? ?? '',
        observacion: json['observacion'] as String?,
        idEstudiante: json['idEstudiante'] as int? ?? 0,
        nombreEstudiante: json['nombreEstudiante'] as String? ?? '',
        idModulo: json['idModulo'] as int? ?? 0,
        nombreModulo: json['nombreModulo'] as String? ?? '',
        nombreProfesor: json['nombreProfesor'] as String? ?? '',
        justificacion: json['justificacion'] != null
            ? Justification.fromJson((json['justificacion'] as Map).cast<String, dynamic>())
            : null,
      );

  final int id;
  final String fecha;
  final String estado; // presente | ausente | retraso | justificado
  final String? observacion;
  final int idEstudiante;
  final String nombreEstudiante;
  final int idModulo;
  final String nombreModulo;
  final String nombreProfesor;
  final Justification? justificacion;

  bool get canJustify =>
      (estado == 'ausente' || estado == 'retraso') &&
      (justificacion == null || justificacion!.estado == 'rechazada');
}

class RosterStudent {
  const RosterStudent({required this.id, required this.nombre});
  factory RosterStudent.fromJson(Map<String, dynamic> json) =>
      RosterStudent(id: json['idEstudiante'] as int, nombre: json['nombreEstudiante'] as String? ?? '');
  final int id;
  final String nombre;
}

class PendingJustification {
  const PendingJustification({
    required this.idJustificacion,
    required this.idAsistencia,
    required this.motivo,
    required this.fecha,
    required this.nombreModulo,
    required this.nombreEstudiante,
    required this.archivoUrl,
  });

  factory PendingJustification.fromJson(Map<String, dynamic> json) => PendingJustification(
        idJustificacion: json['idJustificacion'] as int,
        idAsistencia: json['idAsistencia'] as int,
        motivo: json['motivo'] as String? ?? '',
        fecha: json['fecha'] as String? ?? '',
        nombreModulo: json['nombreModulo'] as String? ?? '',
        nombreEstudiante: json['nombreEstudiante'] as String? ?? '',
        archivoUrl: json['archivo_url'] as String?,
      );

  final int idJustificacion;
  final int idAsistencia;
  final String motivo;
  final String fecha;
  final String nombreModulo;
  final String? archivoUrl;
  final String nombreEstudiante;
}

class AttendanceRepository {
  AttendanceRepository(this._client);
  final ApiClient _client;

  Future<List<AttendanceRecord>> fetchMine() async {
    final data = await _client.get('/attendance.php');
    return (data['attendance'] as List).cast<Map<String, dynamic>>().map(AttendanceRecord.fromJson).toList();
  }

  Future<({List<AttendanceRecord> attendance, List<RosterStudent> roster})> fetchForModule(
    int idModulo, {
    String? fecha,
  }) async {
    final data = await _client.get('/attendance.php', query: {
      'idModulo': idModulo,
      if (fecha != null) 'fecha': fecha,
    });
    return (
      attendance: (data['attendance'] as List).cast<Map<String, dynamic>>().map(AttendanceRecord.fromJson).toList(),
      roster: (data['roster'] as List).cast<Map<String, dynamic>>().map(RosterStudent.fromJson).toList(),
    );
  }

  /// secretaria/director: a specific student's attendance (center-wide, no
  /// scope restriction) — backs StaffJustifyScreen's student-search flow.
  Future<List<AttendanceRecord>> fetchForStudent(int idEstudiante) async {
    final data = await _client.get('/attendance.php', query: {'idEstudiante': idEstudiante});
    return (data['attendance'] as List).cast<Map<String, dynamic>>().map(AttendanceRecord.fromJson).toList();
  }

  Future<void> submitAttendance({
    required int idModulo,
    required String fecha,
    required List<Map<String, dynamic>> registros,
  }) {
    return _client.post('/attendance.php', data: {
      'idModulo': idModulo,
      'fecha': fecha,
      'registros': registros,
    });
  }

  Future<void> justify({required int idAsistencia, required String motivo, File? archivo}) {
    return _client.post('/attendance-justify.php', data: FormData.fromMap({
      'idAsistencia': idAsistencia,
      'motivo': motivo,
      if (archivo != null) 'archivo': MultipartFile.fromFileSync(archivo.path),
    }));
  }

  Future<List<PendingJustification>> fetchPending() async {
    final data = await _client.get('/attendance-resolve.php');
    return (data['pending'] as List).cast<Map<String, dynamic>>().map(PendingJustification.fromJson).toList();
  }

  Future<void> resolve({required int idJustificacion, required bool aprobar, String? motivoRechazo}) {
    return _client.post('/attendance-resolve.php', data: {
      'idJustificacion': idJustificacion,
      'aprobar': aprobar,
      if (motivoRechazo != null) 'motivoRechazo': motivoRechazo,
    });
  }
}

final attendanceRepositoryProvider = Provider<AttendanceRepository>(
  (ref) => AttendanceRepository(ref.read(apiClientProvider)),
);

final attendanceMineProvider = FutureProvider.autoDispose<List<AttendanceRecord>>(
  (ref) => ref.read(attendanceRepositoryProvider).fetchMine(),
);

final pendingJustificationsProvider = FutureProvider.autoDispose<List<PendingJustification>>(
  (ref) => ref.read(attendanceRepositoryProvider).fetchPending(),
);
