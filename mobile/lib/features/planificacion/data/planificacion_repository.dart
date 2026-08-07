import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class PlanTarea {
  const PlanTarea({
    required this.id,
    required this.texto,
    required this.completada,
    this.fechaCompletada,
    this.completadaPorNombre,
  });

  factory PlanTarea.fromJson(Map<String, dynamic> json) => PlanTarea(
        id: int.tryParse(json['idPlanTarea'].toString()) ?? 0,
        texto: json['texto'] as String? ?? '',
        completada: json['completada'] == 1 || json['completada'] == true,
        fechaCompletada: json['fechaCompletada'] as String?,
        completadaPorNombre: json['completadaPorNombre'] as String?,
      );

  final int id;
  final String texto;
  final bool completada;
  final String? fechaCompletada;
  final String? completadaPorNombre;
}

class PlanificacionRepository {
  PlanificacionRepository(this._client);
  final ApiClient _client;

  Future<List<PlanTarea>> fetchTareas() async {
    final data = await _client
        .get('/planificacion.php', query: {'action': 'list'});
    return (data['tareas'] as List)
        .cast<Map<String, dynamic>>()
        .map(PlanTarea.fromJson)
        .toList();
  }

  Future<void> crear(String texto) {
    return _client.post('/planificacion.php',
        query: {'action': 'create'}, data: {'texto': texto});
  }

  Future<void> toggle(int id, bool completada) {
    return _client.post('/planificacion.php',
        query: {'action': 'toggle'},
        data: {'id': id, 'completada': completada});
  }

  Future<void> borrar(int id) {
    return _client.post('/planificacion.php',
        query: {'action': 'delete'}, data: {'id': id});
  }
}

final planificacionRepositoryProvider = Provider<PlanificacionRepository>(
  (ref) => PlanificacionRepository(ref.read(apiClientProvider)),
);

final planificacionListProvider =
    FutureProvider.autoDispose<List<PlanTarea>>(
  (ref) => ref.read(planificacionRepositoryProvider).fetchTareas(),
);
