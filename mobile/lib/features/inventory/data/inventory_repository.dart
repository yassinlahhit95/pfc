import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class Device {
  const Device({
    required this.id,
    required this.nombre,
    required this.numeroSerie,
    required this.estado,
  });

  factory Device.fromJson(Map<String, dynamic> json) => Device(
        id: json['idArticulo'] as int,
        nombre: json['nombreArticulo'] as String? ?? '',
        numeroSerie: json['numeroSerie'] as String? ?? '',
        estado: json['estado'] as String? ?? '',
      );

  final int id;
  final String nombre;
  final String numeroSerie;
  final String estado;
}

class Loan {
  const Loan({
    required this.id,
    required this.idArticulo,
    required this.nombreArticulo,
    required this.nombreEstudiante,
    required this.fechaPrestamo,
    required this.estadoPrestamo,
  });

  factory Loan.fromJson(Map<String, dynamic> json) => Loan(
        id: json['idPrestamo'] as int,
        idArticulo: json['idArticulo'] as int? ?? 0,
        nombreArticulo: json['nombreArticulo'] as String? ?? '',
        nombreEstudiante: json['nombreEstudiante'] as String? ?? '',
        fechaPrestamo: json['fechaPrestamo'] as String? ?? '',
        estadoPrestamo: json['estadoPrestamo'] as String? ?? '',
      );

  final int id;
  final int idArticulo;
  final String nombreArticulo;
  final String nombreEstudiante;
  final String fechaPrestamo;
  final String estadoPrestamo;
}

class InventoryRepository {
  InventoryRepository(this._client);
  final ApiClient _client;

  Future<List<Device>> fetchDevices() async {
    final data = await _client.get('/inventory.php', query: {'action': 'devices'});
    return (data['devices'] as List).cast<Map<String, dynamic>>().map(Device.fromJson).toList();
  }

  Future<List<Loan>> fetchLoans() async {
    final data = await _client.get('/inventory.php', query: {'action': 'loans'});
    return (data['loans'] as List).cast<Map<String, dynamic>>().map(Loan.fromJson).toList();
  }

  Future<void> prestar({required int idArticulo, required int idEstudiante}) {
    return _client.post('/inventory.php', data: {
      'action': 'prestar',
      'idArticulo': idArticulo,
      'idEstudiante': idEstudiante,
    });
  }

  Future<void> devolver(int idPrestamo) {
    return _client.post('/inventory.php', data: {'action': 'devolver', 'idPrestamo': idPrestamo});
  }
}

final inventoryRepositoryProvider = Provider<InventoryRepository>(
  (ref) => InventoryRepository(ref.read(apiClientProvider)),
);

final devicesProvider = FutureProvider.autoDispose<List<Device>>(
  (ref) => ref.read(inventoryRepositoryProvider).fetchDevices(),
);

final loansProvider = FutureProvider.autoDispose<List<Loan>>(
  (ref) => ref.read(inventoryRepositoryProvider).fetchLoans(),
);
