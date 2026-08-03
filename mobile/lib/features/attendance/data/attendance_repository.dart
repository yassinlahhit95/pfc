import 'dart:io';

import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

int _parseInt(dynamic value) {
  if (value == null) return 0;
  if (value is int) return value;
  if (value is String) return int.tryParse(value) ?? 0;
  return 0;
}

class Justification {
  const Justification({
    required this.id,
    required this.motivo,
    required this.estado,
    required this.motivoRechazo,
    required this.archivoUrl,
  });

  factory Justification.fromJson(Map<String, dynamic> json) => Justification(
        id: _parseInt(json['idJustificacion']),
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
    this.hora,
    required this.estado,
    required this.observacion,
    required this.idEstudiante,
    required this.nombreEstudiante,
    required this.idModulo,
    required this.nombreModulo,
    required this.nombreProfesor,
    required this.justificacion,
  });

  factory AttendanceRecord.fromJson(Map<String, dynamic> json) =>
      AttendanceRecord(
        id: _parseInt(json['idAsistencia']),
        fecha: json['fecha'] as String? ?? '',
        hora: json['hora'] as String?,
        estado: json['estado'] as String? ?? '',
        observacion: json['observacion'] as String?,
        idEstudiante: _parseInt(json['idEstudiante']),
        nombreEstudiante: json['nombreEstudiante'] as String? ?? '',
        idModulo: _parseInt(json['idModulo']),
        nombreModulo: json['nombreModulo'] as String? ?? '',
        nombreProfesor: json['nombreProfesor'] as String? ?? '',
        justificacion: json['justificacion'] != null
            ? Justification.fromJson(
                (json['justificacion'] as Map).cast<String, dynamic>())
            : null,
      );

  final int id;
  final String fecha;
  final String? hora;
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
  factory RosterStudent.fromJson(Map<String, dynamic> json) => RosterStudent(
      id: _parseInt(json['idEstudiante']),
      nombre: json['nombreEstudiante'] as String? ?? '');
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

  factory PendingJustification.fromJson(Map<String, dynamic> json) =>
      PendingJustification(
        idJustificacion: _parseInt(json['idJustificacion']),
        idAsistencia: _parseInt(json['idAsistencia']),
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
    return (data['attendance'] as List)
        .cast<Map<String, dynamic>>()
        .map(AttendanceRecord.fromJson)
        .toList();
  }

  Future<({List<AttendanceRecord> attendance, List<RosterStudent> roster})>
      fetchForModule(
    int idModulo, {
    String? fecha,
  }) async {
    final data = await _client.get('/attendance.php', query: {
      'idModulo': idModulo,
      if (fecha != null) 'fecha': fecha,
    });
    return (
      attendance: (data['attendance'] as List)
          .cast<Map<String, dynamic>>()
          .map(AttendanceRecord.fromJson)
          .toList(),
      roster: (data['roster'] as List)
          .cast<Map<String, dynamic>>()
          .map(RosterStudent.fromJson)
          .toList(),
    );
  }

  /// secretaria/director: a specific student's attendance (center-wide, no
  /// scope restriction) — backs StaffJustifyScreen's student-search flow.
  Future<List<AttendanceRecord>> fetchForStudent(int idEstudiante) async {
    final data = await _client
        .get('/attendance.php', query: {'idEstudiante': idEstudiante});
    return (data['attendance'] as List)
        .cast<Map<String, dynamic>>()
        .map(AttendanceRecord.fromJson)
        .toList();
  }

  Future<({List<AttendanceRecord> attendance, int total})>
      fetchCenterAttendance({
    int? limit,
    int? offset,
    int? nivel,
    int? ciclo,
    String? anio,
    int? grupo,
    String? q,
    String? estado,
  }) async {
    final query = <String, dynamic>{
      if (limit != null) 'limit': limit,
      if (offset != null) 'offset': offset,
      if (nivel != null) 'nivel': nivel,
      if (ciclo != null) 'ciclo': ciclo,
      if (anio != null && anio.isNotEmpty) 'anio': anio,
      if (grupo != null) 'grupo': grupo,
      if (q != null && q.isNotEmpty) 'q': q,
      if (estado != null && estado.isNotEmpty) 'estado': estado,
    };
    final data = await _client.get('/attendance.php', query: query);
    return (
      attendance: (data['attendance'] as List)
          .cast<Map<String, dynamic>>()
          .map(AttendanceRecord.fromJson)
          .toList(),
      total: data['total'] as int? ?? 0,
    );
  }

  Future<Map<String, dynamic>> fetchLookups() async {
    return _client.get('/lookups.php');
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

  Future<void> justify(
      {required int idAsistencia, required String motivo, File? archivo}) {
    return _client.post('/attendance.php',
        data: FormData.fromMap({
          'action': 'justify',
          'idAsistencia': idAsistencia,
          'motivo': motivo,
          if (archivo != null)
            'archivo': MultipartFile.fromFileSync(archivo.path),
        }));
  }

  Future<List<PendingJustification>> fetchPending() async {
    final data =
        await _client.get('/attendance.php', query: {'action': 'resolve'});
    return (data['pending'] as List)
        .cast<Map<String, dynamic>>()
        .map(PendingJustification.fromJson)
        .toList();
  }

  Future<void> resolve(
      {required int idJustificacion,
      required bool aprobar,
      String? motivoRechazo}) {
    return _client.post('/attendance.php', data: {
      'action': 'resolve',
      'idJustificacion': idJustificacion,
      'aprobar': aprobar,
      if (motivoRechazo != null) 'motivoRechazo': motivoRechazo,
    });
  }
}

final attendanceRepositoryProvider = Provider<AttendanceRepository>(
  (ref) => AttendanceRepository(ref.read(apiClientProvider)),
);

final attendanceMineProvider =
    FutureProvider.autoDispose<List<AttendanceRecord>>(
  (ref) => ref.read(attendanceRepositoryProvider).fetchMine(),
);

final pendingJustificationsProvider =
    FutureProvider.autoDispose<List<PendingJustification>>(
  (ref) => ref.read(attendanceRepositoryProvider).fetchPending(),
);
