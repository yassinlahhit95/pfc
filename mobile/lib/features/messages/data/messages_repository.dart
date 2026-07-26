import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class MessageThread {
  const MessageThread({
    required this.id,
    required this.asunto,
    required this.descripcion,
    required this.fecha,
    required this.emisorRol,
    required this.leido,
    required this.nombreEstudiante,
    required this.nombreProfesor,
  });

  factory MessageThread.fromJson(Map<String, dynamic> json) => MessageThread(
        id: json['idReclamacion'] as int,
        asunto: json['asunto'] as String? ?? '',
        descripcion: json['descripcion'] as String? ?? '',
        fecha: json['fecha'] as String? ?? '',
        emisorRol: json['emisor_rol'] as String? ?? '',
        leido: json['leido'] == 1 || json['leido'] == true,
        nombreEstudiante: json['nombreEstudiante'] as String?,
        nombreProfesor: json['nombreProfesor'] as String?,
      );

  final int id;
  final String asunto;
  final String descripcion;
  final String fecha;
  final String emisorRol;
  final bool leido;
  final String? nombreEstudiante;
  final String? nombreProfesor;
}

class MessagesRepository {
  MessagesRepository(this._client);
  final ApiClient _client;

  Future<List<MessageThread>> fetchThreads() async {
    final data = await _client.get('/messages.php');
    return (data['messages'] as List).cast<Map<String, dynamic>>().map(MessageThread.fromJson).toList();
  }

  Future<List<MessageThread>> fetchThread(int id) async {
    final data = await _client.get('/messages.php', query: {'id': id});
    return (data['thread'] as List).cast<Map<String, dynamic>>().map(MessageThread.fromJson).toList();
  }

  Future<void> reply({required int idParent, required String contenido}) {
    return _client.post('/messages.php', data: {'id_parent': idParent, 'contenido': contenido});
  }

  Future<void> createThread({
    required String asunto,
    required String descripcion,
    int? idProfesor,
    int? idEstudiante,
  }) {
    return _client.post('/messages.php', data: {
      'asunto': asunto,
      'descripcion': descripcion,
      if (idProfesor != null) 'idProfesor': idProfesor,
      if (idEstudiante != null) 'idEstudiante': idEstudiante,
    });
  }

  Future<void> markRead(int id) {
    return _client.post('/messages.php', data: {'action': 'read', 'id': id});
  }

  Future<int> fetchUnread() async {
    final data = await _client.get('/messages-unread.php');
    return data['unread'] as int;
  }
}

final messagesRepositoryProvider = Provider<MessagesRepository>(
  (ref) => MessagesRepository(ref.read(apiClientProvider)),
);

final messageThreadsProvider = FutureProvider.autoDispose<List<MessageThread>>(
  (ref) => ref.read(messagesRepositoryProvider).fetchThreads(),
);

/// Polls the unread mensajería count for the bottom-nav badge (home_shell.dart) —
/// same pattern as chatUnreadCountProvider in chat_repository.dart.
final messagesUnreadCountProvider = StreamProvider.autoDispose<int>((ref) async* {
  final repo = ref.watch(messagesRepositoryProvider);
  while (true) {
    try {
      yield await repo.fetchUnread();
    } catch (_) {
      yield 0;
    }
    await Future.delayed(const Duration(seconds: 30));
  }
});
