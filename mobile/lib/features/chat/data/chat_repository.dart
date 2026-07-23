import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class ChatContact {
  const ChatContact({required this.uid, required this.nombre, required this.rol});

  factory ChatContact.fromJson(Map<String, dynamic> json) => ChatContact(
        uid: json['uid'] as int,
        nombre: json['nombre'] as String? ?? '',
        rol: json['rol'] as String? ?? '',
      );

  final int uid;
  final String nombre;
  final String rol;
}

class ChatConversation {
  const ChatConversation({
    required this.id,
    required this.otherRol,
    required this.otherId,
    required this.otherNombre,
    required this.lastPreview,
    required this.lastMessageAt,
    required this.unreadCount,
  });

  factory ChatConversation.fromJson(Map<String, dynamic> json) => ChatConversation(
        id: json['id'] as int,
        otherRol: json['other_rol'] as String? ?? '',
        otherId: json['other_id'] as int? ?? 0,
        otherNombre: json['other_nombre'] as String? ?? '?',
        lastPreview: json['last_preview'] as String?,
        lastMessageAt: json['last_message_at'] as String?,
        unreadCount: json['unread_count'] as int? ?? 0,
      );

  final int id;
  final String otherRol;
  final int otherId;
  final String otherNombre;
  final String? lastPreview;
  final String? lastMessageAt;
  final int unreadCount;
}

class ChatMessage {
  const ChatMessage({
    required this.id,
    required this.emisorRol,
    required this.emisorId,
    required this.emisorNombre,
    required this.contenido,
    required this.fecha,
    required this.leido,
  });

  factory ChatMessage.fromJson(Map<String, dynamic> json) => ChatMessage(
        id: json['id'] as int,
        emisorRol: json['emisor_rol'] as String? ?? '',
        emisorId: json['emisor_id'] as int? ?? 0,
        emisorNombre: json['emisor_nombre'] as String? ?? '?',
        contenido: json['contenido'] as String? ?? '',
        fecha: json['fecha'] as String? ?? '',
        leido: json['leido'] == 1 || json['leido'] == true,
      );

  final int id;
  final String emisorRol;
  final int emisorId;
  final String emisorNombre;
  final String contenido;
  final String fecha;
  final bool leido;
}

class ChatRepository {
  ChatRepository(this._client);
  final ApiClient _client;

  Future<List<ChatContact>> fetchContacts({String query = ''}) async {
    final data = await _client.get('/chat.php', query: {'action': 'contacts', 'q': query});
    return (data['contacts'] as List).cast<Map<String, dynamic>>().map(ChatContact.fromJson).toList();
  }

  Future<List<ChatConversation>> fetchConversations() async {
    final data = await _client.get('/chat.php', query: {'action': 'conversations'});
    return (data['conversations'] as List).cast<Map<String, dynamic>>().map(ChatConversation.fromJson).toList();
  }

  Future<List<ChatMessage>> fetchMessages(int convId, {int? after}) async {
    final data = await _client.get('/chat.php', query: {
      'action': 'messages',
      'conv_id': convId,
      if (after != null) 'after': after,
    });
    return (data['messages'] as List).cast<Map<String, dynamic>>().map(ChatMessage.fromJson).toList();
  }

  Future<int> startConversation({required String targetRol, required int targetId}) async {
    final data = await _client.post('/chat.php', data: {
      'action': 'start',
      'target_rol': targetRol,
      'target_id': targetId,
    });
    return data['conv_id'] as int;
  }

  Future<void> sendMessage({required int convId, required String contenido}) {
    return _client.post('/chat.php', data: {
      'action': 'send',
      'conv_id': convId,
      'contenido': contenido,
    });
  }

  Future<int> fetchUnread() async {
    final data = await _client.get('/chat.php', query: {'action': 'unread'});
    return data['unread'] as int;
  }
}

final chatRepositoryProvider = Provider<ChatRepository>(
  (ref) => ChatRepository(ref.read(apiClientProvider)),
);

final chatConversationsProvider = FutureProvider.autoDispose<List<ChatConversation>>(
  (ref) => ref.read(chatRepositoryProvider).fetchConversations(),
);
