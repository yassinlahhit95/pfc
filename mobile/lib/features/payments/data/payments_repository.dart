import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class Payment {
  const Payment({
    required this.id,
    required this.monto,
    required this.fechaPago,
    required this.tipoPago,
    required this.estadoComprobante,
    required this.nombreEstudiante,
    required this.nombreCiclo,
  });

  factory Payment.fromJson(Map<String, dynamic> json) => Payment(
        id: json['idPago'] as int,
        monto: json['monto'] as String? ?? '0',
        fechaPago: json['fechaPago'] as String? ?? '',
        tipoPago: json['tipoPago'] as String? ?? '',
        estadoComprobante: json['estadoComprobante'] as String? ?? '',
        nombreEstudiante: json['nombreEstudiante'] as String? ?? '',
        nombreCiclo: json['nombreCiclo'] as String? ?? '',
      );

  final int id;
  final String monto;
  final String fechaPago;
  final String tipoPago;
  final String estadoComprobante;
  final String nombreEstudiante;
  final String nombreCiclo;
}

class PendingPayment {
  const PendingPayment({
    required this.idEstudiante,
    required this.nombreEstudiante,
    required this.nombreCiclo,
    required this.deuda,
  });

  factory PendingPayment.fromJson(Map<String, dynamic> json) => PendingPayment(
        idEstudiante: json['idEstudiante'] as int,
        nombreEstudiante: json['nombreEstudiante'] as String? ?? '',
        nombreCiclo: json['nombreCiclo'] as String? ?? '',
        deuda: json['deuda']?.toString() ?? '0',
      );

  final int idEstudiante;
  final String nombreEstudiante;
  final String nombreCiclo;
  final String deuda;
}

class PaymentsRepository {
  PaymentsRepository(this._client);
  final ApiClient _client;

  Future<List<Payment>> fetchAll() async {
    final data = await _client.get('/payments.php');
    return (data['payments'] as List).cast<Map<String, dynamic>>().map(Payment.fromJson).toList();
  }

  Future<List<PendingPayment>> fetchPending() async {
    final data = await _client.get('/payments.php', query: {'pending': 1});
    return (data['pending'] as List).cast<Map<String, dynamic>>().map(PendingPayment.fromJson).toList();
  }
}

final paymentsRepositoryProvider = Provider<PaymentsRepository>(
  (ref) => PaymentsRepository(ref.read(apiClientProvider)),
);

final paymentsProvider = FutureProvider.autoDispose<List<Payment>>(
  (ref) => ref.read(paymentsRepositoryProvider).fetchAll(),
);

final pendingPaymentsProvider = FutureProvider.autoDispose<List<PendingPayment>>(
  (ref) => ref.read(paymentsRepositoryProvider).fetchPending(),
);
