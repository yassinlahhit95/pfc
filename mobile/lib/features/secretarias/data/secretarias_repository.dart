import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';
import '../../../core/utils/cache_extension.dart';

class Secretaria {
  const Secretaria({
    required this.id,
    required this.nombre,
    required this.email,
    required this.activo,
  });

  factory Secretaria.fromJson(Map<String, dynamic> json) => Secretaria(
        id: json['idSecretaria'] as int,
        nombre: json['nombreSecretaria'] as String? ?? '',
        email: json['emailSecretaria'] as String? ?? '',
        activo:
            json['activoSecretaria'] == 1 || json['activoSecretaria'] == true,
      );

  final int id;
  final String nombre;
  final String email;
  final bool activo;
}

class SecretariasRepository {
  SecretariasRepository(this._client);
  final ApiClient _client;

  Future<({List<Secretaria> secretarias, int total})> fetchSecretarias({
    int limit = 20,
    int offset = 0,
    String? query,
  }) async {
    final queryParams = {
      'limit': limit.toString(),
      'offset': offset.toString(),
      if (query != null && query.isNotEmpty) 'q': query,
    };

    final data = await _client.get('/secretarias.php', query: queryParams);
    return (
      secretarias: (data['secretarias'] as List)
          .cast<Map<String, dynamic>>()
          .map(Secretaria.fromJson)
          .toList(),
      total: data['total'] as int? ?? 0,
    );
  }

  Future<void> createSecretaria(Map<String, dynamic> data) async {
    await _client.post('/secretarias.php', data: data);
  }

  Future<void> updateSecretaria(Map<String, dynamic> data) async {
    await _client.put('/secretarias.php', data: data);
  }

  Future<void> deleteSecretaria(int id, String password) async {
    await _client.delete('/secretarias.php',
        query: {'id': id.toString()}, data: {'password': password});
  }
}

final secretariasRepositoryProvider = Provider<SecretariasRepository>(
  (ref) => SecretariasRepository(ref.read(apiClientProvider)),
);

class SecretariasNotifier
    extends AsyncNotifier<({List<Secretaria> secretarias, int total})> {
  SecretariasNotifier(this.arg);
  final String? arg;

  bool _isLoadingMore = false;

  @override
  Future<({List<Secretaria> secretarias, int total})> build() async {
    ref.cacheFor(const Duration(minutes: 5));
    return ref.read(secretariasRepositoryProvider).fetchSecretarias(
          limit: 20,
          offset: 0,
          query: arg,
        );
  }

  Future<void> loadMore() async {
    if (_isLoadingMore) return;
    final current = state.value;
    if (current == null) return;
    if (current.secretarias.length >= current.total) return;

    _isLoadingMore = true;
    try {
      final nextData =
          await ref.read(secretariasRepositoryProvider).fetchSecretarias(
                limit: 20,
                offset: current.secretarias.length,
                query: arg,
              );
      state = AsyncData((
        secretarias: [...current.secretarias, ...nextData.secretarias],
        total: nextData.total,
      ));
    } finally {
      _isLoadingMore = false;
    }
  }
}

final secretariasProvider = AsyncNotifierProvider.autoDispose.family<
    SecretariasNotifier, ({List<Secretaria> secretarias, int total}), String?>(
  SecretariasNotifier.new,
);
