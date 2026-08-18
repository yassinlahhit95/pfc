import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class AppNotification {
  const AppNotification({
    required this.id,
    required this.tipo,
    required this.mensaje,
    required this.leido,
    required this.fechaCreacion,
  });

  factory AppNotification.fromJson(Map<String, dynamic> json) =>
      AppNotification(
        id: json['idNotificacion'] as int,
        tipo: json['tipo'] as String? ?? '',
        mensaje: json['mensaje'] as String? ?? '',
        leido: json['leido'] == 1 || json['leido'] == true,
        fechaCreacion: json['fechaCreacion'] as String? ?? '',
      );

  final int id;
  final String tipo;
  final String mensaje;
  final bool leido;
  final String fechaCreacion;
}

class NotificationsRepository {
  NotificationsRepository(this._client);
  final ApiClient _client;

  Future<List<AppNotification>> fetchAll() async {
    final data = await _client
        .get('/notificaciones.php', query: {'action': 'list'});
    return (data['notificaciones'] as List)
        .cast<Map<String, dynamic>>()
        .map(AppNotification.fromJson)
        .toList();
  }

  Future<int> fetchUnreadCount() async {
    final data = await _client
        .get('/notificaciones.php', query: {'action': 'unread-count'});
    return data['count'] as int? ?? 0;
  }

  Future<void> markRead(List<int> ids) {
    return _client.post('/notificaciones.php',
        query: {'action': 'mark-read'}, data: {'ids': ids});
  }

  Future<void> markAllRead() {
    return _client.post('/notificaciones.php',
        query: {'action': 'mark-all-read'});
  }
}

final notificationsRepositoryProvider = Provider<NotificationsRepository>(
  (ref) => NotificationsRepository(ref.read(apiClientProvider)),
);

final notificationsListProvider =
    FutureProvider.autoDispose<List<AppNotification>>(
  (ref) => ref.read(notificationsRepositoryProvider).fetchAll(),
);

final unreadNotificationsCountProvider = FutureProvider.autoDispose<int>(
  (ref) => ref.read(notificationsRepositoryProvider).fetchUnreadCount(),
);
