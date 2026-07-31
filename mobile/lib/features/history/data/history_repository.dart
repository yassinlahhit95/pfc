import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/api/api_client.dart';

class HistoryItem {
  const HistoryItem({
    required this.role,
    required this.userName,
    required this.accion,
    required this.tabla,
    required this.descripcion,
    required this.fecha,
  });

  factory HistoryItem.fromJson(Map<String, dynamic> json) {
    return HistoryItem(
      role: json['role'] as String? ?? 'sistema',
      userName: json['user_name'] as String? ?? 'Sistema',
      accion: json['accion'] as String? ?? '',
      tabla: json['tabla'] as String? ?? '',
      descripcion: json['descripcion'] as String? ?? '',
      fecha: DateTime.parse(json['fecha'] as String),
    );
  }

  final String role;
  final String userName;
  final String accion;
  final String tabla;
  final String descripcion;
  final DateTime fecha;
}

class HistoryRepository {
  HistoryRepository(this._client);
  final ApiClient _client;

  Future<List<HistoryItem>> fetchHistory(String dateStr) async {
    final data = await _client.get('/history.php', query: {'date': dateStr});
    final rows = (data['history'] as List).cast<Map<String, dynamic>>();
    return rows.map(HistoryItem.fromJson).toList();
  }
}

final historyRepositoryProvider = Provider<HistoryRepository>(
  (ref) => HistoryRepository(ref.read(apiClientProvider)),
);

final historyProvider = FutureProvider.autoDispose.family<List<HistoryItem>, String>(
  (ref, dateStr) => ref.read(historyRepositoryProvider).fetchHistory(dateStr),
);
