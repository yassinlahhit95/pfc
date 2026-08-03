import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class Device {
  const Device({
    required this.id,
    required this.nombre,
    required this.numeroSerie,
    required this.estado,
    required this.cantidad,
    this.foto,
  });

  factory Device.fromJson(Map<String, dynamic> json) => Device(
        id: json['idArticulo'] as int,
        nombre: json['nombreArticulo'] as String? ?? '',
        numeroSerie: json['numeroSerie'] as String? ?? '',
        estado: json['estado'] as String? ?? '',
        cantidad: json['cantidad'] as int? ?? 1,
        foto: json['foto'] as String?,
      );

  final int id;
  final String nombre;
  final String numeroSerie;
  final String estado;
  final int cantidad;
  final String? foto;
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

// Removed InventoryItem

class InventoryRepository {
  InventoryRepository(this._client);
  final ApiClient _client;

  // Device loans (dispositivos)
  Future<List<Device>> fetchDevices() async {
    final data =
        await _client.get('/inventory.php', query: {'action': 'devices'});
    return (data['devices'] as List)
        .cast<Map<String, dynamic>>()
        .map(Device.fromJson)
        .toList();
  }

  Future<List<Loan>> fetchLoans() async {
    final data =
        await _client.get('/inventory.php', query: {'action': 'loans'});
    return (data['loans'] as List)
        .cast<Map<String, dynamic>>()
        .map(Loan.fromJson)
        .toList();
  }

  Future<void> prestar({required int idArticulo, required int idEstudiante}) {
    return _client.post('/inventory.php', data: {
      'action': 'prestar',
      'idArticulo': idArticulo,
      'idEstudiante': idEstudiante,
    });
  }

  Future<void> devolver(int idPrestamo) {
    return _client.post('/inventory.php',
        data: {'action': 'devolver', 'idPrestamo': idPrestamo});
  }

  Future<void> addDevice({
    required String nombreArticulo,
    required String numeroSerie,
    required int cantidad,
    String? fotoBase64,
  }) {
    return _client.post('/inventory.php', data: {
      'action': 'add_device',
      'nombreArticulo': nombreArticulo,
      'numeroSerie': numeroSerie,
      'cantidad': cantidad,
      if (fotoBase64 != null) 'fotoBase64': fotoBase64,
    });
  }

  Future<void> editDevice({
    required int idArticulo,
    required String nombreArticulo,
    required String numeroSerie,
    required String estado,
    required int cantidad,
    String? fotoBase64,
  }) {
    return _client.post('/inventory.php', data: {
      'action': 'edit_device',
      'idArticulo': idArticulo,
      'nombreArticulo': nombreArticulo,
      'numeroSerie': numeroSerie,
      'estado': estado,
      'cantidad': cantidad,
      if (fotoBase64 != null) 'fotoBase64': fotoBase64,
    });
  }

  Future<void> deleteDevice(int idArticulo) {
    return _client.post('/inventory.php', data: {
      'action': 'delete_device',
      'idArticulo': idArticulo,
    });
  }

  // Removed generic item methods
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

// Removed generic item providers
