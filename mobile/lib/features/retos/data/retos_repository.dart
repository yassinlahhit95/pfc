import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';
import '../../../core/auth/auth_state.dart';

class Reto {
  const Reto({
    required this.id,
    required this.idModulo,
    required this.titulo,
    required this.descripcion,
    required this.nombreModulo,
    required this.nombreProfesor,
    required this.fechaCreacion,
    this.archivoAdjunto,
    this.publicado = true,
    this.entregado = false,
    this.nota,
    this.fechaEntrega,
  });

  factory Reto.fromJson(Map<String, dynamic> json) {
    return Reto(
      id: json['idReto'] as int,
      idModulo: json['idModulo'] as int,
      titulo: json['titulo'] as String? ?? '',
      descripcion: json['descripcion'] as String? ?? '',
      nombreModulo: json['nombreModulo'] as String? ?? '',
      nombreProfesor: json['nombreProfesor'] as String? ?? '',
      fechaCreacion: json['fechaCreacion'] as String? ?? '',
      archivoAdjunto: json['archivoAdjunto'] as String?,
      publicado: (json['publicado'] as int? ?? 1) == 1,
      entregado: json['entregado'] == true,
      nota: json['nota']?.toString(),
      fechaEntrega: json['fechaEntrega'] as String?,
    );
  }

  final int id;
  final int idModulo;
  final String titulo;
  final String descripcion;
  final String nombreModulo;
  final String nombreProfesor;
  final String fechaCreacion;
  final String? archivoAdjunto;
  final bool publicado;
  final bool entregado;
  final String? nota;
  final String? fechaEntrega;
}

class RetosRepository {
  RetosRepository(this._client, this._token);
  final ApiClient _client;
  final String? _token;

  Future<List<Reto>> fetchRetos() async {
    final data = await _client.get('/retos.php', query: {
      'action': 'list',
    });
    return (data['retos'] as List)
        .cast<Map<String, dynamic>>()
        .map(Reto.fromJson)
        .toList();
  }

  String downloadUrl(int idReto) {
    return '$apiBaseUrl/api/v1/retos.php?action=download&kind=reto&id=$idReto&token=$_token';
  }
}

final retosRepositoryProvider = Provider<RetosRepository>((ref) {
  final token = ref.watch(sessionControllerProvider).valueOrNull?.token;
  return RetosRepository(ref.read(apiClientProvider), token);
});

final retosProvider = FutureProvider.autoDispose<List<Reto>>(
  (ref) => ref.read(retosRepositoryProvider).fetchRetos(),
);
