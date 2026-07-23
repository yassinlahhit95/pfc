import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class ScheduleSlot {
  const ScheduleSlot({
    required this.diaSemana,
    required this.horaInicio,
    required this.horaFin,
    required this.nombreModulo,
    required this.nombreProfesor,
    required this.codigoAula,
    required this.nombreAula,
  });

  factory ScheduleSlot.fromJson(Map<String, dynamic> json) => ScheduleSlot(
        diaSemana: json['diaSemana'] as String? ?? '',
        horaInicio: json['horaInicio'] as String? ?? '',
        horaFin: json['horaFin'] as String? ?? '',
        nombreModulo: json['nombreModulo'] as String? ?? '',
        nombreProfesor: json['nombreProfesor'] as String? ?? '',
        codigoAula: json['codigoAula'] as String? ?? '',
        nombreAula: json['nombreAula'] as String? ?? '',
      );

  final String diaSemana;
  final String horaInicio;
  final String horaFin;
  final String nombreModulo;
  final String nombreProfesor;
  final String codigoAula;
  final String nombreAula;
}

class ScheduleRepository {
  ScheduleRepository(this._client);
  final ApiClient _client;

  Future<List<ScheduleSlot>> fetchSchedule() async {
    final data = await _client.get('/schedule.php');
    final rows = (data['schedule'] as List).cast<Map<String, dynamic>>();
    return rows.map(ScheduleSlot.fromJson).toList();
  }
}

final scheduleRepositoryProvider = Provider<ScheduleRepository>(
  (ref) => ScheduleRepository(ref.read(apiClientProvider)),
);

final scheduleProvider = FutureProvider.autoDispose<List<ScheduleSlot>>(
  (ref) => ref.read(scheduleRepositoryProvider).fetchSchedule(),
);
