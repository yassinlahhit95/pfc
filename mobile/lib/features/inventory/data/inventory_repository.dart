import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class Device {
  const Device({
    required this.id,
    required this.nombre,
    required this.numeroSerie,
    required this.estado,
    this.foto,
  });

  factory Device.fromJson(Map<String, dynamic> json) => Device(
        id: json['idArticulo'] as int,
        nombre: json['nombreArticulo'] as String? ?? '',
        numeroSerie: json['numeroSerie'] as String? ?? '',
        estado: json['estado'] as String? ?? '',
        foto: json['foto'] as String?,
      );

  final int id;
  final String nombre;
  final String numeroSerie;
  final String estado;
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

class InventoryItem {
  const InventoryItem({
    required this.id,
    required this.nombre,
    required this.descripcion,
    required this.cantidad,
  });

  factory InventoryItem.fromJson(Map<String, dynamic> json) => InventoryItem(
        id: json['idInventario'] as int,
        nombre: json['nombreArticulo'] as String? ?? '',
        descripcion: json['descripcion'] as String?,
        cantidad: json['cantidad'] as int? ?? 0,
      );

  final int id;
  final String nombre;
  final String? descripcion;
  final int cantidad;
}

class InventoryRepository {
  InventoryRepository(this._client);
  final ApiClient _client;

  // Device loans (dispositivos)
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

  Future<void> addDevice({
    required String nombreArticulo,
    required String numeroSerie,
    String? fotoBase64,
  }) {
    return _client.post('/inventory.php', data: {
      'action': 'add_device',
      'nombreArticulo': nombreArticulo,
      'numeroSerie': numeroSerie,
      if (fotoBase64 != null) 'fotoBase64': fotoBase64,
    });
  }

  Future<void> editDevice({
    required int idArticulo,
    required String nombreArticulo,
    required String numeroSerie,
    required String estado,
    String? fotoBase64,
  }) {
    return _client.post('/inventory.php', data: {
      'action': 'edit_device',
      'idArticulo': idArticulo,
      'nombreArticulo': nombreArticulo,
      'numeroSerie': numeroSerie,
      'estado': estado,
      if (fotoBase64 != null) 'fotoBase64': fotoBase64,
    });
  }

  Future<void> deleteDevice(int idArticulo) {
    return _client.post('/inventory.php', data: {
      'action': 'delete_device',
      'idArticulo': idArticulo,
    });
  }

  // Generic inventory items CRUD
  Future<List<InventoryItem>> fetchItems({int limit = 100, int offset = 0}) async {
    final data = await _client.get('/inventory.php', query: {
      'action': 'items',
      'limit': limit,
      'offset': offset,
    });
    return (data['items'] as List).cast<Map<String, dynamic>>().map(InventoryItem.fromJson).toList();
  }

  Future<InventoryItem> fetchItem(int id) async {
    final data = await _client.get('/inventory.php', query: {'action': 'item', 'id': id});
    return InventoryItem.fromJson((data['item'] as Map).cast<String, dynamic>());
  }

  Future<int> createItem({
    required String nombre,
    String? descripcion,
    int cantidad = 0,
  }) async {
    final data = await _client.post('/inventory.php', data: {
      'action': 'create_item',
      'nombreArticulo': nombre,
      if (descripcion != null) 'descripcion': descripcion,
      'cantidad': cantidad,
    });
    return data['id'] as int;
  }

  Future<void> updateItem({
    required int id,
    required String nombre,
    String? descripcion,
    int? cantidad,
  }) {
    return _client.post('/inventory.php', data: {
      'action': 'update_item',
      'idInventario': id,
      'nombreArticulo': nombre,
      if (descripcion != null) 'descripcion': descripcion,
      if (cantidad != null) 'cantidad': cantidad,
    });
  }

  Future<void> deleteItem(int id) {
    return _client.post('/inventory.php', data: {
      'action': 'delete_item',
      'idInventario': id,
    });
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

final inventoryItemsProvider = FutureProvider.autoDispose.family<InventoryItem, int>(
  (ref, id) => ref.read(inventoryRepositoryProvider).fetchItem(id),
);

final inventoryItemsListProvider = FutureProvider.autoDispose<List<InventoryItem>>(
  (ref) => ref.read(inventoryRepositoryProvider).fetchItems(),
);
