import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class Announcement {
  const Announcement({
    required this.id,
    required this.titulo,
    required this.mensaje,
    required this.fecha,
  });

  factory Announcement.fromJson(Map<String, dynamic> json) => Announcement(
        id: json['idAnuncio'] as int,
        titulo: json['titulo'] as String? ?? '',
        mensaje: json['mensaje'] as String? ?? '',
        fecha: json['fechaAnuncio'] as String? ?? '',
      );

  final int id;
  final String titulo;
  final String mensaje;
  final String fecha;
}

class AnnouncementsRepository {
  AnnouncementsRepository(this._client);
  final ApiClient _client;

  Future<List<Announcement>> fetchAnnouncements({
    int limit = 20,
    int offset = 0,
  }) async {
    final data = await _client.get('/announcements.php', query: {
      'limit': limit,
      'offset': offset,
    });
    final rows = (data['announcements'] as List).cast<Map<String, dynamic>>();
    return rows.map(Announcement.fromJson).toList();
  }
}

final announcementsRepositoryProvider = Provider<AnnouncementsRepository>(
  (ref) => AnnouncementsRepository(ref.read(apiClientProvider)),
);

final announcementsProvider = FutureProvider.autoDispose<List<Announcement>>(
  (ref) => ref.read(announcementsRepositoryProvider).fetchAnnouncements(),
);
