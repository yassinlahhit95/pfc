import 'dart:io';

import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';
import '../../../core/auth/auth_state.dart';

class CategoriaGasto {
  const CategoriaGasto({
    required this.idCategoria,
    required this.nombre,
    required this.color,
  });

  factory CategoriaGasto.fromJson(Map<String, dynamic> json) {
    return CategoriaGasto(
      idCategoria: json['idCategoria'] as int,
      nombre: json['nombre'] as String? ?? '',
      color: json['color'] as String? ?? '#808080',
    );
  }

  final int idCategoria;
  final String nombre;
  final String color;
}

class Gasto {
  const Gasto({
    required this.idGasto,
    required this.concepto,
    required this.importe,
    required this.fecha,
    required this.nombreCategoria,
    required this.colorCategoria,
    required this.archivoJustificante,
  });

  factory Gasto.fromJson(Map<String, dynamic> json) {
    return Gasto(
      idGasto: json['idGasto'] as int,
      concepto: json['concepto'] as String? ?? '',
      importe: double.tryParse(json['importe']?.toString() ?? '0') ?? 0.0,
      fecha: json['fecha'] as String? ?? '',
      nombreCategoria: json['nombreCategoria'] as String? ?? 'Desconocida',
      colorCategoria: json['color'] as String? ?? '#808080',
      archivoJustificante: json['archivoJustificante'] as String?,
    );
  }

  final int idGasto;
  final String concepto;
  final double importe;
  final String fecha;
  final String nombreCategoria;
  final String colorCategoria;
  final String? archivoJustificante;
}

class GastosRepository {
  GastosRepository(this._client, this._token);
  final ApiClient _client;
  final String? _token;

  Future<({List<Gasto> gastos, List<CategoriaGasto> categorias})> fetchGastos() async {
    final data = await _client.get('/gastos.php', query: {'action': 'list'});
    return (
      gastos: (data['gastos'] as List).cast<Map<String, dynamic>>().map(Gasto.fromJson).toList(),
      categorias: (data['categorias'] as List).cast<Map<String, dynamic>>().map(CategoriaGasto.fromJson).toList(),
    );
  }

  Future<void> registrarGasto({
    required int idCategoria,
    required String concepto,
    required double importe,
    required String fecha,
    File? archivo,
  }) async {
    final Map<String, dynamic> body = {
      'idCategoria': idCategoria,
      'concepto': concepto,
      'importe': importe,
      'fecha': fecha,
    };

    if (archivo != null) {
      body['archivo'] = await MultipartFile.fromFile(archivo.path);
    }
    await _client.post('/gastos.php', query: {'action': 'create'}, data: FormData.fromMap(body));
  }

  String downloadUrl(String filename) {
    // Some filenames might be JSON array stringified
    String realFilename = filename;
    if (filename.startsWith('[')) {
      realFilename = filename.replaceAll('"', '').replaceAll('[', '').replaceAll(']', '');
    }
    return '$apiBaseUrl/public/uploads/justificantes/$realFilename';
  }
}

final gastosRepositoryProvider = Provider<GastosRepository>((ref) {
  final token = ref.watch(sessionControllerProvider).valueOrNull?.token;
  return GastosRepository(ref.read(apiClientProvider), token);
});

final gastosListProvider = FutureProvider.autoDispose<({List<Gasto> gastos, List<CategoriaGasto> categorias})>(
  (ref) => ref.read(gastosRepositoryProvider).fetchGastos(),
);
