import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class SchoolEvent {
  const SchoolEvent({
    required this.id,
    required this.titulo,
    required this.fecha,
    required this.hora,
    this.descripcion,
    this.ubicacion,
  });

  factory SchoolEvent.fromJson(Map<String, dynamic> json) => SchoolEvent(
        id: json['idEvento'] as int? ?? 0,
        titulo: json['tituloEvento'] as String? ?? '',
        fecha: json['fechaEvento'] as String? ?? '',
        hora: json['horaEvento'] as String? ?? '',
        descripcion: json['descripcionEvento'] as String?,
        ubicacion: json['ubicacionEvento'] as String?,
      );

  final int id;
  final String titulo;
  final String fecha;
  final String hora;
  final String? descripcion;
  final String? ubicacion;
}

class EventsRepository {
  EventsRepository(this._client);
  final ApiClient _client;

  Future<List<SchoolEvent>> fetchEvents({
    int limit = 20,
    int offset = 0,
    bool upcoming = true,
  }) async {
    final data = await _client.get('/events.php', query: {
      'limit': limit,
      'offset': offset,
      if (upcoming) 'upcoming': '1',
    });
    final rows = (data['events'] as List).cast<Map<String, dynamic>>();
    return rows.map(SchoolEvent.fromJson).toList();
  }
}

final eventsRepositoryProvider = Provider<EventsRepository>(
  (ref) => EventsRepository(ref.read(apiClientProvider)),
);

final eventsProvider = FutureProvider.autoDispose<List<SchoolEvent>>(
  (ref) => ref.read(eventsRepositoryProvider).fetchEvents(),
);
